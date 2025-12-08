<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="content">
    <h1>Загрузка сообщений блога из CSV</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <?= htmlspecialchars($_SESSION['error_message']) ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="errors" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <h3>Ошибки при загрузке:</h3>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div style="background: #f8f9fa; padding: 30px; border-radius: 10px; margin: 30px 0;">
        <h2 style="color: #764ba2; margin-top: 0;">Загрузить CSV файл</h2>

        <div style="margin-bottom: 20px; padding: 20px; background: white; border-radius: 8px;">
            <h3>Формат CSV файла:</h3>
            <p>Файл должен содержать следующие поля (в порядке следования):</p>
            <ol>
                <li><strong>title</strong> - тема сообщения (обязательно)</li>
                <li><strong>message</strong> - текст сообщения (обязательно)</li>
                <li><strong>author</strong> - автор (если пусто, будет "Аноним")</li>
                <li><strong>created_at</strong> - дата создания в формате YYYY-MM-DD HH:MM (если пусто, будет текущая дата)</li>
            </ol>
            <p><strong>Пример содержимого CSV:</strong></p>
            <pre style="background: #e9ecef; padding: 15px; border-radius: 5px; overflow-x: auto;">
"тема 1","сообщение 1","Vasiliy","2019-01-01 14:00"
"тема 2","сообщение 2","","2020-02-15 10:30"
"тема 3","сообщение 3","Maria",""</pre>
            <p style="color: #666; font-size: 14px;">
                <strong>Примечание:</strong> Поля должны быть разделены запятыми, а строки заключены в двойные кавычки.
                Максимальный размер файла: 5MB. Допустимые форматы: .csv, .txt
            </p>
        </div>

        <form action="/blog/upload-csv" method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 20px;">
                <label for="csv_file" style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">
                    Выберите CSV файл *
                </label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #555;">
                    Действие при обнаружении ошибок:
                </label>
                <div style="display: flex; gap: 20px;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="radio" name="on_error" value="stop" checked style="margin-right: 8px;">
                        <span>Остановить загрузку при первой ошибке</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="radio" name="on_error" value="skip" style="margin-right: 8px;">
                        <span>Пропускать строки с ошибками</span>
                    </label>
                </div>
            </div>

            <button type="submit" style="background: #667eea; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: 600; transition: background 0.3s;">
                Загрузить файл
            </button>

            <a href="/blog" style="margin-left: 15px; color: #667eea; text-decoration: none;">
                Вернуться к редактору блога
            </a>
        </form>
    </div>

    <?php if (isset($results) && !empty($results)): ?>
        <div style="background: #e9f7ef; padding: 25px; border-radius: 10px; margin-top: 30px; border: 1px solid #c3e6cb;">
            <h3 style="color: #155724; margin-top: 0;">Результаты загрузки:</h3>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 15px;">
                <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                    <div style="font-size: 32px; color: #28a745; font-weight: bold;"><?= $results['total_processed'] ?></div>
                    <div style="color: #666;">Всего обработано строк</div>
                </div>

                <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                    <div style="font-size: 32px; color: #17a2b8; font-weight: bold;"><?= $results['successful'] ?></div>
                    <div style="color: #666;">Успешно добавлено</div>
                </div>

                <div style="text-align: center; padding: 15px; background: white; border-radius: 8px;">
                    <div style="font-size: 32px; color: #dc3545; font-weight: bold;"><?= $results['failed'] ?></div>
                    <div style="color: #666;">С ошибками</div>
                </div>
            </div>

            <?php if (!empty($results['errors'])): ?>
                <div style="margin-top: 20px;">
                    <h4 style="color: #856404;">Подробные ошибки:</h4>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                        <thead>
                        <tr style="background: #fff3cd; color: #856404;">
                            <th style="padding: 10px; border: 1px solid #ffeaa7; text-align: left;">Строка</th>
                            <th style="padding: 10px; border: 1px solid #ffeaa7; text-align: left;">Ошибка</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($results['errors'] as $rowNum => $error): ?>
                            <tr style="background: white;">
                                <td style="padding: 10px; border: 1px solid #ffeaa7;"><?= $rowNum ?></td>
                                <td style="padding: 10px; border: 1px solid #ffeaa7; color: #dc3545;"><?= htmlspecialchars($error) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <?php if (!empty($results['successful']) && $results['successful'] > 0): ?>
                <div style="margin-top: 20px; text-align: center;">
                    <a href="/posts" style="background: #28a745; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block;">
                        Просмотреть добавленные записи
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>