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
        $res = $this->query("SELECT p.id as id, sc.`day` as day from substitutions s
                                JOIN schedules sc ON s.schedule_id = sc.id
                                JOIN periods p ON sc.period_id = p.id
                                WHERE s.schedule_id = ?",[$id]);
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
}

?>