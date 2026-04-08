<?php

class RoleController {
    public $view;
    public $layout;

    public function index() {
        $this->view = 'role_selector';
        $this->layout = null;
    }

    public function setRole($rol) {
        $_SESSION['user_role'] = $rol;
        $_SESSION['user_id'] = 1;
        
        $target = ($rol === 'admin') ? 'admin/home' : 'teacher/home';
        redirect($target);
    }
}

?>