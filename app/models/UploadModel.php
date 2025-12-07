<?php
class UploadModel extends Model {
    private $targetFile = 'messages.inc';
    private $allowedTypes = ['inc', 'txt'];
    private $maxFileSize = 2 * 1024 * 1024; // 2MB

    public function uploadFile($file) {
        $result = [
            'success' => false,
            'message' => '',
            'backup_path' => ''
        ];

        // Проверка наличия файла
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $result['message'] = 'Файл не был загружен';
            return $result;
        }

        // Проверка ошибок загрузки
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $result['message'] = $this->getUploadError($file['error']);
            return $result;
        }

        // Проверка размера файла
        if ($file['size'] > $this->maxFileSize) {
            $result['message'] = 'Размер файла превышает 2 МБ';
            return $result;
        }

        // Проверка расширения файла
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $this->allowedTypes)) {
            $result['message'] = 'Допустимы только файлы с расширениями: ' . implode(', ', $this->allowedTypes);
            return $result;
        }

        // Проверка содержимого файла (базовая валидация)
        $content = file_get_contents($file['tmp_name']);
        if (!$this->validateFileContent($content)) {
            $result['message'] = 'Неверный формат файла. Ожидается файл в формате messages.inc';
            return $result;
        }

        // Создаем резервную копию текущего файла
        $backupPath = '';
        if (file_exists($this->targetFile)) {
            $backupPath = 'backup/messages_' . date('Y-m-d_His') . '.inc';
            if (!is_dir('backup')) {
                mkdir('backup', 0755, true);
            }
            copy($this->targetFile, $backupPath);
        }

        // Загружаем новый файл
        if (move_uploaded_file($file['tmp_name'], $this->targetFile)) {
            $result['success'] = true;
            $result['message'] = 'Файл успешно загружен и заменен';
            $result['backup_path'] = $backupPath;

            // Логируем загрузку
            $this->logUpload($file['name'], $backupPath);
        } else {
            $result['message'] = 'Ошибка при сохранении файла';
            // Восстанавливаем из резервной копии при ошибке
            if ($backupPath && file_exists($backupPath)) {
                copy($backupPath, $this->targetFile);
            }
        }

        return $result;
    }

    public function getFileInfo() {
        $info = [
            'exists' => false,
            'size' => 0,
            'modified' => '',
            'lines' => 0,
            'messages_count' => 0
        ];

        if (file_exists($this->targetFile)) {
            $info['exists'] = true;
            $info['size'] = filesize($this->targetFile);
            $info['modified'] = date('d.m.Y H:i:s', filemtime($this->targetFile));

            $lines = file($this->targetFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $info['lines'] = count($lines);

            // Подсчет сообщений
            foreach ($lines as $line) {
                if (count(explode(';', $line)) >= 6) {
                    $info['messages_count']++;
                }
            }
        }

        return $info;
    }

    public function getBackupFiles() {
        $backups = [];

        if (is_dir('backup')) {
            $files = scandir('backup', SCANDIR_SORT_DESCENDING);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'inc') {
                    $filePath = 'backup/' . $file;
                    $backups[] = [
                        'name' => $file,
                        'path' => $filePath,
                        'size' => filesize($filePath),
                        'modified' => date('d.m.Y H:i:s', filemtime($filePath))
                    ];
                }
            }
        }

        return $backups;
    }

    private function validateFileContent($content) {
        $lines = explode(PHP_EOL, $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $parts = explode(';', $line);
                // Проверяем, что в строке хотя бы 6 частей (дата + ФИО + email + сообщение)
                if (count($parts) < 6) {
                    return false;
                }
            }
        }
        return true;
    }

    private function getUploadError($errorCode) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'Размер файла превышает максимально допустимый размер',
            UPLOAD_ERR_FORM_SIZE => 'Размер файла превышает указанный в форме',
            UPLOAD_ERR_PARTIAL => 'Файл был загружен только частично',
            UPLOAD_ERR_NO_FILE => 'Файл не был загружен',
            UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка',
            UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск',
            UPLOAD_ERR_EXTENSION => 'Расширение PHP остановило загрузку файла'
        ];

        return $errors[$errorCode] ?? 'Неизвестная ошибка загрузки';
    }

    private function logUpload($filename, $backupPath) {
        $logMessage = date('Y-m-d H:i:s') . " | Загружен файл: $filename";
        if ($backupPath) {
            $logMessage .= " | Создана резервная копия: " . basename($backupPath);
        }
        $logMessage .= PHP_EOL;

        if (!is_dir('logs')) {
            mkdir('logs', 0755, true);
        }

        file_put_contents('logs/upload.log', $logMessage, FILE_APPEND);
    }
}
?>