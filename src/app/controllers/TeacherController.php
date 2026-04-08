<?php 

class TeacherController {
    public $view;
    public $msg;
    public $layout = 'teacher/layout';

    public function __construct(){
        $this->view = '';
        $this->msg = '';
    }

    public function home() {
        $this->view = 'teacher/home';
    }
}

?>