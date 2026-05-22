<?php 

final class Schedule extends Model
{
    public function getGuardSchedules() : array
    {
        $sql = "SELECT s.id AS schedule_id, s.day, s.period_id, t.id, t.full_name, t.substitution_counter AS count
                FROM schedules s
                JOIN teachers t ON s.teacher_id = t.id
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

    public function setGuardPeriod(int $teacher_id, int $period_id, string $day) : bool 
    {
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
                                    t.full_name AS teacher_name,
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

                                JOIN teachers t ON guardias.teacher_id = t.id
                                WHERE sub.id = ?
                                AND guardias.class_id IS NULL;",[$id]);
                                        return $res['success'] ? $res['data'] : [];
    }
    public function getTeachersHour (int $id, string $letterDay){
        $res = $this->query("SELECT 
                            s.teacher_id,
                            t.full_name
                        FROM schedules s 
                        JOIN teachers t ON s.teacher_id = t.id 
                        WHERE s.class_id IS NULL 
                            AND s.period_id = ? 
                            AND s.`day` = ?",[$id,$letterDay]);
        return $res['success'] ? $res['data'] : [];
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

?>