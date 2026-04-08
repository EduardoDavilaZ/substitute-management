<?php

/**
 * Send a JSON response and terminate the script.
 * * @param mixed $data Data to be encoded.
 * @param int $status HTTP response code.
 * @return never
 */
function json(mixed $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function json_error(string $message, int $status = 400): never
{
    json([
        'status' => 'error', 
        'message' => $message
    ], $status);
}

function json_success(string $message, array|object $data = [], int $status = 200): never
{
    json([
        'status' => 'success', 
        'message' => $message, 
        'data' => $data
    ], $status);
}

?>