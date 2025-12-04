<?php
class AlbumController extends Controller {
    public function index() {
        $this->loadModel('PhotoModel');

        $data = [
            'title' => 'Фотоальбом',
            'pageTitle' => 'Персональный сайт - Фотоальбом',
            'photos' => $this->model->getPhotos()
        ];

        $this->view->render('album/index', $data);
    }
}
?>