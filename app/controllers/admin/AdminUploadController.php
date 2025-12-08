<?php

class AdminUploadController extends Controller
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

        header('Location: /admin/blog/upload'); // Изменено с /admin/upload
        exit();
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
            header('Location: /admin/blog/upload'); // Изменено с /admin/upload
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

        header('Location: /admin/blog/upload'); // Изменено с /admin/upload
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
            header('Location: /admin/blog/upload'); // Изменено с /admin/upload
            exit();
        }
    }
}
?>