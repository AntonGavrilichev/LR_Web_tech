<?php

class AdminBlogController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkAdminAccess();
    }

    private function checkAdminAccess()
    {
        if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
            header('Location: /admin/login');
            exit;
        }
    }
    public function add() {
        // Только POST запросы
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /blog');
            exit;
        }

        // Начинаем сессию
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Валидация
        $errors = $this->validate($_POST, $_FILES);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header('Location: /blog');
            exit;
        }


        // Обработка файла
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->handleFileUpload($_FILES['image']);
            if (!$imagePath) {
                $_SESSION['error_message'] = 'Не удалось сохранить изображение';
                $_SESSION['form_data'] = $_POST;
                header('Location: /blog');
                exit;
            }
        }

        // Загружаем модель и сохраняем
        $this->loadModel('BlogModel');

        $author = !empty($_POST['author']) ? trim($_POST['author']) : 'Аноним';

        try {
            $result = $this->model->savePost(
                $_POST['title'],
                $_POST['content'],
                $author,
                $imagePath
            );

            if ($result) {
                $_SESSION['success_message'] = 'Запись успешно добавлена!';
            } else {
                $_SESSION['error_message'] = 'Ошибка при сохранении записи';
                $_SESSION['form_data'] = $_POST;
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Ошибка: ' . $e->getMessage();
            $_SESSION['form_data'] = $_POST;
        }

        header('Location: /blog');
        exit;
    }

    private function validate($post, $files) {
        $errors = [];

        // Проверка заголовка
        if (empty(trim($post['title'] ?? ''))) {
            $errors['title'] = 'Тема сообщения обязательна для заполнения';
        } elseif (strlen(trim($post['title'])) < 3) {
            $errors['title'] = 'Тема сообщения должна содержать минимум 3 символа';
        } elseif (strlen(trim($post['title'])) > 255) {
            $errors['title'] = 'Тема сообщения должна содержать максимум 255 символов';
        }

        // Проверка содержания
        if (empty(trim($post['content'] ?? ''))) {
            $errors['content'] = 'Текст сообщения обязателен для заполнения';
        } elseif (strlen(trim($post['content'])) < 10) {
            $errors['content'] = 'Текст сообщения должен содержать минимум 10 символов';
        }

        // Проверка файла
        if (isset($files['image']) && $files['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($files['image']['error'] !== UPLOAD_ERR_OK) {
                $errors['image'] = 'Ошибка при загрузке файла';
            } elseif ($files['image']['size'] > 5 * 1024 * 1024) {
                $errors['image'] = 'Размер файла не должен превышать 5MB';
            } else {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $fileType = mime_content_type($files['image']['tmp_name']);
                if (!in_array($fileType, $allowedTypes)) {
                    $errors['image'] = 'Допустимы только изображения (JPEG, PNG, GIF, WebP)';
                }
            }
        }

        return $errors;
    }
    public function edit()
    {
        $data = [
            'title' => 'Редактор блога',
            'pageTitle' => 'Редактор блога - Админ панель'
        ];

        $this->view->render('blog/blog_edit', $data);
    }

    public function index() {
        $this->loadModel('UploadModel');

        $data = [
            'title' => 'Загрузка сообщений гостевой книги',
            'pageTitle' => 'Загрузка файла messages.inc',
            'fileInfo' => $this->model->getFileInfo(),
            'backupFiles' => $this->model->getBackupFiles(),
            'uploadResult' => $_SESSION['upload_result'] ?? null
        ];

        // Очищаем результат загрузки после отображения
        if (isset($_SESSION['upload_result'])) {
            unset($_SESSION['upload_result']);
        }

        $this->view->render('upload/index', $data);
    }

    public function upload() {
        $this->loadModel('UploadModel');

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['guestbook_file'])) {
            $result = $this->model->uploadFile($_FILES['guestbook_file']);
            $_SESSION['upload_result'] = $result;
        }

        header('Location: /admin/blog/upload'); // Изменено с /upload
        exit();
    }
    public function delete() {
        if (!isset($_GET['id'])) {
            header('Location: /blog');
            exit;
        }

        // Начинаем сессию
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = (int)$_GET['id'];

        // Загружаем модель и удаляем
        $this->loadModel('BlogModel');

        try {
            if ($this->model->deletePost($id)) {
                $_SESSION['success_message'] = 'Запись успешно удалена!';
            } else {
                $_SESSION['error_message'] = 'Ошибка при удалении записи';
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Ошибка: ' . $e->getMessage();
        }

        // Редирект обратно на страницу, откуда пришли
        $redirect = $_GET['redirect'] ?? 'blog';
        $pageParam = isset($_GET['page']) ? '?page=' . $_GET['page'] : '';
        header('Location: /' . $redirect . $pageParam);
        exit;
    }
    public function downloadBackup($filename) {
        $filepath = 'backup/' . basename($filename);

        if (file_exists($filepath) && pathinfo($filepath, PATHINFO_EXTENSION) === 'inc') {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit();
        } else {
            header('Location: /admin/blog/upload');
            exit();
        }
    }

    public function restoreBackup($filename) {
        $this->loadModel('UploadModel');

        $backupPath = 'backup/' . basename($filename);
        $targetFile = 'messages.inc';

        if (file_exists($backupPath) && pathinfo($backupPath, PATHINFO_EXTENSION) === 'inc') {
            // Создаем резервную копию текущего файла
            if (file_exists($targetFile)) {
                $currentBackup = 'backup/messages_before_restore_' . date('Y-m-d_His') . '.inc';
                copy($targetFile, $currentBackup);
            }

            // Восстанавливаем из резервной копии
            if (copy($backupPath, $targetFile)) {
                $_SESSION['upload_result'] = [
                    'success' => true,
                    'message' => 'Файл успешно восстановлен из резервной копии'
                ];

                // Логируем восстановление
                $logMessage = date('Y-m-d H:i:s') . " | Восстановлен файл из резервной копии: " . basename($backupPath) . PHP_EOL;
                if (!is_dir('logs')) {
                    mkdir('logs', 0755, true);
                }
                file_put_contents('logs/upload.log', $logMessage, FILE_APPEND);
            } else {
                $_SESSION['upload_result'] = [
                    'success' => false,
                    'message' => 'Ошибка при восстановлении файла'
                ];
            }
        }

        header('Location: /admin/blog/upload'); // Изменено с /upload
        exit();
    }

    public function downloadCurrent() {
        $filepath = 'messages.inc';

        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="messages_current.inc"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit();
        } else {
            header('Location: /admin/blog/upload'); // Изменено с /upload
            exit();
        }
    }
}
?>