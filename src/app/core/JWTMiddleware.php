<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTMiddleware
{
    private const ROLE_PRIORITY = ['coordinador_guardias', 'profesor'];

    private const ROLE_MAP = [
        'coordinador_guardias' => 'Coordinador',
        'profesor'             => 'Profesor',
    ];

    public const ROLES_TODOS  = ['Coordinador', 'Profesor'];
    public const ROLES_EDITOR = ['Coordinador', 'Profesor'];
    public const ROLES_ADMIN  = ['Coordinador'];

    private static ?array $usuarioCache = null;
    private static ?string $tokenActual = null;

    public static function validateToken(?string $tokenArg = null): ?array
    {
        if (self::$usuarioCache !== null) {
            return self::$usuarioCache;
        }

        $token = $tokenArg ?? self::extractToken();
        if ($token === null) {
            return null;
        }

        try {
            $jwtSecret = $_ENV['JWT_SECRET'] ?? '';
            if (empty($jwtSecret)) {
                return null;
            }

            $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));
            $payload = json_decode(json_encode($decoded), true);

            if (isset($payload['data']) && is_array($payload['data'])) {
                $payload['data'] = self::normalizePayload($payload['data']);
            }

            self::$usuarioCache = $payload;
            self::$tokenActual = $token;

            return $payload;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function getUsuario(): ?array
    {
        $payload = self::validateToken();
        return $payload['data'] ?? null;
    }

    public static function getToken(): ?string
    {
        if (self::$tokenActual === null) {
            self::validateToken();
        }
        return self::$tokenActual;
    }

    public static function requireRole(array $rolesPermitidos, ?array $usuario): void
    {
        if ($usuario === null || !isset($usuario['rol'])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'No autenticado']);
            exit;
        }

        if (!in_array($usuario['rol'], $rolesPermitidos, true)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'No autorizado para esta acción']);
            exit;
        }
    }

    public static function requireMethod(string $metodo): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($metodo)) {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
            exit;
        }
    }

    private static function extractToken(): ?string
    {
        $auth = $_SERVER['HTTP_AUTHORIZATION']
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
            ?? '';
        if (preg_match('/^Bearer\s+(.+)$/i', $auth, $matches)) {
            return $matches[1];
        }

        if (!empty($_SERVER['HTTP_X_AUTH_TOKEN'])) {
            return $_SERVER['HTTP_X_AUTH_TOKEN'];
        }

        if (!empty($_COOKIE['auth_token'])) {
            return $_COOKIE['auth_token'];
        }

        return null;
    }

    private static function normalizePayload(array $data): array
    {
        if (!isset($data['user_id']) && isset($data['id'])) {
            $data['user_id'] = (int) $data['id'];
        }

        if (!isset($data['rol']) && isset($data['roles']) && is_array($data['roles'])) {
            $rol = null;
            foreach (self::ROLE_PRIORITY as $r) {
                if (in_array($r, $data['roles'], true)) {
                    $rol = self::ROLE_MAP[$r] ?? null;
                    break;
                }
            }
            $data['rol'] = $rol;
        }

        return $data;
    }
}
