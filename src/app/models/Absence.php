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
                                WHERE s.is_enabled=1
                                GROUP BY ab.id, ta.full_name, ab.is_justified, ab.date, ab.reason");
            return $res['success'] ? $res['data'] : [];
        }

        public function getAbsenceById(int $id) : array
        {
            $res = $this->query("SELECT
                                    ab.id,
                                    ta.full_name AS absent,
                                    ab.is_justified AS justify,
                                    ab.date AS date_absence,
                                    ab.reason AS reason,
                                    ab.proof_file_path AS material,
                                    c.name AS class,
                                    p.name AS name_hour,
                                    s.`status` AS state,
                                    ts.full_name AS sustitute
                                FROM substitutions s
                                JOIN teachers ta ON s.absent_teacher_id = ta.id
                                JOIN absence_period a ON s.absence_detail_id = a.id
                                JOIN absences ab ON a.absence_id = ab.id
                                JOIN classes c ON s.class_id = c.id
                                JOIN periods p ON a.period_id = p.id
                                LEFT JOIN teachers ts ON s.substitute_teacher_id = ts.id
                                WHERE s.is_enabled = 1 AND ab.id = ?", [$id]);
            return $res['success'] ? $res['data'] : [];
        }

        public function getAbsencesDetail() : array
        {
            $res = $this->query("SELECT
                                    s.id,
                                    c.name AS class,
                                    ta.full_name AS absent,
                                    p.name AS name_hour,
                                    ab.is_justified AS justify,
                                    s.`status` AS state,
                                    ts.full_name AS sustitute,
                                    ab.date AS date_absence,
                                    ab.reason AS reason,
                                    ab.proof_file_path AS material
                                FROM substitutions s
                                JOIN classes c ON s.class_id = c.id
                                JOIN teachers ta ON s.absent_teacher_id = ta.id
                                JOIN absence_period a ON s.absence_detail_id = a.id
                                JOIN periods p ON a.period_id = p.id
                                JOIN absences ab ON a.absence_id = ab.id
                                LEFT JOIN teachers ts ON s.substitute_teacher_id = ts.id
                                WHERE s.is_enabled=1 AND (s.status='PENDIENTE' OR ab.is_justified = 0)");
            return $res['success'] ? $res['data'] : [];
        }
    }
?>