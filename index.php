<?php

session_start();

// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Определяем базовый путь
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath == '/') {
    $basePath = '';
}

// Получаем URL
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// Убираем базовый путь из URL
if (strpos($requestUri, $scriptName) === 0) {
    $requestUri = substr($requestUri, strlen($scriptName));
}

// Убираем параметры запроса
$requestUri = strtok($requestUri, '?');

// Убираем начальные и конечные слеши
$requestUri = trim($requestUri, '/');

// Устанавливаем маршрут
$url = !empty($requestUri) ? $requestUri : '';
$_GET['url'] = $url;

// Автозагрузка классов
spl_autoload_register(function($className) {
    $paths = [
        'app/core/' . $className . '.php',
        'app/controllers/' . $className . '.php',
        'app/models/' . $className . '.php',
        'app/models/validators/' . $className . '.php'
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }

    // Если класс не найден
    die("Класс $className не найден");
});

// Проверяем наличие роутера
if (!file_exists('app/core/Router.php')) {
    die("Файл роутера не найден: app/core/Router.php");
}

// Запускаем роутер
try {
    $router = new Router();
    $router->route($url);
} catch (Exception $e) {
    echo "<h1>Ошибка: " . htmlspecialchars($e->getMessage()) . "</h1>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>