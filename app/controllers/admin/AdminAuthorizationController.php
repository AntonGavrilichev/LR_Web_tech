<?php

class AdminAuthorizationController extends AdminController
{

    // Переопределяем конструктор для страницы логина
    function __construct()
    {
        parent::__construct();
        // Для страницы логина не требуем аутентификации
        if ($_REQUEST['action'] == 'login' || $_REQUEST['action'] == 'auth') {
            // Не вызываем authenticate() для этих действий
        }
    }

    function login()
    {
        session_start();
        // Если уже авторизован, редирект в админку
        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) {
            header('Location: /admin/statistics/view');
            exit;
        }

        $data = array(
            'title' => 'Вход администратора'
        );
        $this->view->render('admin_login.php', 'admin_layout.php', $data);
    }

    function auth()
    {
        session_start();
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';

        // Проверка логина и пароля (md5 хеш пароля "qwerty")
        if ($login == 'admin@gmail.com' && md5($password) == 'd8578edf8458ce06fbc5bb76a58c5ca4') {
            $_SESSION['isAdmin'] = 1;
            $_SESSION['admin_login'] = $login;
            header('Location: /admin/statistics/view');
            exit;
        } else {
            $data = array(
                'title' => 'Вход администратора',
                'error' => 'Неверный логин или пароль'
            );
            $this->view->render('admin_login.php', 'admin_layout.php', $data);
        }
    }
}