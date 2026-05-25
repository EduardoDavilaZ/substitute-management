<?php

define("BASE_PATH", dirname(__DIR__, 2) . '/'); 

/**
 * MVC Paths
 */
define("VIEWS_PATH", BASE_PATH . "app/views/");
define("CONTROLLERS_PATH", BASE_PATH . "app/controllers/");
define("MODELS_PATH", BASE_PATH . "app/models/");
define("CORE_PATH", BASE_PATH . "app/core/");
define("SERVICES_PATH", BASE_PATH . "app/services/");

/**
 * Default values
 */
define("DEFAULT_CONTROLLER", "RoleController");
define("DEFAULT_METHOD", "index");

/**
 * URLs (Browser / Assets)
 */
define("BASE_URL", getBaseUrl());

define("ASSETS_URL", BASE_URL . "assets/");

define("JS_URL", ASSETS_URL . "js/");
define("CSS_URL", ASSETS_URL . "css/");
define("IMG_URL", ASSETS_URL . "img/");
define("VENDOR_URL", ASSETS_URL . "vendor/");

define("UPLOADS_URL", BASE_URL . "uploads/");

/**
 * Paths (Filesystem / Backend)
 */
define("PUBLIC_PATH", BASE_PATH . "public/");
define("UPLOADS_PATH", PUBLIC_PATH . "uploads/");
define("UPLOADS_IMG_PATH", UPLOADS_PATH . "img/");

/**
 * Get BASE_URL from protocol, host and base path
 */
function getBaseUrl(): string
{
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $scriptPath = dirname($scriptName);
    
    $scriptPath = str_replace('\\', '/', $scriptPath);
    
    if (basename($scriptPath) === 'public') {
        $scriptPath = dirname($scriptPath);
    }
    
    $basePath = rtrim($scriptPath, '/');
    
    return $protocol . $host . $basePath . '/';
}