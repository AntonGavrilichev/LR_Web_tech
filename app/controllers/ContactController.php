<?php
class ContactController extends Controller {
    public function index() {
        $data = [
            'title' => 'Контакт',
            'pageTitle' => 'Персональный сайт - Контакт'
        ];

        $this->view->render('contact/index', $data);
    }
}
?>