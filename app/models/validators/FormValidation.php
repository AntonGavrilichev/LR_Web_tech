<?php
class FormValidation {
    protected $rules = [];
    public $errors = [];

    // Существующие методы
    public function isNotEmpty($data) {
        return empty(trim($data)) ? "Поле не может быть пустым" : null;
    }

    public function isInteger($data) {
        return !ctype_digit(strval($data)) ? "Значение должно быть целым числом" : null;
    }

    public function isLess($data, $value) {
        $intError = $this->isInteger($data);
        if ($intError) return $intError;

        return (intval($data) >= $value) ? "Значение должно быть меньше $value" : null;
    }

    public function isGreater($data, $value) {
        $intError = $this->isInteger($data);
        if ($intError) return $intError;

        return (intval($data) <= $value) ? "Значение должно быть больше $value" : null;
    }

    public function isEmail($data) {
        if (empty(trim($data))) {
            return "Email не может быть пустым";
        }
        return !filter_var($data, FILTER_VALIDATE_EMAIL) ? "Некорректный email адрес" : null;
    }

    public function setRule($field_name, $validator_name) {
        $this->rules[$field_name] = $validator_name;
    }

    public function validate($post_array) {
        $this->errors = [];

        foreach ($this->rules as $field => $validator) {
            $value = $post_array[$field] ?? '';

            if (is_callable([$this, $validator])) {
                $error = $this->$validator($value);
                if ($error) {
                    $this->errors[$field] = $error;
                }
            }
        }

        return empty($this->errors);
    }

    public function showErrors() {
        if (!empty($this->errors)) {
            echo '<div class="errors">';
            echo '<h3>Обнаружены ошибки:</h3>';
            foreach ($this->errors as $field => $error) {
                echo '<p class="error">' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
        }
    }

    // НОВЫЕ МЕТОДЫ ДЛЯ БЛОГА
    // Метод для проверки блога
    public function validateBlogPost($data, $files = []) {
        $this->errors = [];

        // Проверка заголовка
        if (empty(trim($data['title'] ?? ''))) {
            $this->errors['title'] = "Тема сообщения обязательна для заполнения";
        } elseif (strlen(trim($data['title'])) < 3) {
            $this->errors['title'] = "Тема сообщения должна содержать минимум 3 символа";
        } elseif (strlen(trim($data['title'])) > 255) {
            $this->errors['title'] = "Тема сообщения должна содержать максимум 255 символов";
        }

        // Проверка содержания
        if (empty(trim($data['content'] ?? ''))) {
            $this->errors['content'] = "Текст сообщения обязателен для заполнения";
        } elseif (strlen(trim($data['content'])) < 10) {
            $this->errors['content'] = "Текст сообщения должен содержать минимум 10 символов";
        }

        // Проверка файла (если загружен)
        if (isset($files['image']) && $files['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $this->validateImage($files['image']);
        }

        return empty($this->errors);
    }

    // Метод для проверки изображения
    public function validateImage($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors['image'] = 'Ошибка при загрузке файла';
            return false;
        }

        // Проверка размера (5MB максимум)
        if ($file['size'] > 5 * 1024 * 1024) {
            $this->errors['image'] = 'Размер файла не должен превышать 5MB';
            return false;
        }

        // Проверка типа
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($file['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $this->errors['image'] = 'Допустимы только изображения (JPEG, PNG, GIF, WebP)';
            return false;
        }

        return true;
    }

    // Показать ошибки в стиле вашего проекта
    public function showBlogErrors() {
        if (!empty($this->errors)) {
            echo '<div class="error-messages" style="background: #f8d7da; padding: 15px; border-radius: 5px; margin: 15px 0;">';
            echo '<h4 style="color: #721c24; margin-top: 0;">Обнаружены ошибки:</h4>';
            foreach ($this->errors as $field => $error) {
                echo '<p style="color: #721c24; margin: 5px 0;">' . htmlspecialchars($error) . '</p>';
            }
            echo '</div>';
        }
    }
}
?>