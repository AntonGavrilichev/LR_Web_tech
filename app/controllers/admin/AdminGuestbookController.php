<?php

class AdminGuestbookController extends Controller
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

    public function upload()
    {
        $data = [
            'title' => 'Загрузка гостевой книги',
            'pageTitle' => 'Загрузка гостевой книги - Админ панель'
        ];

        $this->view->render('guestbook/guestbook', $data);
    }
}

?>