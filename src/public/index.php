<?php

session_start();
require_once '../app/config/app.php';
require_once '../app/helpers/app.php';

$clean_url = trim($_GET['url'] ?? '', '/');
$url_parts = explode('/', $clean_url);

$controller_name = empty($url_parts[0]) 
    ? DEFAULT_CONTROLLER 
    : parse_controller_name($url_parts[0]);

$controller_path = CONTROLLERS_PATH . $controller_name . '.php';

if (!file_exists($controller_path)) abort(404);

require_once $controller_path;

$method_name = empty($url_parts[1]) 
    ? DEFAULT_METHOD 
    : parse_method_name($url_parts[1]);

$controller = new $controller_name();
$params     = array_slice($url_parts, 2);

if (!method_exists($controller, $method_name)) abort(404);

$data = ($_SERVER['REQUEST_METHOD'] === 'POST')
    ? $controller->{$method_name}($_POST, ...$params)
    : $controller->{$method_name}(...$params);

if (!empty($controller->view)) {

    if (is_array($data)) {
        extract($data);
    }

    $view_file = VIEWS_PATH . $controller->view . '.php';
    
    $layout_name = $controller->layout ?? 'layout'; 
    $layout_file = VIEWS_PATH . "layouts/{$layout_name}.php";

    if (file_exists($layout_file)) {
        require_once $layout_file;
    } else {
        require_once $view_file;
    }
}

?>