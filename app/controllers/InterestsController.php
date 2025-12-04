<?php
class InterestsController extends Controller {
    public function index() {
        $this->loadModel('InterestModel');

        $data = [
            'title' => 'Мои интересы',
            'pageTitle' => 'Персональный сайт - Мои интересы',
            'interests' => $this->model->getInterests()
        ];

        $this->view->render('interests/index', $data);
    }
}
?>