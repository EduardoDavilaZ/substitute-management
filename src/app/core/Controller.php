<?php

abstract class Controller {
    public string $view;
    public string $msg;
    public ?string $layout;

    final public function __construct() 
    {
        $this->view = '';
        $this->msg = '';
        $this->layout = '';
        $this->init();
    }

    protected function init() {}

}

?>