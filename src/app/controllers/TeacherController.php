<?php 

final class TeacherController extends Controller 
{
    protected function init() : void
    {
        $this->layout = 'teacher/layout';
    }

    public function home() : void
    {
        $this->view = 'teacher/home';
    }
}

?>