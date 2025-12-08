<?php
class Controller {
    protected $model;
    protected $view;
    protected $db;

    public function __construct() {
        // Инициализация сессии (с проверкой)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Инициализация CSRF токена
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Создаем экземпляр View
        require_once 'app/core/View.php';
        $this->view = new View();

        // Подключаем базу данных
        require_once 'config/database.php';
        $this->db = Database::getConnection();
    }

    protected function loadModel($name) {
        $modelFile = 'app/models/' . $name . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            $modelName = $name;
            $this->model = new $modelName();
        }
    }

    // Метод для сохранения статистики посещений
    protected function saveVisitStatistics() {
        $page = $_SERVER['REQUEST_URI'];
        // Не сохраняем статистику для админской зоны и AJAX запросов
        if (strpos($page, '/admin/') === false) {
            try {
                // Проверяем существование таблицы statistics
                $checkTable = $this->db->query("SHOW TABLES LIKE 'statistics'");
                if ($checkTable && $checkTable->rowCount() > 0) {
                    $sql = "INSERT INTO statistics (time_statistic, web_page, ip_address, host_name, browser_name) 
                            VALUES (?, ?, ?, ?, ?)";
                    $stmt = $this->db->prepare($sql);

                    $time = date('Y-m-d H:i:s');
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $host = @gethostbyaddr($ip) ?: $ip;
                    $browser = $_SERVER['HTTP_USER_AGENT'] ?? 'Неизвестный браузер';

                    $stmt->execute([$time, $page, $ip, $host, $browser]);
                }
            } catch (Exception $e) {
                // Логируем ошибку, но не прерываем выполнение
                error_log("Ошибка сохранения статистики: " . $e->getMessage());
            }
        }
    }

    // Метод для проверки авторизации пользователя
    protected function isUserLoggedIn() {
        return isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true;
    }

    // Метод для проверки авторизации администратора
    protected function isAdmin() {
        return isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1;
    }

    // Метод для получения информации о текущем пользователе
    protected function getCurrentUser() {
        if ($this->isUserLoggedIn() && isset($_SESSION['user_id'])) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                error_log("Ошибка получения пользователя: " . $e->getMessage());
                return null;
            }
        }
        return null;
    }

    // Генерация CSRF токена
    protected function generateCsrfToken() {
        return $_SESSION['csrf_token'];
    }

    // Проверка CSRF токена
    protected function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>