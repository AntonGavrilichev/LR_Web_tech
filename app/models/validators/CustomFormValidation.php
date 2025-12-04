<?php
class CustomFormValidation extends FormValidation {
    public function validateTestForm($data) {
        $this->errors = [];

        // Проверка ФИО
        if (empty($data['full_name'])) {
            $this->errors['full_name'] = "ФИО обязательно для заполнения";
        } elseif (strlen($data['full_name']) < 5) {
            $this->errors['full_name'] = "ФИО должно содержать минимум 5 символов";
        }

        // Проверка группы
        if (empty($data['group'])) {
            $this->errors['group'] = "Выберите группу";
        }

        // Проверка ответов на вопросы
        for ($i = 1; $i <= 3; $i++) {
            if (!isset($data["q$i"])) {
                $this->errors["q$i"] = "Ответьте на вопрос $i";
            }
        }

        return empty($this->errors);
    }
}
?>