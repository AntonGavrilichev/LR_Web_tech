<?php
class StudyController extends Controller {
    public function index() {
        $this->loadModel('StudyModel');

        $data = [
            'title' => 'Учеба',
            'pageTitle' => 'Персональный сайт - Учеба',
            'university' => 'Севастопольский государственный университет',
            'department' => 'Кафедра информационных систем и технологий',
            'subjects' => $this->model->getSubjects()
        ];

        $this->view->render('study/index', $data);
    }
}
?>