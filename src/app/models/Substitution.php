<?php
    final class Substitution extends Model
    {
        public function getSubstitutions() : array
        {
            $res = $this->all('substitutions');
            return $res['data'] ?? [];
        }

        public function getSubstitution(int $id) : array
        {
            $res = $this->find('substitutions', $id);
            return $res['success'] ? $res['data'] : [];
        }

        public function countTodaySubstitutions(string $date) : int
        {
            $res = $this->query("SELECT COUNT(*) as count FROM substitutions WHERE date = ? AND substitute_teacher_id is not null", [$date]);
            return $res['success'] ? (int)$res['data'][0]['count'] : 0;
        }
        public function getPendingGuards(string $date) : array
        {
            $res = $this->query("SELECT 
                                    ELT(WEEKDAY(s.date) + 1, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo') AS dia,c.name AS class_name 
                                FROM substitutions s 
                                JOIN classes c ON s.class_id = c.id 
                                WHERE s.date = ? AND s.substitute_teacher_id IS NULL;", [$date]);
            return $res['success'] ? $res['data'] : [];
        }

        public function getSubstitutionsWeek(string $date) : array
        {
            $res = $this->query("SELECT 
                                    ELT(WEEKDAY(date) + 1, 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom') AS dia_etiqueta,
                                    COUNT(*) AS total_sustituciones
                                FROM substitutions
                                WHERE YEARWEEK(date, 1) = YEARWEEK(?, 1)
                                GROUP BY date
                                ORDER BY date ASC",[$date]);
            return $res['success'] ? $res['data'] : [];
        }

        public function getSubstitutionsCalendar() : array
        {
            $res = $this->query("SELECT s.date AS start, c.name AS title, c.name AS class,c.stage, ta.full_name AS absent_teacher, ts.full_name AS substitute_teacher
                                FROM substitutions s 
                                JOIN teachers ta ON s.absent_teacher_id = ta.id
                                JOIN classes c ON s.class_id = c.id
                                JOIN teachers ts ON s.substitute_teacher_id = ts.id
                                WHERE s.status = 'CONFIRMADO'");
            return $res['success'] ? $res['data'] : [];
        }
        public function deleteSubstitutions(int $id): bool
        {   
            $res = $this->query("UPDATE substitutions SET enabled = 0 WHERE id = ?;",[$id]);
            return $res['success'] ? true : false;
        }
    }
?>