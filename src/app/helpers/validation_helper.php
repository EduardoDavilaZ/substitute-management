<?php 

function validate_email(string $email, int $max = 100): ?string
{
    if (strlen($email) > $max) {
        return "El correo no puede superar {$max} caracteres.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Correo electrónico no válido.';
    }

    return null;
}

function validate_length(string $value, int $min, int $max, string $field): ?string 
{
    $len = mb_strlen($value);

    if ($len < $min) {
        return "{$field} debe tener al menos {$min} caracteres.";
    }

    if ($len > $max) {
        return "{$field} no puede superar {$max} caracteres.";
    }

    return null;
}

function validate_regex(string $value, string $pattern, string $message): ?string 
{
    if (!preg_match($pattern, $value)) {
        return $message;
    }

    return null;
}