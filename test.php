<?php
// test.php - для проверки базовых функций
echo '<h1>Тест работы PHP</h1>';
echo '<p>Версия PHP: ' . phpversion() . '</p>';

// Проверка структуры
echo '<h2>Проверка файловой структуры:</h2>';
$files = [
    'index.php',
    'app/core/Router.php',
    'app/core/Controller.php',
    'app/core/View.php',
    'app/controllers/MainController.php',
    'app/views/layouts/main.php',
    'app/views/main/index.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color:green;'>✅ $file существует</p>";
    } else {
        echo "<p style='color:red;'>❌ $file не найден</p>";
    }
}

// Проверка работы контроллера
echo '<h2>Проверка работы MainController:</h2>';
if (file_exists('app/controllers/MainController.php')) {
    require_once 'app/core/Controller.php';
    require_once 'app/core/View.php';
    require_once 'app/controllers/MainController.php';

    try {
        $controller = new MainController();
        echo "<p style='color:green;'>✅ MainController создан успешно</p>";

        // Проверяем метод index
        if (method_exists($controller, 'index')) {
            echo "<p style='color:green;'>✅ Метод index() существует</p>";
        } else {
            echo "<p style='color:red;'>❌ Метод index() не найден</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'>❌ Ошибка создания MainController: " . $e->getMessage() . "</p>";
    }
}

// Тестовые ссылки
echo '<h2>Тестовые ссылки:</h2>';
echo '<ul>';
$links = [
    '/' => 'Главная',
    '/about' => 'Обо мне',
    '/interests' => 'Мои интересы',
    '/study' => 'Учеба',
    '/album' => 'Фотоальбом',
    '/contact' => 'Контакт',
    '/test' => 'Тест'
];

foreach ($links as $url => $title) {
    echo "<li><a href='$url'>$title</a></li>";
}
echo '</ul>';

// Информация о сервере
echo '<h2>Информация о сервере:</h2>';
echo '<pre>';
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n";
echo "PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'N/A') . "\n";
echo "QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'N/A') . "\n";
echo '</pre>';
?>