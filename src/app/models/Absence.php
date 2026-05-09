<?php
    final class Absence extends Model
    {
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
    }
?>