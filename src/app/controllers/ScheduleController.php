<?php 

final class ScheduleController extends Controller 
{
    protected function init() : void
    {
        $this->layout = 'teacher/layout';
    }

    public function guardScheduleAssignment(int $id, string $day, int $period_id) : array
    {
        $this->view = 'admin/modals/guard_schedule_assignment';
        $this->layout = null;

        $teachers = (new Teacher())->getTeachers();
        $period = (new Period())->getPeriod($period_id);
        $dayNames = ['L' => 'Lunes', 'M' => 'Martes', 'X' => 'Miércoles', 'J' => 'Jueves', 'V' => 'Viernes'];
        $infoText = ($dayNames[$day] ?? $day) . " - " . 
                    $period['name'] . " (" . $period['start_time'] . "-" . $period['end_time'] . ")";
        
        $currentSchedule = ($id > 0) ? (new Schedule())->getSchedule($id) : null;
        $selectedTeacherId = $currentSchedule ? $currentSchedule['teacher_id'] : null;

        return [
            'teachers'          => $teachers,
            'selectedId'        => $id, 
            'selectedTeacherId' => $selectedTeacherId,
            'infoText'          => $infoText,
            'day'               => $day,
            'period'            => $period['id']
        ];
    }   

    public function setGuardPeriod() : void
    {
        $fields = validate_fields(['teacher_id', 'day', 'period_id']);
        
        if (!$fields) {
            json_error("Faltan datos requeridos.");
        }

        extract($fields);
        $success = (new Schedule())->setGuardPeriod($teacher_id, $period_id, $day);
        
        if ($success) {
            json_success("La asignación se realizó correctamente.");
        } else {
            json_error("No se pudo guardar la asignación en la base de datos.");
        }
    }

    public function updateGuardPeriod() : void 
    {
        $id = $_POST['id'] ?? 0;
        $teacher_id = $_POST['teacher_id'] ?? 0;
        
        if ($id <= 0 || $teacher_id <= 0) {
            json_error("Datos inválidos. ID: $id, Teacher: $teacher_id");
            return;
        }

        $result = (new Schedule())->updateGuardPeriod($id, $teacher_id);
        
        if ($result) {
            json_success("El profesor ha sido actualizado correctamente.");
        } else {
            json_error("No se pudo actualizar la asignación.");
        }
    }

    public function deleteGuardPeriod() : void 
    {
        $id = $_POST['id'] ?? 0;
        if ($id <= 0) {
            json_error("ID de guardia inválido.");
        }

        $result = (new Schedule())->deleteGuardPeriod($id);
        
        if ($result) {
            json_success("La guardia ha sido eliminada correctamente.");
        } else {
            json_error("No se pudo eliminar: el registro no existe o ya fue eliminado.");
        }
    }
}

?>

