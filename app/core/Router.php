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
        'test/results' => ['controller' => 'TestController', 'action' => 'viewResults'],

        // Гостевая книга только для пользователей (без загрузки)
        'guestbook' => ['controller' => 'GuestbookController', 'action' => 'index'],

        // Блог только для пользователей (без редактирования)
        'blog' => ['controller' => 'BlogController', 'action' => 'index'],
        'posts' => ['controller' => 'BlogController', 'action' => 'posts'],
        'blog/upload' => ['controller' => 'BlogController', 'action' => 'upload'],
        'blog/upload-csv' => ['controller' => 'BlogController', 'action' => 'uploadCsv', 'method' => 'POST'],

        // маршруты для пользователей
        'user/register' => ['controller' => 'UserController', 'action' => 'register'],
        'user/login' => ['controller' => 'UserController', 'action' => 'login'],
        'user/logout' => ['controller' => 'UserController', 'action' => 'logout'],

        // Маршруты для админки
        'admin/login' => ['controller' => 'AdminLoginController', 'action' => 'login'],
        'admin/logout' => ['controller' => 'AdminLoginController', 'action' => 'logout'],
        'admin/statistics' => ['controller' => 'AdminStatisticsController', 'action' => 'index'],
        'admin/blog/edit' => ['controller' => 'AdminBlogController', 'action' => 'edit'],

        // Исправленный маршрут для загрузки гостевой книги
        'admin/guestbook/upload' => ['controller' => 'AdminGuestbookController', 'action' => 'upload'],

        // маршруты блога в админке - ВАЖНО: специфичные маршруты с параметрами должны быть ВЫШЕ
        'admin/blog/upload/downloadBackup/(:any)' => ['controller' => 'AdminBlogController', 'action' => 'downloadBackup'],
        'admin/blog/upload/restoreBackup/(:any)' => ['controller' => 'AdminBlogController', 'action' => 'restoreBackup'],
        'admin/blog/upload/downloadCurrent' => ['controller' => 'AdminBlogController', 'action' => 'downloadCurrent'],
        'admin/blog/upload/upload' => ['controller' => 'AdminBlogController', 'action' => 'upload', 'method' => 'POST'],
        'admin/blog/upload' => ['controller' => 'AdminBlogController', 'action' => 'index'],

        'admin/blog/add' => ['controller' => 'AdminBlogController', 'action' => 'add', 'method' => 'POST'],
        'admin/blog/delete' => ['controller' => 'AdminBlogController', 'action' => 'delete'],

    ];

    public function route() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = trim($uri, '/');

        // Удаляем query string если есть
        $uri = strtok($uri, '?');

        // Логируем для отладки
        error_log("=== Router Start ===");
        error_log("Запрошен URI: '$uri'");
        error_log("Всего маршрутов: " . count($this->routes));

        // Проверяем, соответствует ли URI какому-либо маршруту
        foreach ($this->routes as $route => $config) {
            // Заменяем (:any) на регулярное выражение
            $pattern = str_replace('(:any)', '([^/]+)', $route);
            $pattern = "#^" . $pattern . "$#";

            error_log("Проверка маршрута: '$route' -> '$pattern'");

            if (preg_match($pattern, $uri, $matches)) {
                error_log("Совпадение найдено!");
                error_log("Маршрут: $route");
                error_log("URI: $uri");
                error_log("Matches: " . print_r($matches, true));

                $controllerName = $config['controller'];
                $actionName = $config['action'];

                // Проверяем метод запроса если указан
                if (isset($config['method']) && $_SERVER['REQUEST_METHOD'] !== $config['method']) {
                    $this->error("Метод не поддерживается для этого маршрута");
                    return;
                }

                // Определяем путь к контроллеру
                $controllerFile = 'app/controllers/' . $controllerName . '.php';

                // Проверяем специальные случаи для админки
                if (strpos($controllerName, 'Admin') === 0) {
                    $controllerFile = 'app/controllers/admin/' . $controllerName . '.php';
                }

                error_log("Загрузка контроллера: $controllerFile");

                if (file_exists($controllerFile)) {
                    require_once $controllerFile;

                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();

                        if (method_exists($controller, $actionName)) {
                            // Передаем параметры из matches если есть
                            if (count($matches) > 1) {
                                array_shift($matches); // Убираем полное совпадение
                                error_log("Передача параметров: " . print_r($matches, true));
                                $controller->$actionName(...$matches);
                            } else {
                                $controller->$actionName();
                            }
                            return;
                        } else {
                            $this->error("Метод $actionName не найден в контроллере $controllerName");
                            return;
                        }
                    } else {
                        $this->error("Класс $controllerName не найден в файле $controllerFile");
                        return;
                    }
                } else {
                    $this->error("Файл контроллера $controllerFile не найден");
                    return;
                }
            }
        }

        // Если маршрут не найден
        error_log("Маршрут не найден для URI: $uri");
        $this->debugPage($uri);
    }

    private function error($message) {
        http_response_code(500);
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Ошибка роутера</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .error { 
                    background: #ffebee; 
                    border: 1px solid #ffcdd2; 
                    padding: 15px; 
                    margin: 20px 0;
                    border-radius: 5px;
                }
                a { color: #2196F3; text-decoration: none; }
                a:hover { text-decoration: underline; }
            </style>
        </head>
        <body>
            <h1>Ошибка роутера</h1>
            <div class='error'><strong>Ошибка:</strong> $message</div>
            <p><a href='/'>Вернуться на главную</a></p>
        </body>
        </html>";
    }

    private function debugPage($requestedUrl) {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Отладка маршрутов</title>
            <style>
                body { font-family: Arial; padding: 20px; }
                .error { 
                    background: #f8d7da; 
                    padding: 15px; 
                    border-radius: 5px;
                    margin-bottom: 20px;
                }
                .routes { 
                    margin: 20px 0; 
                    padding: 15px;
                    background: #f8f9fa;
                    border-radius: 5px;
                }
                ul { list-style: none; padding: 0; }
                li { 
                    margin: 10px 0; 
                    padding: 10px;
                    background: white;
                    border-radius: 3px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }
                a { color: #667eea; text-decoration: none; }
                a:hover { text-decoration: underline; }
                .admin-route { border-left: 4px solid #dc3545; }
                .user-route { border-left: 4px solid #28a745; }
            </style>
        </head>
        <body>
            <div class='error'>
                <h2>404 - Маршрут не найден</h2>
                <p>Запрошенный URL: <strong>/$requestedUrl</strong></p>
                <p>Доступные маршруты:</p>
            </div>
            
            <div class='routes'>
                <h3>Основные маршруты:</h3>
                <ul>";

        foreach ($this->routes as $route => $info) {
            $displayRoute = empty($route) ? '/' : "/$route";
            $routeClass = strpos($route, 'admin/') === 0 ? 'admin-route' : (strpos($route, 'user/') === 0 ? 'user-route' : '');

            echo "<li class='$routeClass'>
                    <a href='$displayRoute'>$displayRoute</a>
                    <br>
                    <small>{$info['controller']}::{$info['action']}()</small>
                  </li>";
        }

        echo "</ul>
            </div>
            
            <p><a href='/'>Вернуться на главную</a></p>
        </body>
        </html>";
    }
}
?>