<?php
class PhotoModel extends Model {
    public function getPhotos() {
        return [
            ['id' => 1, 'name' => 'Горы', 'file' => 'photo1.jpg', 'description' => 'Красивые горные вершины'],
            ['id' => 2, 'name' => 'Море', 'file' => 'photo2.jpg', 'description' => 'Морской пейзаж на закате'],
            ['id' => 3, 'name' => 'Лес', 'file' => 'photo3.jpg', 'description' => 'Лесная тропинка'],
            ['id' => 4, 'name' => 'Город', 'file' => 'photo4.jpg', 'description' => 'Ночной город'],
            ['id' => 5, 'name' => 'Закат', 'file' => 'photo5.jpg', 'description' => 'Закат на берегу'],
            ['id' => 6, 'name' => 'Рассвет', 'file' => 'photo6.jpg', 'description' => 'Утренний рассвет'],
            ['id' => 7, 'name' => 'Река', 'file' => 'photo7.jpg', 'description' => 'Горная река'],
            ['id' => 8, 'name' => 'Озеро', 'file' => 'photo8.jpg', 'description' => 'Озеро в лесу'],
            ['id' => 9, 'name' => 'Пляж', 'file' => 'photo9.jpg', 'description' => 'Тропический пляж'],
            ['id' => 10, 'name' => 'Водопад', 'file' => 'photo10.jpg', 'description' => 'Большой водопад'],
            ['id' => 11, 'name' => 'Пустыня', 'file' => 'photo11.jpg', 'description' => 'Песчаные дюны'],
            ['id' => 12, 'name' => 'Снег', 'file' => 'photo12.jpg', 'description' => 'Зимний лес'],
            ['id' => 13, 'name' => 'Цветы', 'file' => 'photo13.jpg', 'description' => 'Поле цветов'],
            ['id' => 14, 'name' => 'Архитектура', 'file' => 'photo14.jpg', 'description' => 'Старая архитектура'],
            ['id' => 15, 'name' => 'Животные', 'file' => 'photo15.jpg', 'description' => 'Дикие животные']
        ];
    }
}
?>