<?php
// Добавляем наследование от Controller
class TestController extends Controller {
    public function __construct() {
        parent::__construct(); // Вызываем конструктор родителя
    }

    public function index() {
        // Сохраняем статистику посещений
        $this->saveVisitStatistics();

        // Проверяем авторизацию пользователя для доступа к тесту
        // (согласно п.8 задания, результаты видны только авторизованным)
        $userId = $this->isUserLoggedIn();

        $errors = [];
        $results = null;
        $formData = [];

        // Проверяем отправку формы
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Проверяем авторизацию перед обработкой результатов
            if (!$userId) {
                // Если не авторизован, сохраняем данные формы и редиректим на логин
                $_SESSION['test_form_data'] = $_POST;
                $_SESSION['redirect_after_login'] = '/test';
                header('Location: /user/login');
                exit;
            }

            // Загружаем валидаторы
            require_once 'app/models/validators/FormValidation.php';
            require_once 'app/models/validators/CustomFormValidation.php';
            require_once 'app/models/validators/ResultsVerification.php';

            $validator = new ResultsVerification();

            // Валидируем форму теста
            if ($validator->validateTestForm($_POST)) {
                // Сохраняем данные формы
                $formData = $_POST;

                // Проверяем ответы
                $results = $validator->checkAnswers($_POST);

                // Сохраняем в базу данных с привязкой к пользователю
                $validator->saveToDatabase($_POST['full_name'], $_POST, $results, $_SESSION['user_id']);

                // Формируем сообщение с результатами
                $this->saveTestResults($_POST, $results);

            } else {
                // Сохраняем ошибки
                $errors = $validator->errors;
                // Сохраняем введенные данные
                $formData = $_POST;
            }
        }

        // Получаем информацию о текущем пользователе для отображения
        $currentUser = $this->getCurrentUser();

        $data = [
            'title' => 'Тест по дисциплине',
            'pageTitle' => 'Тест по Теории вероятностей и математической статистике',
            'discipline' => 'Теория вероятностей и математическая статистика',
            'errors' => $errors,
            'results' => $results,
            'formData' => $formData,
            'isLoggedIn' => $userId,
            'currentUser' => $currentUser
        ];

