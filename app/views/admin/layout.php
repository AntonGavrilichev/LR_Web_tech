<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Админ-панель'; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }
        .admin-header {
            background: #2c3e50;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .admin-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .admin-user-info {
            color: #ecf0f1;
            font-size: 14px;
        }
        .admin-nav {
            margin-top: 15px;
        }
        .admin-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 15px;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            transition: background 0.3s;
            font-size: 14px;
        }
        .admin-nav a:hover {
            background: #34495e;
        }
        .admin-nav .active {
            background: #3498db;
        }
        .admin-main {
            padding: 20px;
        }
        .admin-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }
        .admin-section {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #eee;
        }
        .admin-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-primary:hover {
            background: #2980b9;
        }
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        .btn-secondary:hover {
            background: #7f8c8d;
        }
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .form-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-check-input {
            margin: 0;
        }
        .form-check-label {
            margin: 0;
        }
    </style>
</head>
<body>
<header class="admin-header">
    <div class="admin-container">
        <div class="admin-header-content">
            <h1>Административная панель</h1>
            <?php if (isset($_SESSION['admin_login'])): ?>
                <div class="admin-user-info">
                    Администратор: <?php echo htmlspecialchars($_SESSION['admin_login']); ?>
                </div>
            <?php endif; ?>
        </div>

        <nav class="admin-nav">
            <ul>
                <li><a href="/admin/blog/edit" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'blog') ? 'active' : ''; ?>">Редактор блога</a></li>
                <li><a href="/admin/guestbook/upload" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'guestbook') ? 'active' : ''; ?>">Загрузка гостевой книги</a></li>
                <li><a href="/">На сайт</a></li>
                <li><a href="/admin/logout" style="background: #e74c3c;">Выход</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="admin-main">
    <div class="admin-container">
        <div class="admin-content">
            <?php echo $content ?? ''; ?>
        </div>
    </div>
</main>

<script>
    // Простой скрипт для подсветки активного пункта меню
    document.addEventListener('DOMContentLoaded', function() {
        const currentUrl = window.location.pathname;
        const navLinks = document.querySelectorAll('.admin-nav a');

        navLinks.forEach(link => {
            if (link.href.includes(currentUrl) && !link.href.includes('logout')) {
                link.classList.add('active');
            }
        });
    });
</script>
</body>
</html>