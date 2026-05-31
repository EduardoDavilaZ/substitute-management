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
        $res = $this->query("SELECT
            sub.id AS id,
            t_abs.full_name AS absent,
            sub.absence_detail_id as detailId,
            c.name AS class,
            sub.date AS date,
            p.name AS name_hour,
            a.is_justified AS justify,
            sub.status AS state,
            sub.absent_teacher_id
        FROM substitutions sub
        LEFT JOIN teachers t_abs ON sub.absent_teacher_id = t_abs.id
        LEFT JOIN classes c ON sub.class_id = c.id
        LEFT JOIN absence_period ap ON sub.absence_detail_id = ap.id
        LEFT JOIN periods p ON ap.period_id = p.id
        LEFT JOIN absences a ON ap.absence_id = a.id
        WHERE sub.enabled = TRUE AND sub.id = ?",[$id]);

        return $res['success'] ? $res['data'] : [];
        
    }

    public function getSubstitutionsBySubstitute(int $teacherId): array
    {
        $sql = "SELECT
                    s.id,
                    s.date,
                    s.status,
                    COALESCE(p.name, '-') AS period_name,
                    p.start_time,
                    p.end_time,
                    c.code AS class_code,
                    COALESCE(c.name, 'Sin clase') AS class_name,
                    c.stage AS class_stage,
                    COALESCE(ta.full_name, 'Profesor desconocido') AS absent_teacher,
                    ab.reason,
                    ab.proof_file_path AS material
                FROM substitutions s
                LEFT JOIN teachers ta ON s.absent_teacher_id = ta.id
                LEFT JOIN classes c ON s.class_id = c.id
                JOIN absence_period ap ON s.absence_detail_id = ap.id
                JOIN absences ab ON ap.absence_id = ab.id
                LEFT JOIN periods p ON ap.period_id = p.id
                WHERE s.substitute_teacher_id = ?
                    AND s.enabled = 1
                    AND s.status IN ('PENDIENTE', 'CONFIRMADO')
                ORDER BY s.date ASC, p.start_time ASC";

        $res = $this->query($sql, [$teacherId]);
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
                                ELT(WEEKDAY(s.date) + 1, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo') AS dia,
                                COALESCE(c.name, 'Sin clase') AS class_name 
                            FROM substitutions s 
                            LEFT JOIN classes c ON s.class_id = c.id 
                            WHERE s.date = ? AND s.status = 'PENDIENTE';", [$date]);
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
        $res = $this->query("SELECT s.date AS start,
                                    COALESCE(c.name, 'Sin clase') AS title,
                                    COALESCE(c.name, 'Sin clase') AS class,
                                    c.stage,
                                    COALESCE(ta.full_name, 'Profesor desconocido') AS absent_teacher,
                                    COALESCE(ts.full_name, 'Sin asignar') AS substitute_teacher
                            FROM substitutions s 
                            LEFT JOIN teachers ta ON s.absent_teacher_id = ta.id
                            LEFT JOIN classes c ON s.class_id = c.id
                            LEFT JOIN teachers ts ON s.substitute_teacher_id = ts.id
                            WHERE s.status = 'CONFIRMADO'");
        return $res['success'] ? $res['data'] : [];
    }
    public function deleteSubstitutions(int $id): bool
    {   
        $res = $this->query("UPDATE substitutions 
                            SET enabled = 0, status = 'CANCELADO' 
                            WHERE id = ?;",[$id]);
        return $res['success'] ? true : false;
    }
    public function assign(){
        $substitutionId = $_POST['idSubstitution'] ?? 0;
        $teacherToday = $_POST['teacherToday'] ?? '';
        $teacherFree = $_POST['teacherFree'] ?? '';

        if (!$substitutionId || (!$teacherToday && !$teacherFree)) {
            return false;
        }

        $selectedTeacher = $teacherToday ?: $teacherFree;

        $res = $this->query("UPDATE substitutions
                        SET substitute_teacher_id = ?,
                            status = 'CONFIRMADO',
                            updated_at = NOW()
                        WHERE id = ?
                        AND status = 'PENDIENTE'
                        AND enabled = 1;",[$selectedTeacher, $substitutionId]);
        if($res['success']){
            return $this->incraseCounter($selectedTeacher);
        }
    }
    private function incraseCounter (int $selectedTeacher){
        $res = $this->query("UPDATE teachers SET substitution_counter = substitution_counter + 1 WHERE id = ?",[$selectedTeacher]);
        return (bool)$res['success'];
    }

    public function generateSubstitutionsFromAbsencePeriods(): bool
    {
        $sqlInsert = "INSERT INTO substitutions (absence_detail_id, schedule_id, absent_teacher_id, class_id, date)
            SELECT ap.id, s.id, a.teacher_id, s.class_id, a.date
            FROM absence_period ap
            JOIN absences a ON ap.absence_id = a.id
            JOIN schedules s ON s.teacher_id = a.teacher_id
                AND s.period_id = ap.period_id
                AND s.day = (
                    CASE DAYOFWEEK(a.date)
                        WHEN 2 THEN 'L'
                        WHEN 3 THEN 'M'
                        WHEN 4 THEN 'X'
                        WHEN 5 THEN 'J'
                        WHEN 6 THEN 'V'
                    END
                )
            WHERE ap.is_cover_generated = FALSE
            AND s.class_id IS NOT NULL";

        $resInsert = $this->insert($sqlInsert, []);
        if (!$resInsert['success']) {
            return false;
        }

        $resUpdate = $this->update(
            "UPDATE absence_period SET is_cover_generated = TRUE WHERE is_cover_generated = FALSE",
            []
        );

        return $resUpdate['success'];
    }
}