        $this->view->render('test/index', $data);
    }

    public function viewResults() {
        // Сохраняем статистику посещений
        $this->saveVisitStatistics();

        // Проверяем авторизацию администратора для просмотра всех результатов
        if (!$this->isAdmin()) {
            // Для обычных пользователей показываем только их результаты
            if (!$this->isUserLoggedIn()) {
                header('Location: /user/login');
                exit;
            }

            // Показываем только результаты текущего пользователя
            try {
                $stmt = $this->db->prepare("SELECT * FROM test_results WHERE user_id = ? ORDER BY created_at DESC");
                $stmt->execute([$_SESSION['user_id']]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $results = [];
                $error = "Ошибка при получении данных: " . $e->getMessage();
            }
        } else {
            // Администратор видит все результаты
            try {
                $stmt = $this->db->query("SELECT * FROM test_results ORDER BY created_at DESC");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $results = [];
                $error = "Ошибка при получении данных: " . $e->getMessage();
            }
        }

        // Преобразуем JSON данные обратно в массив для удобного отображения
        foreach ($results as &$result) {
            $result['user_answers_array'] = json_decode($result['user_answers'], true);
            $result['correct_answers_array'] = json_decode($result['correct_answers'], true);
            $result['created_at_formatted'] = date('d.m.Y H:i', strtotime($result['created_at']));
        }

        // Передаем функцию getAnswerText как замыкание
        $getAnswerText = function($questionNum, $answerCode) {
            return $this->getAnswerText($questionNum, $answerCode);
        };

        $data = [
            'title' => 'Результаты тестирования',
            'pageTitle' => 'Просмотр результатов теста',
            'results' => $results,
            'error' => $error ?? null,
            'getAnswerText' => $getAnswerText->bindTo($this),
            'isAdmin' => $this->isAdmin(),
            'isLoggedIn' => $this->isUserLoggedIn()
        ];

        $this->view->render('test/results', $data);
    }

    private function saveTestResults($postData, $results) {
        // Сохраняем результаты в сессию для демонстрации
        if (!isset($_SESSION['test_results'])) {
            $_SESSION['test_results'] = [];
        }

        $testData = [
            'id' => uniqid(),
            'date' => date('d.m.Y H:i:s'),
            'full_name' => $postData['full_name'],
            'group' => $postData['group'],
            'results' => $results,
            'score' => $results['percentage'] ?? 0
        ];

        array_unshift($_SESSION['test_results'], $testData);

        // Ограничиваем количество сохраненных результатов
        if (count($_SESSION['test_results']) > 10) {
            array_pop($_SESSION['test_results']);
        }

        // Также сохраняем в файл
        $logDir = 'test_results/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $filename = $logDir . 'test_' . date('Y-m-d_H-i-s') . '.txt';
        $content = $this->formatTestResults($postData, $results);
        file_put_contents($filename, $content);
    }

    private function formatTestResults($postData, $results) {
        $output = "РЕЗУЛЬТАТЫ ТЕСТИРОВАНИЯ\n";
        $output .= "=====================\n\n";

        $output .= "👤 ИНФОРМАЦИЯ О СТУДЕНТЕ:\n";
        $output .= "-------------------------\n";
        $output .= "ФИО: " . $postData['full_name'] . "\n";
        $output .= "Группа: " . $postData['group'] . "\n";
        $output .= "ID пользователя: " . ($_SESSION['user_id'] ?? 'неизвестно') . "\n";
        $output .= "Дата тестирования: " . date('d.m.Y H:i:s') . "\n\n";

        $output .= "📊 РЕЗУЛЬТАТЫ:\n";
        $output .= "---------------\n";
        $output .= "Правильных ответов: " . ($results['correct'] ?? 0) . " из " . ($results['total'] ?? 0) . "\n";
        $output .= "Процент правильных: " . ($results['percentage'] ?? 0) . "%\n";
        $output .= "Оценка: " . $this->getGrade($results['percentage'] ?? 0) . "\n\n";

        $output .= "📝 ДЕТАЛИЗАЦИЯ ПО ВОПРОСАМ:\n";
        $output .= "---------------------------\n";

        if (isset($results['details'])) {
            foreach ($results['details'] as $q => $detail) {
                $qNum = substr($q, 1);
                $output .= "\nВопрос $qNum:\n";
                $output .= "  Ваш ответ: " . $this->getAnswerText($qNum, $detail['user'] ?? '') . "\n";
                $output .= "  Правильный ответ: " . $this->getAnswerText($qNum, $detail['correct'] ?? '') . "\n";
                $output .= "  Результат: " . ($detail['is_correct'] ? '✅ Правильно' : '❌ Неправильно') . "\n";
            }
        }

        $output .= "\n=====================\n";
        $output .= "Тест по дисциплине: Теория вероятностей и математическая статистика\n";

        return $output;
    }

    private function getGrade($percentage) {
        if ($percentage >= 90) return 'Отлично (5)';
        if ($percentage >= 75) return 'Хорошо (4)';
        if ($percentage >= 60) return 'Удовлетворительно (3)';
        return 'Неудовлетворительно (2)';
    }

    public function getAnswerText($questionNum, $answerCode) {
        $answers = [
            1 => [
                '1' => 'Численная мера возможности наступления события',
                '2' => 'Отношение числа неблагоприятных исходов к общему числу исходов',
                '3' => 'Сумма всех возможных исходов',
                '4' => 'Произведение всех возможных исходов'
            ],
            2 => [
                '1' => 'Нормальное распределение',
                '2' => 'Биномиальное распределение',
                '3' => 'Равномерное распределение',
                '4' => 'Распределение Пуассона'
            ],
            3 => [
                '1' => 'Среднее значение случайной величины',
                '2' => 'Разброс значений относительно среднего',
                '3' => 'Наиболее вероятное значение',
                '4' => 'Медианное значение'
            ]
        ];

        return $answers[$questionNum][$answerCode] ?? 'Ответ не выбран';
    }
}
?>