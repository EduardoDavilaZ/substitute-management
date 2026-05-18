<?php 

final class AdminController extends Controller
{
    protected function init() {
        $this->layout = 'admin/layout';
    }

    public function home() : void
    {
        $this->view = 'admin/home';
    }

    public function substitutionSchedule()
    {
        $this->view = 'admin/substitution_schedule';

        return [
            'periods'   => (new Period())->getPeriods(),
            'schedules' => (new Schedule())->getGuardSchedules()
        ];
    }

    public function substitutionManagement() : void
    {
        $this->view = 'admin/substitution_management';
    }

    public function substitutionCalendar() : void
    {
        $this->view = 'admin/substitution_calendar';
    }

    public function absenceHistory() : array
    {
        $this->view = 'admin/absence_history';

        return [
            'absences' => (new Absence())->getAbsences()
        ];
    }

    public function eventManagement() : void
    {
        $this->view = 'admin/event_management';
    }

    public function teacherManagement() : void
    {
        $this->view = 'admin/teacher_management';
    }

    public function groupManagement() : void
    {
        $this->view = 'admin/group_management';
    }
}

?>