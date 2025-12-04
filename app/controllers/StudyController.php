<?php
class StudyController extends Controller {
    public function index() {
        $this->loadModel('StudyModel');

        $data = [
            'title' => 'Учеба',
            'pageTitle' => 'Персональный сайт - Учеба',
            'university' => 'Национальный исследовательский университет "МЭИ"',
            'department' => 'Кафедра информационных систем',
            'subjects' => $this->model->getSubjects()
        ];

        $this->view->render('study/index', $data);
    }
}
?>