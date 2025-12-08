<?php

class AdminController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->authenticate();
    }

    // Проверка авторизации администратора (п.5 задания)
    protected function authenticate()
    {
        if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
            header('Location: /admin/login');
            exit;
        }
    }

    // Метод для выхода администратора
    public function logout()
    {
        unset($_SESSION['isAdmin']);
        unset($_SESSION['admin_login']);
        session_destroy();
        header('Location: /admin/login');
        exit;
    }
}

?>