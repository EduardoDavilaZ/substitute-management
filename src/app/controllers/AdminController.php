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

    public function absenceHistory() : void
    {
        $this->view = 'admin/absence_history';
    }

    public function eventManagement() : array
    {
        $this->view = 'admin/event_management';

        return [
            'events' => (new Event())->getEventsWithClasses()
        ];
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