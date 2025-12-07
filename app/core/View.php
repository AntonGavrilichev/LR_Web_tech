<?php
// app/core/View.php
class View {
    private $data = [];

    public function __construct() {
        // Инициализация
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __get($name) {
        return $this->data[$name] ?? null;
    }

    public function render($template, $data = []) {
        // Объединяем данные
        $this->data = array_merge($this->data, $data);

        // Извлекаем переменные для шаблона
        extract($this->data);
        ob_start();
        include 'app/views/' . $template . '.php';
        $content = ob_get_clean();

        // Сохраняем контент в данных
        $this->data['content'] = $content;

        // Извлекаем данные снова
        extract($this->data);

        // Включаем основной layout
        include 'app/views/layouts/main.php';
    }
}
?>