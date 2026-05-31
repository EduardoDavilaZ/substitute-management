<?php

use App\Services\TemplateService;

final class Schedule extends Model
{
    public function getGuardSchedules() : array
    {
        $sql = "SELECT s.id AS schedule_id, s.day, s.period_id, t.id, t.full_name, t.substitution_counter AS count
                FROM schedules s
                LEFT JOIN teachers t ON s.teacher_id = t.id
                WHERE s.class_id IS NULL";
        
        $res = $this->query($sql);
        return $res['success'] ? $res['data'] : [];
    }

    public function getSchedule(int $id): array 
    {
        $res = $this->find('schedules', $id); 
        if (!$res['success'] || empty($res['data'])) {
            return [];
        }
        return isset($res['data'][0]) ? $res['data'][0] : $res['data'];
    }

    public function getTeacherSchedule(int $teacherId): array
    {
        $sql = "SELECT
                    s.id,
                    s.day,
                    s.period_id,
                    p.name AS period_name,
                    p.start_time,
                    p.end_time,
                    c.code AS class_code,
                    c.name AS class_name,
                    c.stage AS class_stage
                FROM schedules s
                JOIN periods p ON s.period_id = p.id
                LEFT JOIN classes c ON s.class_id = c.id
                WHERE s.teacher_id = ?
                ORDER BY p.start_time ASC,
                    FIELD(s.day, 'L', 'M', 'X', 'J', 'V')";

        $res = $this->query($sql, [$teacherId]);
        return $res['success'] ? $res['data'] : [];
    }

    public function countGuardHoursByTeacher(int $teacherId, int $excludeScheduleId = 0): int
    {
        $sql = "SELECT COUNT(*) AS count FROM schedules
                WHERE teacher_id = ? AND class_id IS NULL";
        $params = [$teacherId];

        if ($excludeScheduleId > 0) {
            $sql .= " AND id != ?";
            $params[] = $excludeScheduleId;
        }

        $res = $this->query($sql, $params, false);
        return $res['success'] ? (int) ($res['data']['count'] ?? 0) : 0;
    }

    public function getGuardHourLimit(int $teacherId): int
    {
        $teacher = (new Teacher())->getTeacher($teacherId);
        if (empty($teacher)) {
            return 0;
        }

        return ((int) ($teacher['is_tutor'] ?? 0)) === 1 ? 1 : 2;
    }

    public function validateGuardAssignment(int $teacherId, int $excludeScheduleId = 0): ?string
    {
        $max = $this->getGuardHourLimit($teacherId);
        if ($max === 0) {
            return 'Profesor no encontrado.';
        }

        $current = $this->countGuardHoursByTeacher($teacherId, $excludeScheduleId);

        if ($current >= $max) {
            $role = $max === 1 ? 'tutor' : 'no tutor';
            return "Este profesor es {$role} y ya tiene el máximo de {$max} hora(s) de guardia asignada(s). No se pueden añadir más.";
        }

        return null;
    }

    public function setGuardPeriod(int $teacher_id, int $period_id, string $day) : bool 
    {
        if ($this->validateGuardAssignment($teacher_id) !== null) {
            return false;
        }

        $sql1 = "SELECT id FROM schedules 
                    WHERE teacher_id = :teacher_id 
                    AND period_id = :period_id 
                    AND day = :day";

        $sql2 = "UPDATE schedules 
                    SET class_id = NULL 
                    WHERE id = :id";

        $sql3 = "INSERT INTO schedules (teacher_id, period_id, day, class_id) 
                    VALUES (:teacher_id, :period_id, :day, NULL)";
        
        $params = [
            'teacher_id' => $teacher_id,
            'period_id'  => $period_id,
            'day'        => $day
        ];

        $res = $this->query($sql1, $params, false);

        if ($res['success'] && $res['total'] > 0) {
            $id = $res['data']['id'];
            $updateRes = $this->update($sql2, ['id' => $id]);
            return $updateRes['success'];
        } else {
            $insertRes = $this->insert($sql3, $params);
            return $insertRes['success'];
        }
    }

    public function updateGuardPeriod(int $schedule_id, int $teacher_id): bool
    {
        if ($this->validateGuardAssignment($teacher_id, $schedule_id) !== null) {
            return false;
        }

        $sql = "UPDATE schedules SET teacher_id = :teacher_id WHERE id = :id";
        
        $res = $this->update($sql, [
            'teacher_id' => $teacher_id, 
            'id'         => $schedule_id
        ]);

        if (!($res['success'] ?? false)) {
            return false;
        }
        return true; 
    }
    public function deleteGuardPeriod(int $id): bool
    {
        $sql = "DELETE FROM schedules WHERE id = :id";
        $res = $this->delete($sql, ['id' => $id]);

        if (!($res['success'] ?? false)) {
            return false;
        }
        
        return ($res['rowsAffected'] ?? 0) > 0;
    }

