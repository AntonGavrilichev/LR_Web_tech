<?php
define('DB_HOST', 'localhost');
define('DB_PORT', '3307');
define('DB_NAME', 'lab9_db');
define('DB_USER', 'root');
define('DB_PASS', '');

class Database {
    private static $connection = null;

    public static function getConnection() {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=" . DB_HOST .
                    ";port=" . DB_PORT .
                    ";dbname=" . DB_NAME .
                    ";charset=utf8",
                    DB_USER,
                    DB_PASS
                );
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Ошибка подключения к БД: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
?>