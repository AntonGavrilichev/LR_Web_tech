<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Персональный сайт' ?></title>
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .main-menu {
            display: flex;
            justify-content: center;
            list-style: none;
            flex-wrap: wrap;
            gap: 10px;
        }

        .main-menu li {
            margin: 0;
        }

        .main-menu a {
            color: #333;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            font-weight: 500;
            display: block;
        }

        .main-menu a:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
        }

        main {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin: 30px auto;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            min-height: 500px;
        }

        h1 {
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        h2 {
            color: #555;
            margin: 25px 0 15px 0;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 30px;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .profile-img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #667eea;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        table th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        table tr:nth-child(even) {
            background: #f8f9fa;
        }

        table tr:hover {
            background: #e9ecef;
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 30px 0;
        }

        .photo-item {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .photo-item:hover {
            transform: translateY(-5px);
        }

        .photo-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .photo-item p {
            padding: 15px;
            margin: 0;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        textarea {
            min-height: 150px;
            resize: vertical;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            font-weight: normal;
            cursor: pointer;
        }

        .radio-group input {
            width: auto;
            margin-right: 8px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
            margin-right: 10px;
            display: inline-block;
            text-decoration: none;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: white;
            margin-top: 30px;
        }

        .errors {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #f5c6cb;
        }

        .error {
            margin: 5px 0;
        }

        @media (max-width: 768px) {
            .photo-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .profile {
                flex-direction: column;
                text-align: center;
            }

            .main-menu {
                flex-direction: column;
                align-items: center;
            }
        }

        @media (max-width: 480px) {
            .photo-grid {
                grid-template-columns: 1fr;
            }

            main {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <header>
        <nav>
            <ul class="main-menu">
                <li><a href="/">Главная</a></li>
                <li><a href="/about">Обо мне</a></li>
                <li><a href="/interests">Мои интересы</a></li>
                <li><a href="/study">Учеба</a></li>
                <li><a href="/album">Фотоальбом</a></li>
                <li><a href="/contact">Контакт</a></li>
                <li><a href="/test">Тест</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php
        // ВЫВОДИМ КОНТЕНТ
        if (isset($content)) {
            echo $content;
        } else {
            echo '<div class="content"><h1>Контент не загружен</h1></div>';
        }
        ?>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Персональный сайт. Все права защищены.</p>
    </footer>
</div>
</body>
</html>