    public function getIdAndDay(int $id){
        $res = $this->query("SELECT
                                    guardias.teacher_id AS teacher_id,
                                    COALESCE(t.full_name, 'Profesor desconocido') AS teacher_name,
                                    t.substitution_counter AS counter
                                FROM substitutions sub
                                JOIN schedules clases_ausentes ON sub.schedule_id = clases_ausentes.id
                                JOIN schedules guardias ON clases_ausentes.period_id = guardias.period_id
                                AND guardias.day = CASE WEEKDAY(sub.date)
                                                    WHEN 0 THEN 'L'
                                                    WHEN 1 THEN 'M'
                                                    WHEN 2 THEN 'X'
                                                    WHEN 3 THEN 'J'
                                                    WHEN 4 THEN 'V'
                                                    ELSE NULL
                                                END

                                LEFT JOIN teachers t ON guardias.teacher_id = t.id
                                WHERE sub.id = ?
                                AND guardias.class_id IS NULL;",[$id]);
                                        return $res['success'] ? $res['data'] : [];
    }
    public function getTeachersHour (int $id, string $letterDay){
        $res = $this->query("SELECT 
                            s.teacher_id,
                            COALESCE(t.full_name, 'Profesor desconocido') AS full_name
                        FROM schedules s 
                        LEFT JOIN teachers t ON s.teacher_id = t.id 
                        WHERE s.class_id IS NULL 
                            AND s.period_id = ? 
                            AND s.`day` = ?",[$id,$letterDay]);
        return $res['success'] ? $res['data'] : [];
    }

    public function importTeacherSchedule(int $teacherId, string $filePath): array
    {
        $teacher = (new Teacher())->getTeacher($teacherId);

        if (empty($teacher) || (int) ($teacher['enabled'] ?? 0) === 0) {
            return ['success' => false, 'message' => 'Profesor no encontrado o dado de baja.'];
        }

        try {
            $parsed = TemplateService::parseTeacherScheduleFile($filePath, $teacherId);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        $codeMap = (new Classes())->getActiveClassCodeMap();
        $entries = [];
        $invalidCodes = [];

        $deduped = [];

        foreach ($parsed['entries'] as $entry) {

            $code = strtoupper($entry['code']);

            $key = $entry['period_id'] . '-' . $entry['day'];

            /*
            * GUARDIA
            */
            if (($entry['is_guard'] ?? false) === true) {

                $deduped[$key] = [
                    'period_id' => (int) $entry['period_id'],
                    'day'       => $entry['day'],
                    'class_id'  => null,
                ];

                continue;
            }

            /*
            * Clase lectiva normal
            */
            if (!isset($codeMap[$code])) {
                $invalidCodes[] = $code;
                continue;
            }

            $deduped[$key] = [
                'period_id' => (int) $entry['period_id'],
                'day'       => $entry['day'],
                'class_id'  => $codeMap[$code],
            ];
        }

        $entries = array_values($deduped);

        if (!empty($invalidCodes)) {
            return [
                'success' => false,
                'message' => 'Códigos de clase no válidos: ' . implode(', ', array_unique($invalidCodes)),
            ];
        }

        try {
            $saved = $this->replaceTeacherClassSchedules($teacherId, $entries);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }

        return [
            'success' => true,
            'message' => 'Horario importado correctamente (' . $saved . ' asignaciones).',
            'count'   => $saved,
        ];
    }

    public function replaceTeacherClassSchedules(int $teacherId, array $entries): int
    {
        $sqlInsert = 'INSERT INTO schedules (teacher_id, period_id, day, class_id) VALUES (?, ?, ?, ?)';

        try {
            $this->connection->exec("DELETE FROM schedules WHERE teacher_id = " . (int) $teacherId);
        } catch (\Throwable $e) {
            throw new \RuntimeException('No se pudo limpiar el horario anterior: ' . $e->getMessage());
        }

        $insertedRows = 0;
        foreach ($entries as $entry) {
            try {
                $stmt = $this->connection->prepare($sqlInsert);
                $stmt->execute([
                    $teacherId,
                    $entry['period_id'],
                    $entry['day'],
                    $entry['class_id'],
                ]);
                $insertedRows += $stmt->rowCount();
            } catch (\Throwable $e) {
                error_log('[Schedule] Error al insertar horario: ' . $e->getMessage()
                    . " | teacher_id={$teacherId}"
                    . " | period_id={$entry['period_id']}"
                    . " | day={$entry['day']}"
                    . " | class_id={$entry['class_id']}");
                throw new \RuntimeException('Error al insertar una asignación de horario.');
            }
        }

        $verify = $this->query("SELECT COUNT(*) AS cnt FROM schedules WHERE teacher_id = ?", [$teacherId], false);
        $saved = (int) ($verify['data']['cnt'] ?? 0);

        if ($saved !== $insertedRows) {
            error_log("[Schedule] Mismatch: insertedRows={$insertedRows}, verified={$saved}");
        }

        return $saved;
    }

    public function getTeacherFree(int $id,string $date): array
    {
        $res = $this->query("SELECT 
                            t.id AS teacher_id, 
                            t.full_name AS teacher_name,
                            t.substitution_counter AS counter
                        FROM teachers t
                        JOIN schedules s ON t.id = s.teacher_id 
                            AND s.period_id = ?
                            AND s.day = (
                                CASE DAYOFWEEK(?)
                                    WHEN 2 THEN 'L'
                                    WHEN 3 THEN 'M'
                                    WHEN 4 THEN 'X'
                                    WHEN 5 THEN 'J'
                                    WHEN 6 THEN 'V'
                                END
                            )
                        JOIN classes c ON s.class_id = c.id
                        JOIN event_schedules es ON s.id = es.schedule_id
                        JOIN events ev ON es.event_id = ev.id 
                            AND ? BETWEEN ev.start_date AND ev.end_date
                            AND ev.enabled = TRUE
                        WHERE 
                            NOT EXISTS (
                                SELECT 1 
                                FROM event_teachers et 
                                WHERE et.event_id = ev.id 
                                AND et.teacher_id = t.id
                            )
                            AND t.enabled = TRUE;",[$id,$date,$date]);
        return $res['success'] ? $res['data'] : [];
    }
}
