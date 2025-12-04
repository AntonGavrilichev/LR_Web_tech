<?php
class TestController extends Controller {
    public function index() {
        $data = [
            'title' => 'Тест по дисциплине',
            'pageTitle' => 'Тест по Теории вероятностей и математической статистике',
            'discipline' => 'Теория вероятностей и математическая статистика'
        ];

        $this->view->render('test/index', $data);
    }
}
?>