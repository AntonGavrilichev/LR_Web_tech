<h1>Главная страница</h1>

<div class="profile">
    <img src="<?= $photo ?? 'https://via.placeholder.com/200' ?>" alt="Фото профиля" class="profile-img">
    <div class="profile-info">
        <h2><?= htmlspecialchars($fullName ?? 'Гавриличев Антон Александрович') ?></h2>
        <p><strong>Группа:</strong> <?= htmlspecialchars($group ?? 'ИС/22-1-з') ?></p>
        <p><strong>Лабораторная работа:</strong> <?= htmlspecialchars($labWork ?? 'Лабораторная работа №9: Исследование возможностей хранения данных на стороне сервера. Работа с файлами. Работа с реляционными СУБД') ?></p>
        <p>Добро пожаловать на мой персональный сайт!</p>
    </div>
</div>