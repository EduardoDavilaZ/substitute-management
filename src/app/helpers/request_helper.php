<?php

/**
 * Validate and sanitize multiple input fields.
 * Works with: Text, Select, Password, and Required Radio buttons.
 * * @param array $fields List of required field names.
 * @param string $method 'post' or 'get'.
 * @return array|null Returns sanitized data array or null if any field is missing/empty.
 */
function validate_fields(array $fields, string $method = 'post'): ?array
{
    $source = ($method === 'post') ? $_POST : $_GET;
    $data = [];

    foreach ($fields as $field) {
        if (!isset($source[$field]) || (is_string($source[$field]) && trim($source[$field]) === '')) {
            return null; 
        }
        
        $data[$field] = is_string($source[$field]) ? trim($source[$field]) : $source[$field];
    }

    return $data;
}

/**
 * Get a specific input value with an optional default.
 * Works with: Any input type (Text, Radio, Hidden).
 * * @param string $key The input name.
 * @param mixed $default Value to return if key doesn't exist.
 * @return mixed
 */
function input(string $key, mixed $default = null): mixed
{
    $value = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_string($value) ? trim($value) : $value;
}

/**
 * Check if an input key exists in the request.
 * Best for: Checkboxes and Optional Radio buttons.
 * * @param string $key The input name.
 * @return bool
 */
function has_input(string $key): bool
{
    return isset($_POST[$key]) || isset($_GET[$key]);
}

?>