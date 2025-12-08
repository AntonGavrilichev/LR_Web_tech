<?php
// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        date('d.m.y'),
        trim($_POST['last_name'] . ' ' . $_POST['first_name'] . ' ' . $_POST['middle_name']),
        trim($_POST['email']),
        trim($_POST['message'])
    ];

    $fileData = guestbook . phpimplode(';', $data) . PHP_EOL;
    file_put_contents('messages.inc', $fileData, FILE_APPEND);
    header('Location: ?success=1');
    exit;
}

// Чтение сообщений
$messages = [];
if (file_exists('messages.inc')) {
    $lines = file('messages.inc', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode(';', $line);
        if (count($parts) == 4) {
            $messages[] = [
                'date' => $parts[0],
                'fio' => $parts[1],
                'email' => $parts[2],
                'message' => $parts[3]
            ];
        }
    }
    $messages = array_reverse($messages); // новые сверху
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Гостевая книга</title>
</head>
<body>
<h1>Гостевая книга</h1>

<form method="POST">
    <input type="text" name="last_name" placeholder="Фамилия" required>
    <input type="text" name="first_name" placeholder="Имя" required>
    <input type="text" name="middle_name" placeholder="Отчество">
    <input type="email" name="email" placeholder="E-mail" required>
    <textarea name="message" placeholder="Сообщение" required></textarea>
    <button type="submit">Отправить</button>
</form>

<h2>Сообщения:</h2>
<table border="1">
    <tr>
        <th>Дата</th><th>ФИО</th><th>E-mail</th><th>Сообщение</th>
    </tr>
    <?php foreach ($messages as $msg): ?>
        <tr>
            <td><?= htmlspecialchars($msg['date']) ?></td>
            <td><?= htmlspecialchars($msg['fio']) ?></td>
            <td><?= htmlspecialchars($msg['email']) ?></td>
            <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>