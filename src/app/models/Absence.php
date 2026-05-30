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
                "INSERT INTO absences (teacher_id, reason, date, proof_file_path, is_justified)
                 VALUES (?, ?, ?, ?, ?)",
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

            $absenceId = (int) $absenceRes['lastInsertId'];
            $createdPeriods = 0;
            $createdSubstitutions = 0;

            foreach ($periodIds as $periodId) {
                $materialUrl = $materialsByPeriod[$periodId] ?? null;

                $periodRes = $this->insert(
                    "INSERT INTO absence_period
                        (absence_id, period_id, instruction_material_url, comments, is_cover_generated)
                     VALUES (?, ?, ?, ?, TRUE)",
                    [
                        $absenceId,
                        $periodId,
                        $materialUrl,
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
                            (int) $schedule['id'],
                            $teacherId,
                            (int) $schedule['class_id'],
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
                'success' => true,
                'absence_id' => $absenceId,
                'periods' => $createdPeriods,
                'substitutions' => $createdSubstitutions,
            ];
        } catch (Throwable $e) {
            $this->rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
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

    public function getAbsencesByTeacher(int $teacherId): array
    {
        $sql = "SELECT
                    a.id,
                    a.reason,
                    a.date,
                    a.proof_file_path,
                    a.is_justified,
                    a.created_at,
                    GROUP_CONCAT(
                        DISTINCT p.name
                        ORDER BY p.start_time
                        SEPARATOR ', '
                    ) AS periods,
                    COUNT(DISTINCT ap.id) AS affected_periods,
                    SUM(CASE WHEN s.status = 'PENDIENTE' THEN 1 ELSE 0 END) AS pending_substitutions,
                    SUM(CASE WHEN s.status = 'CONFIRMADO' THEN 1 ELSE 0 END) AS confirmed_substitutions,
                    SUM(CASE WHEN s.status = 'CANCELADO' THEN 1 ELSE 0 END) AS cancelled_substitutions
                FROM absences a
                LEFT JOIN absence_period ap ON ap.absence_id = a.id
                LEFT JOIN periods p ON ap.period_id = p.id
                LEFT JOIN substitutions s ON s.absence_detail_id = ap.id AND s.enabled = 1
                WHERE a.teacher_id = ?
                GROUP BY a.id, a.reason, a.date, a.proof_file_path, a.is_justified, a.created_at
                ORDER BY a.date DESC, a.created_at DESC";

        $res = $this->query($sql, [$teacherId]);
        return $res['success'] ? $res['data'] : [];
    }
    
    public function countAbsencesByDate(string $date) : int
    {
        $res = $this->query("SELECT COUNT(*) as count FROM absences WHERE date = ?", [$date]);
        return $res['success'] ? (int)$res['data'][0]['count'] : 0;
    }
    public function getTeacherAbsencesToday(string $date) : array
    {
        $res = $this->query("SELECT t.full_name ,t.profile_img_path
                            FROM absences a JOIN teachers t ON a.teacher_id = t.id
                            WHERE DATE = ?", [$date]);
        return $res['success'] ? $res['data'] : [];
    }
    public function getAbsencesHistory() : array
    {
        $res = $this->query("SELECT
                                ab.id,
                                ta.full_name AS absent,
                                COUNT(s.id) AS absent_hours,
                                ab.is_justified AS justify,
                                ab.date AS date_absence,
                                ab.reason AS reason,
                                GROUP_CONCAT(DISTINCT ts.full_name ORDER BY ts.full_name SEPARATOR ', ') AS sustitute
                            FROM substitutions s
                            JOIN teachers ta ON s.absent_teacher_id = ta.id
                            JOIN absence_period a ON s.absence_detail_id = a.id
                            JOIN absences ab ON a.absence_id = ab.id
                            LEFT JOIN teachers ts ON s.substitute_teacher_id = ts.id
                            WHERE s.enabled=1
                            GROUP BY ab.id, ta.full_name, ab.is_justified, ab.date, ab.reason");
        return $res['success'] ? $res['data'] : [];
    }

    public function getAbsencesDetails() : array
    {
        $res = $this->query("SELECT 
                                s.id,
                                c.name AS class, 
                                ta.full_name AS absent, 
                                p.name AS name_hour, 
                                ts.full_name AS substitute, 
                                s.`status` AS state, 
                                ab.date AS date_absence,
                                ab.reason AS reason,
                                ab.proof_file_path AS material
                            FROM substitutions s 
                            JOIN classes c ON s.class_id = c.id
                            JOIN teachers ta ON s.absent_teacher_id = ta.id
                            LEFT JOIN teachers ts ON s.substitute_teacher_id = ts.id 
                            JOIN absence_period a ON s.absence_detail_id = a.id
                            JOIN schedules sc ON s.schedule_id = sc.id
                            JOIN periods p ON sc.period_id = p.id
                            LEFT JOIN absences ab ON a.absence_id = ab.id
                            WHERE s.enabled = 1 
                            AND s.status IN ('PENDIENTE', 'CONFIRMADO');"); 
        return $res['success'] ? $res['data'] : [];
    }
    public function getAbsencesDetailsById(int $id){
        $res = $this->query("SELECT
                            s.id,
                            c.name AS class,
                            ta.full_name AS absent,
                            p.name AS name_hour,
                            ab.is_justified AS justify,
                            s.`status` AS state,
                            ab.date AS date_absence,
                            ab.reason AS reason,
                            ab.proof_file_path AS material
                            FROM substitutions s
                            JOIN classes c ON s.class_id = c.id
                            JOIN teachers ta ON s.absent_teacher_id = ta.id
                            JOIN absence_period a ON s.absence_detail_id = a.id
                            JOIN schedules sc ON s.schedule_id = sc.id
                            JOIN periods p ON sc.period_id = p.id
                            LEFT JOIN absences ab ON a.absence_id = ab.id
                            WHERE s.id = ?", [$id]);

        return $res['success'] ? $res['data'] : [];
    }
}
