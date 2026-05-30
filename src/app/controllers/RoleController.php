<?php

final class RoleController extends Controller
{
    protected function init() : void
    {
        $this->layout = 'null';
    }

    public function index() : void
    {
        $this->view = 'role_selector';
    }

    public function setRole(string $rol) : never
    {
        $_SESSION['user_role'] = $rol;
        $_SESSION['user_id'] = 1;

        $target = match ($rol) {
            'Coordinador' => 'admin/home',
            'admin'       => 'admin/home',
            default       => 'teacher/home',
        };
        redirect($target);
    }
}