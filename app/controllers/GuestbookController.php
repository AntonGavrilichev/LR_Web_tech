<?php
class GuestbookController extends Controller {
    public function index() {
        $this->loadModel('GuestbookModel');

        // Обработка отправки формы
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleFormSubmission();
        }

        $data = [
            'title' => 'Гостевая книга',
            'pageTitle' => 'Гостевая книга - Персональный сайт',
            'messages' => $this->model->getMessages(),
            'formData' => $_SESSION['form_data'] ?? [],
            'formErrors' => $_SESSION['form_errors'] ?? []
        ];

        // Очищаем данные формы после использования
        unset($_SESSION['form_data'], $_SESSION['form_errors']);

        $this->view->render('guestbook/index', $data);
    }

    private function handleFormSubmission() {
        $data = [
            'last_name' => $_POST['last_name'] ?? '',
            'first_name' => $_POST['first_name'] ?? '',
            'patronymic' => $_POST['patronymic'] ?? '',
            'email' => $_POST['email'] ?? '',
            'message' => $_POST['message'] ?? ''
        ];

        $errors = $this->validateForm($data);

        if (empty($errors)) {
            if ($this->model->saveMessage($data)) {
                $_SESSION['success_message'] = 'Ваше сообщение успешно добавлено!';
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
            } else {
                $_SESSION['form_errors'] = ['Ошибка сохранения сообщения'];
            }
        } else {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $data;
        }
    }

    private function validateForm($data) {
        $errors = [];

        if (empty($data['last_name'])) {
            $errors[] = 'Поле "Фамилия" обязательно для заполнения';
        }

        if (empty($data['first_name'])) {
            $errors[] = 'Поле "Имя" обязательно для заполнения';
        }

        if (empty($data['email'])) {
            $errors[] = 'Поле "E-mail" обязательно для заполнения';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Введите корректный E-mail';
        }

        if (empty($data['message'])) {
            $errors[] = 'Поле "Текст отзыва" обязательно для заполнения';
        } elseif (strlen($data['message']) < 10) {
            $errors[] = 'Сообщение должно содержать не менее 10 символов';
        }

        return $errors;
    }
}
?>