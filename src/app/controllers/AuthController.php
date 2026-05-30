<?php

final class AuthController extends Controller
{
    protected function init(): void
    {
        $this->layout = null;
    }

    public function login(): never
    {
        $token = $_POST['token'] ?? '';
        if (empty($token)) {
            redirect('');
        }

        $payload = JWTMiddleware::validateToken($token);
        if ($payload === null) {
            redirect('');
        }

        $usuario = $payload['data'] ?? [];
        $rol = $usuario['rol'] ?? '';

        setcookie('auth_token', $token, [
            'expires'  => 0,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        (new Teacher())->sincronizarTodos();

        $target = match ($rol) {
            'Coordinador' => 'admin/home',
            'Profesor'    => 'teacher/home',
            default       => '',
        };

        redirect($target);
    }

    public function info(): never
    {
        $usuario = JWTMiddleware::getUsuario();
        if ($usuario === null) {
            json(['status' => 'error', 'message' => 'No autenticado'], 401);
        }

        json([
            'status' => 'success',
            'data' => [
                'user_id'   => $usuario['user_id'] ?? null,
                'nombre'    => $usuario['nombre'] ?? '',
                'apellidos' => $usuario['apellidos'] ?? '',
                'email'     => $usuario['email'] ?? '',
                'foto'      => $usuario['foto'] ?? '',
                'rol'       => $usuario['rol'] ?? '',
            ]
        ]);
    }

    public function logout(): never
    {
        setcookie('auth_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        $intranetUrl = $_ENV['INTRANET_URL'] ?? '';
        if ($intranetUrl !== '' && str_starts_with($intranetUrl, 'http')) {
            header("Location: " . $intranetUrl);
            exit;
        }
        redirect($intranetUrl);
    }
}
