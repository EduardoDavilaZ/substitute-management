<?php

final class Absence extends Model
{
    public function getAbsences() : array
    {
        $sql = "SELECT absence.id, absence.date, absence.reason, absence.is_justified, teacher.full_name, COUNT(absence_period.id) AS total_periods
                FROM absences absence
                INNER JOIN teachers  teacher ON teacher.id = absence.teacher_id
                LEFT JOIN absence_period absence_period ON absence_period.absence_id = absence.id
                GROUP BY absence.id, absence.date, absence.reason, absence.is_justified, teacher.full_name
                ORDER BY absence.date DESC";

        $res = $this->query($sql);

        if (!$res['success']) {
            return [];
        }

        return $res['data'];
    }
}

?>
