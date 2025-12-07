<?php
class BlogController extends Controller {

    public function index() {
        // Загружаем модель
        $this->loadModel('BlogModel');

        // Получаем номер страницы
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        // Получаем данные
        $blogData = $this->model->getPosts($page);

        // Подготавливаем данные для представления
        $data = [
            'title' => 'Редактор блога',
            'pageTitle' => 'Персональный сайт - Редактор блога',
            'posts' => $blogData['posts'],
            'page' => $blogData['page'],
            'totalPages' => $blogData['totalPages'],
            'total' => $blogData['total']
        ];

        // Если есть ошибки из сессии
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['form_errors'])) {
            $data['errors'] = $_SESSION['form_errors'];
            unset($_SESSION['form_errors']);
        }

        if (isset($_SESSION['form_data'])) {
            $data['old'] = $_SESSION['form_data'];
            unset($_SESSION['form_data']);
        }

        // Рендерим представление
        $this->view->render('blog/index', $data);
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

    private function handleFileUpload($file) {
        $uploadDir = 'public/uploads/blog/';

        // Создаем директорию если нет
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Генерируем уникальное имя
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filePath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return '/' . $filePath;
        }

        return false;
    }
}
?>