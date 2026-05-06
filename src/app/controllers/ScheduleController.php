<?php 

final class ScheduleController extends Controller 
{
    protected function init() : void
    {
        $this->layout = 'teacher/layout';
    }

    public function guardScheduleAssignment(int $id, string $day, int $period) : array
    {
        $this->view = 'admin/modals/guard_schedule_assignment';
        $this->layout = null;

        $teachers = (new Teacher())->getTeachers();
        $period = (new Period())->getPeriod($period);
        
        $dayNames = ['L' => 'Lunes', 'M' => 'Martes', 'X' => 'Miércoles', 'J' => 'Jueves', 'V' => 'Viernes'];
        $infoText = ($dayNames[$day] ?? $day) . " - " . 
                    $period['name'] . " (" . $period['start_time'] . "-" . $period['end_time'] . ")";

        return [
            'teachers'   => $teachers,
            'selectedId' => $id > 0 ? $id : null,
            'infoText'   => $infoText,
            'day'        => $day,
            'period'     => $period['id']
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

    public function deleteGuardPeriod() : void 
    {
        $id = (int)($_POST['id'] ?? 0);
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

