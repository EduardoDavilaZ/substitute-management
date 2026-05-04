<?php 

final class Schedule extends Model
{
    public function getGuardSchedules(): array
    {
        $sql = "SELECT s.day, s.period_id, t.id, t.full_name, t.substitution_counter AS count
                FROM schedules s
                JOIN teachers t ON s.teacher_id = t.id
                WHERE s.class_id IS NULL";
        
        $res = $this->query($sql);
        return $res['success'] ? $res['data'] : [];
    }
}

?>