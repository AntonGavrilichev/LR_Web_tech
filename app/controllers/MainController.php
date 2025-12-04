<?php
class MainController extends Controller {
    public function index() {
        $data = [
            'title' => 'Главная страница',
            'pageTitle' => 'Персональный сайт - Главная страница',
            'fullName' => 'Гавриличев Антон Александрович',
            'group' => 'ИСб-22-1-з',
            'photo' => 'photos/SevSu.jpg'
        ];

        $this->view->render('main/index', $data);
    }
}
?>