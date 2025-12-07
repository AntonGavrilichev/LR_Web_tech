<?php
class Controller {
    protected $model;
    protected $view;

    public function __construct() {
        $this->view = new View();
    }

    protected function loadModel($name) {
        $modelFile = 'app/models/' . $name . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            $modelName = $name;
            $this->model = new $modelName();
        }
    }
}
?>