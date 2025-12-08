<?php

class AdminUploadController extends AdminController {

    public function __construct() {
        parent::__construct();
    }

    // Главная страница загрузки (перенесенная из UploadController)
    public function index() {
        $data = [
            'title' => 'Загрузка файлов',
            'pageTitle' => 'Административная панель - Загрузка файлов',
            'layout' => 'admin/layout'
        ];

        $this->view->render('admin/upload', $data);
    }

    // Загрузка файла
    public function upload() {
        $data = [
            'title' => 'Загрузка файла',
            'pageTitle' => 'Загрузка файла',
            'layout' => 'admin/layout'
        ];

        $this->view->render('admin/upload_form', $data);
    }

    // Скачать текущий файл
    public function downloadCurrent() {
        // Код из UploadController
        $this->checkAdminAccess();
        // ... остальной код скачивания
    }

    // Скачать backup
    public function downloadBackup($filename) {
        $this->checkAdminAccess();
        // ... код скачивания backup
    }

    // Восстановить backup
    public function restoreBackup($filename) {
        $this->checkAdminAccess();
        // ... код восстановления
    }
}
?>