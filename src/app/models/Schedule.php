<?php 

final class Schedule extends Model
{
    public function getGuardSchedules() : array
    {
        $sql = "SELECT s.day, s.period_id, t.id, t.full_name, t.substitution_counter AS count
                FROM schedules s
                JOIN teachers t ON s.teacher_id = t.id
                WHERE s.class_id IS NULL";
        
        $res = $this->query($sql);
        return $res['success'] ? $res['data'] : [];
    }

    public function setGuardTime(int $teacher_id, int $period_id, string $day) : bool 
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
}

?>