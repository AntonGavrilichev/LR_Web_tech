<h1>Главная страница</h1>

<div class="profile">
    <img src="<?= $photo ?? 'https://via.placeholder.com/200' ?>" alt="Фото профиля" class="profile-img">
    <div class="profile-info">
        <h2><?= htmlspecialchars($fullName ?? 'Иванов Иван Иванович') ?></h2>
        <p><strong>Группа:</strong> <?= htmlspecialchars($group ?? 'ИС-21') ?></p>
        <p><strong>Лабораторная работа:</strong> <?= htmlspecialchars($labWork ?? 'Лабораторная работа №8: Разработка MVC приложения на PHP') ?></p>
        <p>Добро пожаловать на мой персональный сайт! Этот сайт разработан в рамках лабораторной работы по веб-технологиям с использованием архитектуры MVC на PHP.</p>
    </div>
</div>