<?php

require_once __DIR__ . '/../config/database.php';

class Database 
{
    private static ?PDO $instance = null;
    private static ?PDO $intranetInstance = null;

    private function __construct() {}

    public static function getConnection(): PDO 
    {
        if (self::$instance === null) {
            try {
                $dsn = DB_CONNECTION . ":host=" . DB_HOST . ";dbname=" . DB_DATABASE . ";charset=utf8mb4;";
                self::$instance = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error en la conexión: " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    public static function getIntranetConnection(): PDO
    {
        if (self::$intranetInstance === null) {
            try {
                $dsn = DB_CONNECTIONINTRA . ":host=" . DB_HOSTINTRA . ";dbname=" . DB_DATABASEINTRA . ";charset=utf8mb4;";
                self::$intranetInstance = new PDO($dsn, DB_USERNAMEINTRA, DB_PASSWORDINTRA);
                self::$intranetInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error en la conexión a INTRANET: " . $e->getMessage());
            }
        }
        return self::$intranetInstance;
    }

    private function __clone() {}
}