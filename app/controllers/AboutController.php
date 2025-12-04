<?php
class AboutController extends Controller {
    public function index() {
        $data = [
            'title' => 'Обо мне',
            'pageTitle' => 'Персональный сайт - Обо мне'
        ];

        $this->view->render('about/index', $data);
    }
}
?>