<h1>Главная страница</h1>

<div class="profile">
    <img src="<?= $photo ?? 'https://via.placeholder.com/200' ?>" alt="Фото профиля" class="profile-img">
    <div class="profile-info">
        <h2><?= htmlspecialchars($fullName ?? 'Ячменев Алексей Анатольевич') ?></h2>
        <p><strong>Группа:</strong> <?= htmlspecialchars($group ?? 'ИС/22-1-з') ?></p>
        <p><strong>Лабораторная работа:</strong> <?= htmlspecialchars($labWork ?? 'Лабораторная работа №8: Исследование архитектуры MVC приложения и возможностей
обработки данных HTML-форм на стороне сервера с использованием языка PHP') ?></p>
        <p>Добро пожаловать на мой персональный сайт!</p>
    </div>
</div>