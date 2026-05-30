<?php
final class Absence extends Model
{
    public function createTeacherAbsence(
        int $teacherId,
        string $date,
        string $reason,
        array $periodIds,
        ?string $proofFilePath = null,
        array $materialsByPeriod = [],
        ?string $notes = null
    ): array {
        $periodIds = array_values(array_unique(array_map('intval', $periodIds)));

        if ($teacherId <= 0 || $date === '' || $reason === '' || empty($periodIds)) {
            return ['success' => false, 'message' => 'Datos de ausencia incompletos.'];
        }

        try {
            $this->beginTransaction();

            $absenceRes = $this->insert(
                "INSERT INTO absences (teacher_id, reason, date, proof_file_path, is_justified) VALUES (?, ?, ?, ?, ?)",
                [
                    $teacherId,
                    $reason,
                    $date,
                    $proofFilePath,
                    $proofFilePath ? 1 : 0,
                ]
            );

            if (!$absenceRes['success']) {
                throw new RuntimeException($absenceRes['message'] ?? 'No se pudo registrar la ausencia.');
            }

            $absenceId            = (int) $absenceRes['lastInsertId'];
            $createdPeriods       = 0;
            $createdSubstitutions = 0;

            foreach ($periodIds as $periodId) {
                $periodRes = $this->insert(
                    "INSERT INTO absence_period
                        (absence_id, period_id, instruction_material_url, comments, is_cover_generated) VALUES (?, ?, ?, ?, TRUE)",
                    [
                        $absenceId,
                        $periodId,
                        $materialsByPeriod[$periodId] ?? null,
                        $notes ?: null,
                    ]
                );

                if (!$periodRes['success']) {
                    throw new RuntimeException($periodRes['message'] ?? 'No se pudo registrar una hora afectada.');
                }

                $absencePeriodId = (int) $periodRes['lastInsertId'];
                $createdPeriods++;

                $scheduleRes = $this->query(
                    "SELECT id, class_id
                     FROM schedules
                     WHERE teacher_id = ?
                        AND period_id = ?
                        AND day = (
                            CASE DAYOFWEEK(?)
                                WHEN 2 THEN 'L'
                                WHEN 3 THEN 'M'
                                WHEN 4 THEN 'X'
                                WHEN 5 THEN 'J'
                                WHEN 6 THEN 'V'
                            END
                        )
                        AND class_id IS NOT NULL",
                    [$teacherId, $periodId, $date]
                );

                if (!$scheduleRes['success']) {
                    throw new RuntimeException($scheduleRes['message'] ?? 'No se pudo consultar el horario.');
                }

                foreach ($scheduleRes['data'] as $schedule) {
                    $substitutionRes = $this->insert(
                        "INSERT INTO substitutions
                            (absence_detail_id, schedule_id, absent_teacher_id, class_id, date)
                         VALUES (?, ?, ?, ?, ?)",
                        [
                            $absencePeriodId,
                            $schedule['id'],
                            $teacherId,
                            $schedule['class_id'],
                            $date,
                        ]
                    );

                    if (!$substitutionRes['success']) {
                        throw new RuntimeException($substitutionRes['message'] ?? 'No se pudo generar la guardia.');
                    }

                    $createdSubstitutions++;
                }
            }

            $this->commit();

            return [
                'success'       => true,
                'absence_id'    => $absenceId,
                'periods'       => $createdPeriods,
                'substitutions' => $createdSubstitutions,
            ];
        } catch (Throwable $e) {
            $this->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getAbsences() : array
    {
        $res = $this->all('absences');
        return $res['data'] ?? [];
    }

    public function getAbsence(int $id) : array
    {
        $res = $this->find('absences', $id);
        return $res['success'] ? $res['data'] : [];
    }

    public function getAbsencesByTeacher(int $teacherId) : array
    {
        $sql = "SELECT
                    absences.id,
                    absences.reason,
                    absences.date,
                    absences.proof_file_path,
                    absences.is_justified,
                    absences.created_at,
                    GROUP_CONCAT(
                        DISTINCT periods.name
                        ORDER BY periods.start_time
                        SEPARATOR ', '
                    ) AS periods,
                    COUNT(DISTINCT absence_period.id) AS affected_periods,
                    SUM(CASE WHEN substitutions.status = 'PENDIENTE'  THEN 1 ELSE 0 END) AS pending_substitutions,
                    SUM(CASE WHEN substitutions.status = 'CONFIRMADO' THEN 1 ELSE 0 END) AS confirmed_substitutions,
                    SUM(CASE WHEN substitutions.status = 'CANCELADO'  THEN 1 ELSE 0 END) AS cancelled_substitutions
                FROM absences
                LEFT JOIN absence_period ON absence_period.absence_id = absences.id
                LEFT JOIN periods ON absence_period.period_id = periods.id
                LEFT JOIN substitutions ON substitutions.absence_detail_id = absence_period.id AND substitutions.enabled = 1
                WHERE absences.teacher_id = ?
                GROUP BY absences.id, absences.reason, absences.date, absences.proof_file_path, absences.is_justified, absences.created_at
                ORDER BY absences.date DESC, absences.created_at DESC";

        $res = $this->query($sql, [$teacherId]);
        return $res['success'] ? $res['data'] : [];
    }

    public function countAbsencesByDate(string $date) : int
    {
        $res = $this->query("SELECT COUNT(*) AS count FROM absences WHERE date = ?", [$date]);
        return $res['success'] ? (int) $res['data'][0]['count'] : 0;
    }

    public function getTeacherAbsencesToday(string $date) : array
    {
        $sql = "SELECT
                    teachers.full_name,
                    teachers.profile_img_path
                FROM absences
                JOIN teachers ON absences.teacher_id = teachers.id
                WHERE absences.date = ?";

        $res = $this->query($sql, [$date]);
        return $res['success'] ? $res['data'] : [];
    }

    public function getAbsencesHistory() : array
    {
        $sql = "SELECT
                    absences.id,
                    absent_teacher.full_name AS absent,
                    COUNT(substitutions.id) AS absent_hours,
                    absences.is_justified AS justify,
                    absences.date AS date_absence,
                    absences.reason,
                    GROUP_CONCAT(DISTINCT substitute_teacher.full_name ORDER BY substitute_teacher.full_name SEPARATOR ', ') AS sustitute
                FROM substitutions
                JOIN teachers AS absent_teacher          ON substitutions.absent_teacher_id = absent_teacher.id
                JOIN absence_period                      ON substitutions.absence_detail_id = absence_period.id
                JOIN absences                            ON absence_period.absence_id = absences.id
                LEFT JOIN teachers AS substitute_teacher ON substitutions.substitute_teacher_id = substitute_teacher.id
                WHERE substitutions.enabled = 1
                GROUP BY absences.id, absent_teacher.full_name, absences.is_justified, absences.date, absences.reason";

        $res = $this->query($sql);
        return $res['success'] ? $res['data'] : [];
    }

    public function getAbsencesDetails() : array
    {
        $sql = "SELECT
                    substitutions.id,
                    classes.name AS class,
                    absent_teacher.full_name AS absent,
                    periods.name AS name_hour,
                    substitute_teacher.full_name AS substitute,
                    substitutions.status AS state,
                    absences.date AS date_absence,
                    absences.reason,
                    absences.proof_file_path AS material
                FROM substitutions
                JOIN classes ON substitutions.class_id = classes.id
                JOIN teachers AS absent_teacher ON substitutions.absent_teacher_id = absent_teacher.id
                LEFT JOIN teachers AS substitute_teacher ON substitutions.substitute_teacher_id = substitute_teacher.id
                JOIN absence_period ON substitutions.absence_detail_id = absence_period.id
                JOIN schedules ON substitutions.schedule_id = schedules.id
                JOIN periods ON schedules.period_id = periods.id
                JOIN absences ON absence_period.absence_id = absences.id
                WHERE substitutions.enabled = 1
                    AND substitutions.status IN ('PENDIENTE', 'CONFIRMADO')";

        $res = $this->query($sql);
        return $res['success'] ? $res['data'] : [];
    }

    public function getAbsencesDetailsById(int $id) : array
    {
        $sql = "SELECT
                    substitutions.id,
                    classes.name AS class,
                    teachers.full_name AS absent,
                    periods.name AS name_hour,
                    absences.is_justified AS justify,
                    substitutions.status AS state,
                    absences.date AS date_absence,
                    absences.reason,
                    absences.proof_file_path AS material
                FROM substitutions
                JOIN classes        ON substitutions.class_id = classes.id
                JOIN teachers       ON substitutions.absent_teacher_id = teachers.id
                JOIN absence_period ON substitutions.absence_detail_id = absence_period.id
                JOIN schedules      ON substitutions.schedule_id = schedules.id
                JOIN periods        ON schedules.period_id = periods.id
                JOIN absences       ON absence_period.absence_id = absences.id
                WHERE substitutions.id = ?";

        $res = $this->query($sql, [$id]);
        return $res['success'] ? $res['data'] : [];
    }
}
