<?php
class MainController extends Controller {
    public function index() {
        $data = [
            'title' => 'Главная страница',
            'pageTitle' => 'Персональный сайт - Главная страница',
            'fullName' => 'Иванов Иван Иванович',
            'group' => 'ИС-21',
            'labWork' => 'Лабораторная работа №8: Разработка MVC приложения на PHP',
            'photo' => 'photos/profile-img.jpg'
        ];

        $this->view->render('main/index', $data);
    }
}
?>