<?php
// Используем абсолютный путь
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Получаем JSON данные
$input = json_decode(file_get_contents('php://input'), true);
$login = trim($input['login'] ?? '');

if (empty($login)) {
    echo json_encode(['error' => 'Login is required']);
    exit;
}

try {
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE login = ?");
    $stmt->execute([$login]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $isTaken = $result['count'] > 0;

    echo json_encode([
        'login' => $login,
        'isTaken' => $isTaken,
        'message' => $isTaken ? 'Логин уже занят' : 'Логин свободен'
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>