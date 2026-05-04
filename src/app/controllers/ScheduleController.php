<?php 

final class ScheduleController extends Controller 
{
    protected function init() : void
    {
        $this->layout = 'teacher/layout';
    }

    public function guardScheduleAssignment(?int $id = null) : array
    {
        $this->view = 'admin/modals/guard_schedule_assignment';
        $this->layout = null;

        $teachers = (new Teacher())->getTeachers();
        
        $infoText = $_GET['info'] ?? 'Sin información'; 

        return [
            'teachers'   => $teachers,
            'selectedId' => $id,
            'infoText'   => $infoText
        ];
    }
}

?>

