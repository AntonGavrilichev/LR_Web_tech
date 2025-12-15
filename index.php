<?php
// В начале index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_error.log');
session_start();

// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// === ВАЖНО: ПЕРВАЯ ПРОВЕРКА - СТАТИЧЕСКИЕ ФАЙЛЫ ===
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// Если запрос идет к статическому файлу - обрабатываем сразу
if (preg_match('/\.(jpg|jpeg|png|gif|webp|ico|svg|css|js)$/i', $requestUri)) {
    // Убираем параметры запроса если есть
    $cleanUri = strtok($requestUri, '?');
    $filePath = __DIR__ . $cleanUri;

    if (file_exists($filePath) && is_file($filePath)) {
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'ico' => 'image/x-icon',
        ];

        if (isset($mimeTypes[$ext])) {
            header('Content-Type: ' . $mimeTypes[$ext]);
        } else {
            $mime = mime_content_type($filePath);
            if ($mime) {
                header('Content-Type: ' . $mime);
            }
        }

        readfile($filePath);
        exit;
    }
}
// === КОНЕЦ ПРОВЕРКИ СТАТИЧЕСКИХ ФАЙЛОВ ===

// Определяем базовый путь
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath == '/') {
    $basePath = '';
}

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
        'app/controllers/admin/' . $className . '.php',
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