<?php
class FormValidation {
    protected $rules = [];
    protected $errors = [];

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
}
?>