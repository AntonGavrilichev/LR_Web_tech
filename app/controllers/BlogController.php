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

    public function posts() {
        // Загружаем модель
        $this->loadModel('BlogModel');

        // Получаем номер страницы
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        // Получаем данные
        $blogData = $this->model->getPosts($page);

        // Подготавливаем данные для представления
        $data = [
            'title' => 'Записи блога',
            'pageTitle' => 'Персональный сайт - Записи блога',
            'posts' => $blogData['posts'],
            'page' => $blogData['page'],
            'totalPages' => $blogData['totalPages'],
            'total' => $blogData['total']
        ];

        // Рендерим представление для записей
        $this->view->render('blog/posts', $data);
    }
    public function upload() {
        // Данные для представления загрузки
        $data = [
            'title' => 'Загрузка CSV',
            'pageTitle' => 'Персональный сайт - Загрузка CSV'
        ];

        $this->view->render('blog/upload_blog', $data);
    }

    public function uploadCsv() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /blog/upload_blog');
            exit;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Загружаем модель
        $this->loadModel('BlogModel');

        // Валидация файла
        $validation = $this->validateCsvFile($_FILES['csv_file']);

        if (!$validation['success']) {
            $_SESSION['error_message'] = $validation['error'];
            header('Location: /blog/upload_blog');
            exit;
        }

        // Обработка CSV файла
        $onError = $_POST['on_error'] ?? 'stop';
        $results = $this->processCsvFile($validation['file_path'], $onError);

        // Подготовка данных для отображения результатов
        $data = [
            'title' => 'Загрузка CSV - Результаты',
            'pageTitle' => 'Персональный сайт - Результаты загрузки CSV',
            'results' => $results
        ];

        $this->view->render('blog/upload_blog', $data);
    }

    private function validateCsvFile($file) {
        $result = ['success' => false, 'error' => '', 'file_path' => null];

        // Проверка наличия файла
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $result['error'] = 'Ошибка при загрузке файла';
            return $result;
        }

        // Проверка размера (макс 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $result['error'] = 'Размер файла не должен превышать 5MB';
            return $result;
        }

        // Проверка расширения
        $allowedExtensions = ['csv', 'txt'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            $result['error'] = 'Допустимы только файлы CSV или TXT';
            return $result;
        }


        $allowedMimeTypes = ['text/csv', 'text/plain', 'application/csv', 'text/comma-separated-values'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimeTypes)) {
            $result['error'] = 'Недопустимый тип файла';
            return $result;
        }

        $result['success'] = true;
        $result['file_path'] = $file['tmp_name'];

        return $result;
    }

    private function processCsvFile($filePath, $onError = 'stop') {
        $results = [
            'total_processed' => 0,
            'successful' => 0,
            'failed' => 0,
            'errors' => []
        ];

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            $results['errors'][0] = 'Не удалось открыть файл';
            return $results;
        }

        // Пропускаем заголовок если есть
        $firstRow = fgetcsv($handle);
        $hasHeader = $this->isHeaderRow($firstRow);

        if (!$hasHeader) {
            // Если первая строка не заголовок, возвращаемся к началу
            rewind($handle);
        }

        $rowNumber = $hasHeader ? 1 : 0;
        $stopOnError = ($onError === 'stop');

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $results['total_processed']++;

            // Пропускаем пустые строки
            if (count($row) === 1 && empty($row[0])) {
                continue;
            }

            // Валидация данных строки
            $validation = $this->validateCsvRow($row, $rowNumber);

            if (!$validation['valid']) {
                $results['failed']++;
                $results['errors'][$rowNumber] = $validation['error'];

                if ($stopOnError) {
                    break;
                }
                continue;
            }

            // Сохранение записи в БД
            try {
                $success = $this->model->savePost(
                    $validation['data']['title'],
                    $validation['data']['message'],
                    $validation['data']['author'],
                    null, // image_path - не поддерживается в CSV
                    $validation['data']['created_at']
                );

                if ($success) {
                    $results['successful']++;
                } else {
                    $results['failed']++;
                    $results['errors'][$rowNumber] = 'Ошибка при сохранении в БД';

                    if ($stopOnError) {
                        break;
                    }
                }
            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][$rowNumber] = 'Ошибка БД: ' . $e->getMessage();

                if ($stopOnError) {
                    break;
                }
            }
        }

        fclose($handle);

        return $results;
    }

    private function isHeaderRow($row) {
        if (!is_array($row) || count($row) < 4) {
            return false;
        }

        // Проверяем, содержит ли строка названия полей
        $headerKeywords = ['title', 'theme', 'message', 'content', 'author', 'date', 'created'];

        foreach ($row as $cell) {
            $cellLower = strtolower(trim($cell));
            foreach ($headerKeywords as $keyword) {
                if (strpos($cellLower, $keyword) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    private function validateCsvRow($row, $rowNumber) {
        $result = ['valid' => false, 'error' => '', 'data' => []];

        // Проверка количества полей
        if (count($row) < 4) {
            $result['error'] = 'Недостаточно полей (ожидается 4 поля)';
            return $result;
        }

        // Очистка данных
        $title = trim($row[0]);
        $message = trim($row[1]);
        $author = trim($row[2]);
        $createdAt = trim($row[3]);

        // Валидация заголовка
        if (empty($title)) {
            $result['error'] = 'Поле "title" не может быть пустым';
            return $result;
        }

        if (strlen($title) < 3) {
            $result['error'] = 'Поле "title" должно содержать минимум 3 символа';
            return $result;
        }

        if (strlen($title) > 255) {
            $result['error'] = 'Поле "title" должно содержать максимум 255 символов';
            return $result;
        }

        // Валидация сообщения
        if (empty($message)) {
            $result['error'] = 'Поле "message" не может быть пустым';
            return $result;
        }

        if (strlen($message) < 10) {
            $result['error'] = 'Поле "message" должно содержать минимум 10 символов';
            return $result;
        }

        // Обработка автора
        if (empty($author)) {
            $author = 'Аноним';
        }

        // Обработка даты
        if (empty($createdAt)) {
            $createdAt = date('Y-m-d H:i:s');
        } else {
            // Парсинг даты
            $parsedDate = DateTime::createFromFormat('Y-m-d H:i', $createdAt);
            if (!$parsedDate) {
                $parsedDate = DateTime::createFromFormat('Y-m-d', $createdAt);
                if ($parsedDate) {
                    $parsedDate->setTime(0, 0, 0);
                }
            }

            if (!$parsedDate) {
                $result['error'] = 'Неверный формат даты. Используйте YYYY-MM-DD HH:MM или YYYY-MM-DD';
                return $result;
            }

            $createdAt = $parsedDate->format('Y-m-d H:i:s');
        }

        $result['valid'] = true;
        $result['data'] = [
            'title' => $title,
            'message' => $message,
            'author' => $author,
            'created_at' => $createdAt
        ];

        return $result;
    }

}
?>