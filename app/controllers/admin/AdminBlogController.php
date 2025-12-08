<?php

class AdminBlogController extends Controller
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

    public function edit()
    {
        $data = [
            'title' => 'Редактор блога',
            'pageTitle' => 'Редактор блога - Админ панель'
        ];

        $this->view->render('admin/blog_edit', $data);
    }
}

?>