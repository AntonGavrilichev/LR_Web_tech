<?php
require_once '../config/database.php';
require_once '../app/models/TestResult.php';

// Сохранение результатов теста
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answers = json_encode($_POST['answers'] ?? []);
    $isCorrect = ($_POST['score'] ?? 0) > 70; // Пример: больше 70% правильно

    $result = new TestResult([
        'full_name' => $_POST['full_name'],
        'answers' => $answers,
        'is_correct' => $isCorrect ? 1 : 0
    ]);

    $result->save();
    echo "Результат сохранен!";
}

// Просмотр всех результатов
$results = TestResult::findAll('created_at DESC');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Тест</title>
</head>
<body>
<form method="POST">

    <input type="text" name="full_name" placeholder="ФИО" required>
    <button type="submit">Отправить</button>
</form>

<h2>Результаты:</h2>
<table border="1">
    <tr>
        <th>Дата</th><th>ФИО</th><th>Результат</th>
    </tr>
    <?php foreach ($results as $result): ?>
        <tr>
            <td><?= $result->created_at ?></td>
            <td><?= htmlspecialchars($result->full_name) ?></td>
            <td><?= $result->is_correct ? 'Верно' : 'Неверно' ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>