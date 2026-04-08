<?php

/**
 * Starts the session if it hasn't been started yet.
 */
function init_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Middleware: Redirects to login if the user is not authenticated.
 * @return void
 */
function auth(): void {
    init_session();
    
    if (!isset($_SESSION['user_id'])) {
        redirect('');
    }
}

/**
 * Check if the user is currently logged in.
 * @return bool
 */
function is_logged(): bool {
    init_session();
    return isset($_SESSION['user_id']);
}

/**
 * Log out the user and destroy the session.
 */
function logout(): void {
    init_session();
    session_destroy();
    redirect('login');
}

?>