<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title'] ?? 'Административная панель'; ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .admin-header {
            background: #2c3e50;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
        }
        .admin-nav ul {
            list-style: none;
            padding: 0;
            display: flex;
            gap: 20px;
        }
        .admin-nav a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
        }
        .admin-nav a:hover {
            background: #34495e;
        }
        .statistics-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .statistics-table th, .statistics-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .statistics-table th {
            background-color: #f2f2f2;
        }
        .pagination {
            margin-top: 20px;
            display: flex;
            gap: 5px;
        }
        .pagination a {
            padding: 5px 10px;
            border: 1px solid #ddd;
            text-decoration: none;
        }
        .pagination a.active {
            background: #2c3e50;
            color: white;
        }
    </style>
</head>
<body>
<div class="admin-header">
    <h1>Административная панель</h1>
    <nav class="admin-nav">
        <ul>
            <li><a href="/admin/statistics/view">Статистика</a></li>
            <li><a href="/admin/blog/edit">Редактор блога</a></li>
            <li><a href="/admin/guestbook/upload">Загрузка гостевой книги</a></li>
            <li><a href="/admin/authorization/logout">Выход</a></li>
            <li><span><?php echo $_SESSION['admin_login'] ?? ''; ?></span></li>
        </ul>
    </nav>
</div>

<div class="container">
    <?php
    if (isset($content_view)) {
        include 'app/admin/views/' . $content_view;
    } else if (isset($data['content'])) {
        echo $data['content'];
    }
    ?>
</div>
</body>
</html>