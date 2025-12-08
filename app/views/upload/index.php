<!-- В самом начале файла добавьте проверку админ-доступа -->
<?php
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
    header('Location: /admin/login');
    exit;
}
?>

<div class="content">
    <h1 style="color: #764ba2; margin-bottom: 30px;">
        <i class="fas fa-upload" style="margin-right: 10px;"></i>Загрузка сообщений гостевой книги
    </h1>

    <!-- Информация о текущем файле -->
    <div style="margin-bottom: 30px; padding: 20px; background: white; border-radius: 8px; border: 1px solid #e0e0e0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #667eea; margin-bottom: 15px;">
            <i class="fas fa-info-circle" style="margin-right: 10px;"></i>Текущий файл messages.inc
        </h2>

        <?php if ($fileInfo['exists']): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                    <strong style="display: block; color: #555; margin-bottom: 5px;">Размер файла</strong>
                    <span style="color: #667eea; font-size: 18px;"><?= number_format($fileInfo['size'] / 1024, 2) ?> КБ</span>
                </div>

                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                    <strong style="display: block; color: #555; margin-bottom: 5px;">Дата изменения</strong>
                    <span style="color: #667eea; font-size: 18px;"><?= $fileInfo['modified'] ?></span>
                </div>

                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                    <strong style="display: block; color: #555; margin-bottom: 5px;">Количество строк</strong>
                    <span style="color: #667eea; font-size: 18px;"><?= $fileInfo['lines'] ?></span>
                </div>

                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
                    <strong style="display: block; color: #555; margin-bottom: 5px;">Сообщений</strong>
                    <span style="color: #667eea; font-size: 18px;"><?= $fileInfo['messages_count'] ?></span>
                </div>
            </div>

            <a href="/admin/blog/upload/downloadCurrent"
               style="display: inline-flex; align-items: center; background: #28a745; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin-right: 10px;">
                <i class="fas fa-download" style="margin-right: 8px;"></i> Скачать текущий файл
            </a>
        <?php else: ?>
            <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; border: 1px solid #ffeaa7; margin-bottom: 15px;">
                <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i>
                Файл messages.inc не найден. Загрузите файл для начала работы.
            </div>
        <?php endif; ?>
    </div>

    <!-- Форма загрузки -->
    <div style="margin-bottom: 40px; padding: 25px; background: white; border-radius: 8px; border: 1px solid #e0e0e0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #667eea; margin-bottom: 20px;">
            <i class="fas fa-file-upload" style="margin-right: 10px;"></i>Загрузить новый файл
        </h2>

        <?php if ($uploadResult): ?>
            <div style="background: <?= $uploadResult['success'] ? '#d4edda' : '#f8d7da' ?>;
                    color: <?= $uploadResult['success'] ? '#155724' : '#721c24' ?>;
                    padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid <?= $uploadResult['success'] ? '#c3e6cb' : '#f5c6cb' ?>;">
                <strong><?= $uploadResult['success'] ? '✓ Успешно!' : '✗ Ошибка:' ?></strong>
                <?= htmlspecialchars($uploadResult['message']) ?>

                <?php if ($uploadResult['success'] && !empty($uploadResult['backup_path'])): ?>
                    <div style="margin-top: 10px; font-size: 14px;">
                        <i class="fas fa-shield-alt" style="margin-right: 5px;"></i>
                        Создана резервная копия: <?= basename($uploadResult['backup_path']) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Исправлена форма: action указывает на правильный маршрут -->
        <form method="POST" action="/admin/blog/upload/upload" enctype="multipart/form-data"
              style="background: #f8f9fa; padding: 20px; border-radius: 5px;">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; font-weight: bold; color: #555;">
                    <i class="fas fa-file-alt" style="margin-right: 8px;"></i>Выберите файл messages.inc
                </label>
                <input type="file" name="guestbook_file" accept=".inc,.txt"
                       style="width: 100%; padding: 10px; border: 2px dashed #667eea; border-radius: 5px; background: white; cursor: pointer;"
                       onchange="previewFileName(this)">
                <div id="fileName" style="margin-top: 10px; color: #666; font-size: 14px; display: none;">
                    <i class="fas fa-file" style="margin-right: 5px;"></i>
                    <span></span>
                </div>
            </div>

            <div style="margin-bottom: 20px; background: #e8f4ff; padding: 15px; border-radius: 5px; border-left: 4px solid #667eea;">
                <strong style="display: block; margin-bottom: 10px; color: #667eea;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i>Требования к файлу:
                </strong>
                <ul style="margin: 0; padding-left: 20px; color: #555;">
                    <li>Формат: текстовый файл с разделителем ";"</li>
                    <li>Расширение: .inc или .txt</li>
                    <li>Максимальный размер: 2 МБ</li>
                    <li>Структура каждой строки: Дата;Фамилия;Имя;Отчество;E-mail;Текст отзыва</li>
                    <li>Перед загрузкой текущий файл будет сохранен в резервную копию</li>
                </ul>
            </div>

            <button type="submit"
                    style="display: inline-flex; align-items: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border: none; border-radius: 25px; font-size: 16px; cursor: pointer; font-weight: bold;">
                <i class="fas fa-upload" style="margin-right: 10px;"></i> Загрузить файл
            </button>

            <a href="/guestbook"
               style="display: inline-flex; align-items: center; background: #6c757d; color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none; margin-left: 15px;">
                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Назад к гостевой книге
            </a>
        </form>
    </div>

    <!-- Резервные копии -->
    <?php if (!empty($backupFiles)): ?>
        <div style="margin-bottom: 30px; padding: 25px; background: white; border-radius: 8px; border: 1px solid #e0e0e0; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h2 style="color: #667eea; margin-bottom: 20px;">
                <i class="fas fa-history" style="margin-right: 10px;"></i>Резервные копии
            </h2>
            <p style="color: #666; margin-bottom: 20px;">
                Доступные резервные копии файла messages.inc:
            </p>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; color: #495057;">Имя файла</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; color: #495057;">Размер</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; color: #495057;">Дата изменения</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 2px solid #dee2e6; color: #495057;">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($backupFiles as $backup): ?>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px;">
                                <i class="fas fa-file-archive" style="color: #667eea; margin-right: 8px;"></i>
                                <?= htmlspecialchars($backup['name']) ?>
                            </td>
                            <td style="padding: 12px;">
                                <?= number_format($backup['size'] / 1024, 2) ?> КБ
                            </td>
                            <td style="padding: 12px;">
                                <?= $backup['modified'] ?>
                            </td>
                            <td style="padding: 12px;">
                                <a href="/admin/blog/upload/downloadBackup/<?= urlencode($backup['name']) ?>"
                                   style="display: inline-flex; align-items: center; background: #17a2b8; color: white; padding: 6px 12px; border-radius: 3px; text-decoration: none; margin-right: 5px; font-size: 14px;">
                                    <i class="fas fa-download" style="margin-right: 5px;"></i> Скачать
                                </a>
                                <a href="/admin/blog/upload/restoreBackup/<?= urlencode($backup['name']) ?>"
                                   onclick="return confirm('Вы уверены, что хотите восстановить эту резервную копию? Текущий файл будет заменен.')"
                                   style="display: inline-flex; align-items: center; background: #ffc107; color: #212529; padding: 6px 12px; border-radius: 3px; text-decoration: none; font-size: 14px;">
                                    <i class="fas fa-redo" style="margin-right: 5px;"></i> Восстановить
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    function previewFileName(input) {
        const fileNameDiv = document.getElementById('fileName');
        const fileNameSpan = fileNameDiv.querySelector('span');

        if (input.files && input.files[0]) {
            fileNameSpan.textContent = input.files[0].name + ' (' +
                formatFileSize(input.files[0].size) + ')';
            fileNameDiv.style.display = 'block';
        } else {
            fileNameDiv.style.display = 'none';
        }
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' байт';
        else if (bytes < 1048576) return (bytes / 1024).toFixed(2) + ' КБ';
        else return (bytes / 1048576).toFixed(2) + ' МБ';
    }

    // Добавляем Font Awesome иконки
    if (!document.querySelector('link[href*="font-awesome"]')) {
        document.head.insertAdjacentHTML('beforeend',
            '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">'
        );
    }
</script>

<style>
    .content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    input[type="file"]:hover {
        border-color: #764ba2 !important;
        background: #f8f9ff;
    }

    @media (max-width: 768px) {
        table {
            font-size: 14px;
        }

        th, td {
            padding: 8px !important;
        }

        button, a[href] {
            width: 100%;
            margin-bottom: 10px !important;
            margin-left: 0 !important;
            justify-content: center;
        }
    }
</style>