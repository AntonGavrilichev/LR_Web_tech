<?php
class UserController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    // Регистрация пользователя
    public function register() {
        $this->saveVisitStatistics(); // Теперь этот метод должен быть доступен

        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';

            // Валидация
            if (empty($fullName) || empty($email) || empty($login) || empty($password)) {
                $errors[] = 'Все поля обязательны для заполнения';
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Некорректный email адрес';
            }

            if (strlen($password) < 6) {
                $errors[] = 'Пароль должен содержать минимум 6 символов';
            }

            // Проверка уникальности логина
            if (empty($errors)) {
                try {
                    $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE login = ?");
                    $stmt->execute([$login]);
                    if ($stmt->fetchColumn() > 0) {
                        $errors[] = 'Пользователь с таким логином уже существует';
                    }
                } catch (Exception $e) {
                    $errors[] = 'Ошибка проверки логина';
                }
            }

            // Проверка уникальности email
            if (empty($errors)) {
                try {
                    $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
                    $stmt->execute([$email]);
                    if ($stmt->fetchColumn() > 0) {
                        $errors[] = 'Пользователь с таким email уже существует';
                    }
                } catch (Exception $e) {
                    $errors[] = 'Ошибка проверки email';
                }
            }

            // Регистрация пользователя
            if (empty($errors)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                try {
                    $stmt = $this->db->prepare("INSERT INTO users (full_name, email, login, password) VALUES (?, ?, ?, ?)");

                    if ($stmt->execute([$fullName, $email, $login, $hashedPassword])) {
                        $success = true;
                        // Автоматический вход после регистрации
                        $userId = $this->db->lastInsertId();
                        $_SESSION['user_id'] = $userId;
                        $_SESSION['user_login'] = $login;
                        $_SESSION['user_full_name'] = $fullName;
                        $_SESSION['isLoggedIn'] = true;
                    } else {
                        $errors[] = 'Ошибка при регистрации пользователя';
                    }
                } catch (Exception $e) {
                    $errors[] = 'Ошибка базы данных: ' . $e->getMessage();
                }
            }
        }

        $data = [
            'title' => 'Регистрация пользователя',
            'pageTitle' => 'Регистрация нового пользователя',
            'errors' => $errors,
            'success' => $success,
            'formData' => $_POST ?? []
        ];

        $this->view->render('user/register', $data);
    }

    // Авторизация пользователя
    public function login() {
        $this->saveVisitStatistics(); // Теперь этот метод должен быть доступен

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = trim($_POST['login'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($login) || empty($password)) {
                $errors[] = 'Введите логин и пароль';
            }

            if (empty($errors)) {
                try {
                    $stmt = $this->db->prepare("SELECT * FROM users WHERE login = ?");
                    $stmt->execute([$login]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user && password_verify($password, $user['password'])) {
                        // Успешная авторизация
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_login'] = $user['login'];
                        $_SESSION['user_full_name'] = $user['full_name'];
                        $_SESSION['isLoggedIn'] = true;

                        // Редирект на предыдущую страницу или главную
                        $redirect = $_SESSION['redirect_after_login'] ?? '/';
                        unset($_SESSION['redirect_after_login']);

                        // Восстанавливаем данные формы теста если они были
                        if (isset($_SESSION['test_form_data'])) {
                            $testData = $_SESSION['test_form_data'];
                            unset($_SESSION['test_form_data']);
                            header('Location: /test');
                            exit;
                        }

                        header('Location: ' . $redirect);
                        exit;
                    } else {
                        $errors[] = 'Неверный логин или пароль';
                    }
                } catch (Exception $e) {
                    $errors[] = 'Ошибка базы данных: ' . $e->getMessage();
                }
            }
        }

        $data = [
            'title' => 'Вход в систему',
            'pageTitle' => 'Авторизация пользователя',
            'errors' => $errors
        ];

        $this->view->render('user/login', $data);
    }

    // Выход пользователя
    public function logout() {
        session_destroy();
        header('Location: /');
        exit;
    }
}
?>