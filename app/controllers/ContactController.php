<?php
class ContactController extends Controller {
    public function index() {
        $errors = [];
        $success = false;
        $formData = [];

        // Проверяем отправку формы
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Загружаем валидатор
            require_once 'app/models/validators/FormValidation.php';
            $validator = new FormValidation();

            // Устанавливаем правила валидации
            $validator->setRule('full_name', 'isNotEmpty');
            $validator->setRule('email', 'isEmail');
            $validator->setRule('message', 'isNotEmpty');

            // Валидируем данные
            if ($validator->validate($_POST)) {
                // Сохраняем данные формы
                $formData = $_POST;

                // Формируем сообщение для email
                $message = "Новое сообщение с персонального сайта:\n\n";
                $message .= "ФИО: " . $_POST['full_name'] . "\n";
                $message .= "Пол: " . ($_POST['gender'] == 'male' ? 'Мужской' : 'Женский') . "\n";
                $message .= "Возраст: " . $_POST['age'] . " лет\n";
                $message .= "Email: " . $_POST['email'] . "\n";
                $message .= "Сообщение:\n" . $_POST['message'] . "\n";

                // Настройки email
                $to = "asdf.qwerty.7331@gmail.com";
                $subject = "Сообщение с персонального сайта";
                $headers = "From: " . $_POST['email'] . "\r\n";
                $headers .= "Reply-To: " . $_POST['email'] . "\r\n";
                $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

                 //не рботает так как стоит просто заглушка
//                 if (mail($to, $subject, $message, $headers)) {
//                     $success = true;
//                 } else {
//                     $errors['email_send'] = "Ошибка отправки сообщения";
//                 }

                // Для демонстрации всегда успех
                $success = true;

                // Очищаем форму после успешной отправки
                if ($success) {
                    $formData = [];
                }
            } else {
                // Сохраняем ошибки
                $errors = $validator->errors;
                // Сохраняем введенные данные
                $formData = $_POST;
            }
        }

        $data = [
            'title' => 'Контакт',
            'pageTitle' => 'Персональный сайт - Контакт',
            'errors' => $errors,
            'success' => $success,
            'formData' => $formData
        ];

        $this->view->render('contact/index', $data);
    }
}
?>