<?php
class InterestModel extends Model {
    public function getInterests() {
        return [
            [
                'id' => 'hobby',
                'title' => 'Мое хобби',
                'description' => 'Программирование, чтение книг, видеоигры, фотография, путешествия. Люблю изучать новые технологии и языки программирования.'
            ],
            [
                'id' => 'books',
                'title' => 'Мои любимые книги',
                'description' => '
                    <ul>
                        <li><strong>"Совершенный код"</strong> - Стив Макконнелл</li>
                        <li><strong>"Чистый код"</strong> - Роберт Мартин</li>
                        <li><strong>"Гарри Поттер"</strong> - Дж.К. Роулинг</li>
                        <li><strong>"451° по Фаренгейту"</strong> - Рэй Брэдбери</li>
                        <li><strong>"Мастер и Маргарита"</strong> - Михаил Булгаков</li>
                    </ul>'
            ],
            [
                'id' => 'music',
                'title' => 'Моя любимая музыка',
                'description' => 'Рок, классическая музыка, саундтреки к фильмам, джаз. Любимые исполнители: Queen, Pink Floyd, Ludovico Einaudi.'
            ],
            [
                'id' => 'movies',
                'title' => 'Мои любимые фильмы',
                'description' => '
                    <ol>
                        <li><strong>"Начало"</strong> (Кристофер Нолан)</li>
                        <li><strong>"Интерстеллар"</strong> (Кристофер Нолан)</li>
                        <li><strong>"Матрица"</strong> (Братья Вачовски)</li>
                        <li><strong>"Побег из Шоушенка"</strong> (Фрэнк Дарабонт)</li>
                        <li><strong>"Форрест Гамп"</strong> (Роберт Земекис)</li>
                    </ol>'
            ],
            [
                'id' => 'sports',
                'title' => 'Спорт',
                'description' => 'Футбол, плавание, настольный теннис, горные лыжи. Регулярно занимаюсь в тренажерном зале.'
            ]
        ];
    }
}
?>