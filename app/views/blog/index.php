<?php
// Начинаем сессию если еще не начата
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="content">
    <h1>Редактор блога</h1>

    <!-- Сообщения об успехе/ошибке -->
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

    <!-- Форма добавления записи -->
    <div style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
        <h2 style="color: #764ba2; margin-top: 0;">Добавить новую запись</h2>
        <form action="/admin/blog/add" method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label for="title" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">
                    Тема сообщения *
                </label>
                <input type="text" id="title" name="title"
                       value="<?= htmlspecialchars($old['title'] ?? '') ?>"
                       placeholder="Введите тему сообщения"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                       required>
                <?php if (isset($errors['title'])): ?>
                    <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">
                        <?= htmlspecialchars($errors['title']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="author" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">
                    Автор
                </label>
                <input type="text" id="author" name="author"
                       value="<?= htmlspecialchars($old['author'] ?? '') ?>"
                       placeholder="Если не указано, будет 'Аноним'"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="image" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">
                    Изображение
                </label>
                <input type="file" id="image" name="image" accept="image/*"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <div style="font-size: 14px; color: #6c757d; margin-top: 5px;">
                    Максимальный размер: 5MB. Допустимые форматы: JPEG, PNG, GIF, WebP
                </div>
                <?php if (isset($errors['image'])): ?>
                    <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">
                        <?= htmlspecialchars($errors['image']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="content" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">
                    Текст сообщения *
                </label>
                <textarea id="content" name="content"
                          placeholder="Введите текст сообщения"
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-height: 150px;"
                          required><?= htmlspecialchars($old['content'] ?? '') ?></textarea>
                <?php if (isset($errors['content'])): ?>
                    <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">
                        <?= htmlspecialchars($errors['content']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" style="background: #667eea; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                Добавить запись
            </button>
        </form>
    </div>

    <!-- Список существующих записей для редактирования -->
    <div style="margin-top: 40px;">
        <h2 style="color: #764ba2; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
            Существующие записи блога
        </h2>

        <?php if (empty($posts)): ?>
            <div style="text-align: center; padding: 40px; color: #6c757d;">
                <p>Записей пока нет.</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div id="admin-post-<?= $post['id'] ?>" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div style="flex: 1;">
                            <h3 style="margin-top: 0; color: #333;">
                                <?= htmlspecialchars($post['title']) ?>
                            </h3>

                            <?php if (!empty($post['image_path'])): ?>
                                <img src="<?= htmlspecialchars($post['image_path']) ?>"
                                     alt="<?= htmlspecialchars($post['title']) ?>"
                                     style="max-width: 200px; height: auto; margin: 10px 0; border-radius: 5px;">
                            <?php endif; ?>

                            <div style="margin: 10px 0; line-height: 1.6; color: #555;">
                                <?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?>...
                            </div>

                            <div style="color: #6c757d; font-size: 14px;">
                                <strong>Автор:</strong> <?= htmlspecialchars($post['author']) ?>
                                <br>
                                <strong>Дата:</strong> <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?>
                            </div>
                        </div>

                        <div style="margin-left: 20px;">
                            <button onclick="editPost(<?= $post['id'] ?>)"
                                    style="background: #28a745; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin-bottom: 5px; display: block; width: 100%;">
                                Изменить
                            </button>

                            <button onclick="if(confirm('Вы уверены, что хотите удалить эту запись?')) window.location.href='/admin/blog/delete?id=<?= $post['id'] ?>&redirect=blog'"
                                    style="background: #dc3545; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; display: block; width: 100%;">
                                Удалить
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($totalPages > 1): ?>
                <div style="display: flex; justify-content: center; margin-top: 30px;">
                    <?php if ($page > 1): ?>
                        <a href="/blog?page=<?= $page - 1 ?>"
                           style="display: inline-block; padding: 8px 12px; margin: 0 2px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #667eea; background: white;">
                            ← Назад
                        </a>
                    <?php endif; ?>

                    <span style="display: inline-block; padding: 8px 12px; margin: 0 2px; color: #6c757d;">
                        Страница <?= $page ?> из <?= $totalPages ?>
                    </span>

                    <?php if ($page < $totalPages): ?>
                        <a href="/blog?page=<?= $page + 1 ?>"
                           style="display: inline-block; padding: 8px 12px; margin: 0 2px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #667eea; background: white;">
                            Вперед →
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Модальное окно для редактирования записи блога -->
<div id="editModal" style="display: none; position: fixed; z-index: 1001; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 5% auto; padding: 20px; width: 90%; max-width: 800px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2); max-height: 80vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Редактировать запись блога</h3>
            <button onclick="closeEditModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">×</button>
        </div>

        <form id="editForm" method="POST" onsubmit="savePostChanges(event)">
            <input type="hidden" id="editPostId" name="id" value="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div style="margin-bottom: 15px;">
                <label for="editTitle" style="display: block; margin-bottom: 5px; font-weight: bold;">Тема сообщения:</label>
                <input type="text" id="editTitle" name="title"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" required>
            </div>

            <div style="margin-bottom: 15px;">
                <label for="editAuthor" style="display: block; margin-bottom: 5px; font-weight: bold;">Автор:</label>
                <input type="text" id="editAuthor" name="author"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="editContent" style="display: block; margin-bottom: 5px; font-weight: bold;">Текст сообщения:</label>
                <textarea id="editContent" name="content" rows="10"
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" required></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeEditModal()"
                        style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Отмена
                </button>
                <button type="submit"
                        style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Сохранить изменения
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // ===========================================
    // ФУНКЦИИ ДЛЯ РЕДАКТИРОВАНИЯ ЗАПИСЕЙ БЛОГА
    // ===========================================

    // CSRF токен (должен быть определен в PHP)
    var csrf_token = '<?= htmlspecialchars($csrf_token) ?>';

    // Загрузка данных записи для редактирования (Script метод)
    function editPost(postId) {
        console.log('Загрузка данных записи ID:', postId);

        // Показываем модальное окно
        document.getElementById('editModal').style.display = 'block';
        document.getElementById('editPostId').value = postId;

        // Загружаем данные записи через Script метод (Plaintext формат)
        var script = document.createElement('script');
        script.src = '/admin/blog/edit/' + postId + '?callback=handleEditData';
        document.body.appendChild(script);
    }

    // Обработка данных в формате Plaintext
    function handleEditData(data) {
        console.log('Получены данные для редактирования:', data);

        // Данные приходят в формате Plaintext с разделением по строкам
        var lines = data.split('\n');

        if (lines.length >= 4) {
            // ID уже установлен
            document.getElementById('editTitle').value = lines[1];
            document.getElementById('editContent').value = lines[2];
            document.getElementById('editAuthor').value = lines[3];

            // Сохраняем исходные значения для сравнения
            document.getElementById('editTitle').defaultValue = lines[1];
            document.getElementById('editContent').defaultValue = lines[2];
            document.getElementById('editAuthor').defaultValue = lines[3];

            // Фокус на поле заголовка
            document.getElementById('editTitle').focus();
        } else {
            alert('Ошибка загрузки данных записи');
            closeEditModal();
        }
    }

    // Закрыть модальное окно редактирования
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
        document.getElementById('editForm').reset();
    }

    // Сохранение изменений (Script метод отправки)
    function savePostChanges(event) {
        event.preventDefault();

        var formData = new FormData(document.getElementById('editForm'));
        var postId = formData.get('id');

        // Валидация
        var title = formData.get('title');
        var content = formData.get('content');

        if (!title.trim()) {
            alert('Тема сообщения не может быть пустой');
            return;
        }

        if (!content.trim()) {
            alert('Текст сообщения не может быть пустым');
            return;
        }

        // Создаем iframe для Script метода отправки данных
        var iframe = document.createElement('iframe');
        iframe.name = 'uploadFrame';
        iframe.style.display = 'none';
        document.body.appendChild(iframe);

        // Создаем форму для отправки
        var tempForm = document.createElement('form');
        tempForm.target = 'uploadFrame';
        tempForm.method = 'POST';
        tempForm.action = '/admin/blog/update';

        // Добавляем данные формы
        for (var [key, value] of formData.entries()) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            tempForm.appendChild(input);
        }

        document.body.appendChild(tempForm);
        tempForm.submit();

        // Обработка ответа
        iframe.onload = function() {
            try {
                var responseText = iframe.contentDocument.body.textContent || iframe.contentWindow.document.body.textContent;
                console.log('Ответ сервера:', responseText);

                if (responseText.startsWith('Успешно:')) {
                    alert('Запись успешно обновлена!');
                    closeEditModal();

                    // Обновляем данные на странице без перезагрузки
                    updatePostOnPage(postId, title, content, formData.get('author'));

                } else {
                    alert('Ошибка: ' + responseText);
                }
            } catch (e) {
                console.error('Ошибка обработки ответа:', e);
                alert('Произошла ошибка при обновлении записи');
            }

            // Очистка
            document.body.removeChild(tempForm);
            document.body.removeChild(iframe);
        };
    }

    // Обновление записи на странице без перезагрузки
    function updatePostOnPage(postId, newTitle, newContent, newAuthor) {
        // Находим контейнер записи
        var postContainer = document.getElementById('admin-post-' + postId);
        if (!postContainer) return;

        // Обновляем заголовок
        var titleElement = postContainer.querySelector('h3');
        if (titleElement) {
            titleElement.textContent = newTitle;
        }

        // Обновляем содержание
        var contentElement = postContainer.querySelector('div[style*="line-height: 1.6"]');
        if (contentElement) {
            contentElement.innerHTML = newContent.substring(0, 200).replace(/\n/g, '<br>') + '...';
        }

        // Обновляем автора
        var authorElement = postContainer.querySelector('strong:contains("Автор:")');
        if (authorElement && authorElement.parentElement) {
            var authorText = authorElement.parentElement.textContent;
            var datePart = authorText.split('<br>')[1] || '';
            authorElement.parentElement.innerHTML = '<strong>Автор:</strong> ' + (newAuthor || 'Аноним') +
                '<br><strong>Дата:</strong> ' + datePart;
        }

        console.log('Запись ID ' + postId + ' обновлена на странице');
    }

    // ===========================================
    // ИНИЦИАЛИЗАЦИЯ И ОБРАБОТЧИКИ СОБЫТИЙ
    // ===========================================

    // Инициализация после загрузки DOM
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Admin blog editor initialized');

        // Закрытие модального окна при клике вне его
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Закрытие модального окна при нажатии Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });
    });
</script>