<?php

/**
 * Generate a full URL based on the application's BASE_URL.
 */
function url(string $path = ''): string 
{
    return BASE_URL . ltrim($path, '/');
}

/**
 * Redirect to a specific path and terminate execution.
 */
function redirect(string $path): never 
{
    header("Location: " . url($path));
    exit;
}

/**
 * Generate the URL for different types of assets.
 */
function asset_url(string $path = '', string $type = 'assets'): string 
{
    return match ($type) {
        'js'     => JS_URL . ltrim($path, '/'),
        'css'    => CSS_URL . ltrim($path, '/'),
        'vendor' => VENDOR_URL . ltrim($path, '/'),
        'img'    => IMG_URL . ltrim($path, '/'),
        default  => ASSETS_URL . ltrim($path, '/'),
    };
}

/**
 * Generate a <script> tag for JS files.
 */
function js(string $path, string $type = 'js', string $attrs = ''): string 
{
    return '<script src="' . asset_url($path, $type) . '" ' . $attrs . '></script>';
}

/**
 * Generate a <link> tag for CSS files.
 */
function css(string $path, string $type = 'css', string $attrs = ''): string 
{
    return '<link rel="stylesheet" href="' . asset_url($path, $type) . '" ' . $attrs . '>';
}

/**
 * Generate an <img> tag with custom class and attributes.
 */
function img(string $path, string $alt = '', string $clase = '', string $type = 'img', string $attrs = ''): string 
{
    return '<img    src="' . asset_url($path, $type) . '" 
                    alt="' . htmlspecialchars($alt) . '" 
                    class="' . htmlspecialchars($clase) . '" 
                    ' . $attrs . '>';
}

/**
 * Returns a CSS class if the current URI matches the given path.
 * Useful for highlighting active links in navigation.
 */
function active_class(string $path, string $class = 'active'): string 
{
    $relative_uri = trim($_GET['url'] ?? '', '/');
    
    $path = trim($path, '/');

    if ($path === '' && $relative_uri === '') {
        return $class;
    }

    if ($path !== '' && ($relative_uri === $path || strpos($relative_uri, $path . '/') === 0)) {
        return $class;
    }

    return '';
}

function parse_controller_name(string $name): string 
{
    if (strpos($name, 'Controller') !== false) return $name;
    
    $name = str_replace(['-', '_'], ' ', $name);
    $name = ucwords($name);
    return str_replace(' ', '', $name) . 'Controller';
}

function parse_method_name(string $name): string 
{
    if ($name === DEFAULT_METHOD) return $name;

    $name = str_replace(['-', '_'], ' ', $name);
    $name = ucwords($name);
    return lcfirst(str_replace(' ', '', $name));
}

function abort(int $code = 404): void
{
    http_response_code($code);
    
    $file = VIEWS_PATH . "errors/{$code}.php";
    
    if (file_exists($file)) {
        require_once $file;
    } else {
        die("Error {$code}: La página solicitada no está disponible.");
    }
    
    exit;
}

?>