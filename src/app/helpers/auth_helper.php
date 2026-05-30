<?php

function init_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function auth(): void
{
    if (!is_logged()) {
        redirect('');
    }
}

function is_logged(): bool
{
    $usuario = JWTMiddleware::getUsuario();
    if ($usuario !== null && isset($usuario['user_id'])) {
        return true;
    }

    init_session();
    return isset($_SESSION['user_id']);
}

function current_user_id(): ?int
{
    $usuario = JWTMiddleware::getUsuario();
    if ($usuario !== null) {
        return $usuario['user_id'] ?? null;
    }

    init_session();
    return $_SESSION['user_id'] ?? null;
}

function current_user_role(): ?string
{
    $usuario = JWTMiddleware::getUsuario();
    if ($usuario !== null) {
        return $usuario['rol'] ?? null;
    }

    init_session();
    return $_SESSION['user_role'] ?? null;
}

function logout(): void
{
    setcookie('auth_token', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);

    init_session();
    session_destroy();

    $intranetUrl = $_ENV['INTRANET_URL'] ?? '';
    if ($intranetUrl !== '' && str_starts_with($intranetUrl, 'http')) {
        header("Location: " . $intranetUrl);
    } else {
        redirect($intranetUrl);
    }
    exit;
}
