<?php
class CommentController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Обработка отправки комментария (через iFrame)
     */
    public function add() {
        // Отладочная информация
        error_log("=== CommentController::add() called ===");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        error_log("Session status: " . session_status());

        // Проверяем модель
        if (!$this->model) {
            error_log("Модель не загружена!");
            header('Content-Type: text/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<response>';
            echo '<status>error</status>';
            echo '<message>Ошибка инициализации контроллера</message>';
            echo '</response>';
            exit;
        } else {
            error_log("Модель загружена успешно: " . get_class($this->model));
        }
        // Начинаем сессию если не начата
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Проверяем авторизацию
        if (!isset($_SESSION['isLoggedIn']) || !$_SESSION['isLoggedIn']) {
            // Возвращаем XML с ошибкой
            header('Content-Type: text/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<response>';
            echo '<status>error</status>';
            echo '<message>Требуется авторизация</message>';
            echo '</response>';
            exit;
        }

        // Проверяем метод запроса
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: text/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<response>';
            echo '<status>error</status>';
            echo '<message>Неверный метод запроса</message>';
            echo '</response>';
            exit;
        }

        // Получаем данные
        $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
        $content = trim($_POST['content'] ?? '');

        // Валидация
        if ($postId < 1) {
            header('Content-Type: text/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<response>';
            echo '<status>error</status>';
            echo '<message>Неверный ID записи</message>';
            echo '</response>';
            exit;
        }

        if (empty($content)) {
            header('Content-Type: text/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<response>';
            echo '<status>error</status>';
            echo '<message>Комментарий не может быть пустым</message>';
            echo '</response>';
            exit;
        }

        // Загружаем модель
        $this->loadModel('CommentModel');

        // Сохраняем комментарий
        try {
            $userId = $_SESSION['user_id'];
            $success = $this->model->saveComment($postId, $userId, $content);

            if ($success) {
                // Получаем информацию о пользователе для ответа
                $authorName = $_SESSION['user_full_name'] ?? $_SESSION['user_login'];
                $currentTime = date('Y-m-d H:i:s');

                header('Content-Type: text/xml; charset=utf-8');
                echo '<?xml version="1.0" encoding="UTF-8"?>';
                echo '<response>';
                echo '<status>success</status>';
                echo '<message>Комментарий успешно добавлен</message>';
                echo '<comment>';
                echo '<author>' . htmlspecialchars($authorName) . '</author>';
                echo '<content>' . htmlspecialchars($content) . '</content>';
                echo '<created_at>' . $currentTime . '</created_at>';
                echo '<date_formatted>' . date('d.m.Y H:i', strtotime($currentTime)) . '</date_formatted>';
                echo '</comment>';
                echo '</response>';
                exit;
            } else {
                throw new Exception('Ошибка сохранения в БД');
            }

        } catch (Exception $e) {
            header('Content-Type: text/xml; charset=utf-8');
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            echo '<response>';
            echo '<status>error</status>';
            echo '<message>Ошибка при сохранении комментария: ' . htmlspecialchars($e->getMessage()) . '</message>';
            echo '</response>';
            exit;
        }
    }
}
?>