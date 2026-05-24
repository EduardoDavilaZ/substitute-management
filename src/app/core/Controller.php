<?php

abstract class Controller {
    public string $view;
    public string $message;
    public ?string $layout;

    final public function __construct() 
    {
        $this->view = '';
        $this->message = '';
        $this->layout = '';
        $this->init();
    }

    protected function init() {}

}