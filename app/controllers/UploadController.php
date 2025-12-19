<?php
class UploadController extends Controller {
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

        header('Location: /upload');
        exit();
    }

    public function downloadBackup() {
        // Получаем имя файла из GET-параметра
        $filename = $_GET['filename'] ?? '';

        if (empty($filename)) {
            // Если файл не указан, показываем список
            header('Location: /upload');
            exit();
        }

        // Безопасная проверка имени файла
        $filename = basename($filename);
        $filepath = 'backup/' . $filename;

        // Проверяем, что файл существует и имеет правильное расширение
        $allowedExtensions = ['inc', 'txt', 'bak'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (file_exists($filepath) &&
            is_file($filepath) &&
            in_array($extension, $allowedExtensions)) {

            // Устанавливаем заголовки для скачивания
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));

            // Очищаем буфер вывода
            if (ob_get_level()) {
                ob_end_clean();
            }

            readfile($filepath);
            exit();
        } else {
            // Если файл не найден, перенаправляем с сообщением об ошибке
            $_SESSION['upload_result'] = [
                'success' => false,
                'message' => 'Файл резервной копии не найден: ' . htmlspecialchars($filename)
            ];
            header('Location: /upload');
            exit();
        }
    }

    public function restoreBackup() {
        $this->loadModel('UploadModel');

        // Получаем имя файла из GET-параметра
        $filename = $_GET['filename'] ?? '';

        if (empty($filename)) {
            $_SESSION['upload_result'] = [
                'success' => false,
                'message' => 'Не указано имя файла для восстановления'
            ];
            header('Location: /upload');
            exit();
        }

        // Безопасная обработка имени файла
        $filename = basename($filename);
        $backupPath = 'backup/' . $filename;
        $targetFile = 'messages.inc';

        // Проверяем расширение файла
        $allowedExtensions = ['inc', 'txt', 'bak'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (file_exists($backupPath) &&
            is_file($backupPath) &&
            in_array($extension, $allowedExtensions)) {

            // Создаем резервную копию текущего файла
            if (file_exists($targetFile)) {
                $currentBackup = 'backup/messages_before_restore_' . date('Y-m-d_His') . '.inc';
                copy($targetFile, $currentBackup);
            }

            // Восстанавливаем из резервной копии
            if (copy($backupPath, $targetFile)) {
                $_SESSION['upload_result'] = [
                    'success' => true,
                    'message' => 'Файл успешно восстановлен из резервной копии: ' . htmlspecialchars($filename)
                ];

                // Логируем восстановление
                $logMessage = date('Y-m-d H:i:s') . " | Восстановлен файл из резервной копии: " . $filename . PHP_EOL;
                if (!is_dir('logs')) {
                    mkdir('logs', 0755, true);
                }
                file_put_contents('logs/upload.log', $logMessage, FILE_APPEND);
            } else {
                $_SESSION['upload_result'] = [
                    'success' => false,
                    'message' => 'Ошибка при восстановлении файла из резервной копии'
                ];
            }
        } else {
            $_SESSION['upload_result'] = [
                'success' => false,
                'message' => 'Резервная копия не найдена или имеет недопустимое расширение: ' . htmlspecialchars($filename)
            ];
        }

        header('Location: /upload');
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
            header('Location: /upload');
            exit();
        }
    }
}
?>