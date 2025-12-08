<?php
class AdminLoginController extends Controller {

    public function __construct() {
        parent::__construct();
        // Для страницы логина не проверяем авторизацию
    }

    // Форма входа администратора (п.4 задания)
    public function login() {
        if ($this->isAdmin()) {
            header('Location: /admin/statistics');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';

            // Проверка логина и пароля (хеш для пароля "qwerty")
            if ($login === 'admin@gmail.com' && md5($password) === 'd8578edf8458ce06fbc5bb76a58c5ca4') {
                $_SESSION['isAdmin'] = 1;
                $_SESSION['admin_login'] = $login;
                header('Location: /admin/statistics');
                exit;
            } else {
                $error = 'Неверный логин или пароль';
            }
        }

        $data = [
            'title' => 'Вход администратора',
            'pageTitle' => 'Административная панель - Вход',
            'error' => $error
        ];

        $this->view->render('admin/login', $data);
    }
    public function logout() {
        unset($_SESSION['isAdmin']);
        unset($_SESSION['admin_login']);
        session_destroy();
        header('Location: /admin/login');
        exit;
    }
}
?>