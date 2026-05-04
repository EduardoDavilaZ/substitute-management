<?php

spl_autoload_register(function ($class) 
{
    $directories = [
        CONTROLLERS_PATH,
        MODELS_PATH
    ];

    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

foreach (glob(__DIR__ . '/*.php') as $helper) 
{
    if (basename($helper) === basename(__FILE__)) {
        continue;
    }
    require_once $helper;
}

?>