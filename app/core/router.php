<?php
class Router {
    private $routes = [
        '' => ['controller' => 'MainController', 'action' => 'index'],
        'about' => ['controller' => 'AboutController', 'action' => 'index'],
        'interests' => ['controller' => 'InterestsController', 'action' => 'index'],
        'study' => ['controller' => 'StudyController', 'action' => 'index'],
        'album' => ['controller' => 'AlbumController', 'action' => 'index'],
        'contact' => ['controller' => 'ContactController', 'action' => 'index'],
        'test' => ['controller' => 'TestController', 'action' => 'index'],
        'guestbook' => ['controller' => 'GuestbookController', 'action' => 'index'],
        'upload' => ['controller' => 'UploadController', 'action' => 'index'],
        'upload/upload' => ['controller' => 'UploadController', 'action' => 'upload', 'method' => 'POST'],
        'upload/downloadCurrent' => ['controller' => 'UploadController', 'action' => 'downloadCurrent'],
        'upload/downloadBackup/(:any)' => ['controller' => 'UploadController', 'action' => 'downloadBackup'],
        'upload/restoreBackup/(:any)' => ['controller' => 'UploadController', 'action' => 'restoreBackup'],
    ];

    public function route($url) {
        // Для отладки
        error_log("Router: URL получен = '$url'");

        // Если маршрут существует
        if (isset($this->routes[$url])) {
            $controllerName = $this->routes[$url]['controller'];
            $actionName = $this->routes[$url]['action'];

            error_log("Router: Найден маршрут - контроллер: $controllerName, действие: $actionName");

            $controllerFile = 'app/controllers/' . $controllerName . '.php';

            if (file_exists($controllerFile)) {
                require_once $controllerFile;

                if (class_exists($controllerName)) {
                    $controller = new $controllerName();

                    if (method_exists($controller, $actionName)) {
                        $controller->$actionName();
                        return;
                    } else {
                        $this->error("Метод $actionName не найден в контроллере $controllerName");
                    }
                } else {
                    $this->error("Класс $controllerName не найден");
                }
            } else {
                $this->error("Файл контроллера $controllerFile не найден");
            }
        } else {
            // Показываем список доступных маршрутов для отладки
            $this->debugPage($url);
        }
    }

    private function error($message) {
        http_response_code(500);
        echo "<h1>Ошибка роутера</h1>";
        echo "<p>$message</p>";
        echo "<p><a href='/'>Вернуться на главную</a></p>";
    }

    private function debugPage($requestedUrl) {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Отладка маршрутов</title>
            <style>
                body { font-family: Arial; padding: 20px; }
                .error { background: #f8d7da; padding: 15px; border-radius: 5px; }
                .routes { margin: 20px 0; }
                ul { list-style: none; padding: 0; }
                li { margin: 5px 0; }
                a { color: #667eea; text-decoration: none; }
                a:hover { text-decoration: underline; }
            </style>
        </head>
        <body>
            <div class='error'>
                <h2>404 - Маршрут не найден</h2>
                <p>Запрошенный URL: <strong>$requestedUrl</strong></p>
                <p>Доступные маршруты:</p>
            </div>
            
            <div class='routes'>
                <ul>";

        foreach ($this->routes as $route => $info) {
            $displayRoute = empty($route) ? '/' : "/$route";
            echo "<li><a href='$displayRoute'>$displayRoute</a> → {$info['controller']}::{$info['action']}()</li>";
        }

        echo "</ul>
            </div>
            
            <p><a href='/'>Вернуться на главную</a></p>
        </body>
        </html>";
    }
}
?>