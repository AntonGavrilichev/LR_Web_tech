<?php
class PhotoModel extends Model {
    public function getPhotos() {
        return [
            ['id' => 1, 'name' => 'Горы', 'filename' => 'mountain.jpg', 'description' => 'Красивые горные вершины'],
            ['id' => 2, 'name' => 'Море', 'filename' => 'sunrise.jpg', 'description' => 'Морской пейзаж'],
            ['id' => 3, 'name' => 'Лес', 'filename' => 'forest.jpg', 'description' => 'Лес летом'],
            ['id' => 4, 'name' => 'Город', 'filename' => 'city.webp', 'description' => 'Город Севастополь'],
            ['id' => 5, 'name' => 'Закат', 'filename' => 'sunset.jpg', 'description' => 'Закат на берегу'],
            ['id' => 6, 'name' => 'Рассвет', 'filename' => 'sunrise.jpg', 'description' => 'Утренний рассвет'],
            ['id' => 7, 'name' => 'Река', 'filename' => 'river.jpg', 'description' => 'Горная река'],
            ['id' => 8, 'name' => 'Озеро', 'filename' => 'lake.jpg', 'description' => 'Озеро в лесу'],
            ['id' => 9, 'name' => 'Пляж', 'filename' => 'beach.jpg', 'description' => 'Тропический пляж']
        ];
    }
}
?>