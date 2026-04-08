<?php 

class AdminController {
    public $view;
    public $msg;
    public $layout = 'admin/layout';

    public function __construct(){
        $this->view = '';
        $this->msg = '';
    }

    public function home() {
        $this->view = 'admin/home';
    }

    public function dailySubstitutions() {
        $this->view = 'admin/daily_substitutions';
    }

    public function substitutionSchedule() {
        $this->view = 'admin/substitution_schedule';
    }

    public function teacherManagement() {
        $this->view = 'admin/teacher_management';
    }

    public function groupManagement() {
        $this->view = 'admin/group_management';
    }


    public function getData(){
        $this->view = '';
        return json([
            "name" => "My Name",
            "age" => 19,
            "city" => "Badajoz"
        ]);
    }
}

?>