<?php
// Начинаем сессию если еще не начата
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>
<div class="content">
    <h1>Записи блога</h1>

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

    <!-- Список записей блога -->
    <div style="margin-bottom: 40px;">
        <h2 style="color: #764ba2; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
            Записи блога
        </h2>

        <?php if (empty($posts)): ?>
            <div style="text-align: center; padding: 40px; color: #6c757d;">
                <p>Записей пока нет.</p>
            </div>
        <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div id="post-<?= $post['id'] ?>" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <h3 style="margin-top: 0; color: #333;">
                    <?= htmlspecialchars($post['title']) ?>
                </h3>

                <?php if (!empty($post['image_path'])): ?>
                    <img src="<?= htmlspecialchars($post['image_path']) ?>"
                         alt="<?= htmlspecialchars($post['title']) ?>"
                         style="max-width: 100%; height: auto; margin: 15px 0; border-radius: 5px;">
                <?php endif; ?>

                <div style="margin: 15px 0; line-height: 1.6;">
                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding-top: 15px; border-top: 1px solid #e9ecef; color: #6c757d; font-size: 14px;">
                    <div>
                        <strong>Автор:</strong> <span id="author-<?= $post['id'] ?>"><?= htmlspecialchars($post['author']) ?></span>
                        <br>
                        <strong>Дата:</strong> <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?>
                    </div>
                    <div>
                        <!-- Кнопка редактирования (только для админа) -->
                        <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
                            <button onclick="editPost(<?= $post['id'] ?>)"
                                    style="background: #28a745; color: white; padding: 5px 15px; border: none; border-radius: 4px; cursor: pointer; margin-right: 5px;">
                                Изменить
                            </button>
                        <?php endif; ?>

                        <button onclick="if(confirm('Вы уверены, что хотите удалить эту запись?')) window.location.href='/admin/blog/delete?id=<?= $post['id'] ?>&redirect=blog/posts'"
                                style="background: #dc3545; color: white; padding: 5px 15px; border: none; border-radius: 4px; cursor: pointer;">
                            Удалить
                        </button>
                    </div>
                </div>

                <!-- Блок комментариев -->
                <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid #eee;">
                    <h4 style="color: #495057; margin-bottom: 15px;">Комментарии</h4>

                    <!-- Контейнер для комментариев -->
                    <div id="comments-<?= $post['id'] ?>" class="comments-container">
                        <!-- Комментарии будут загружаться здесь -->
                    </div>

                    <!-- Кнопка добавления комментария (только для авторизованных) -->
                    <?php if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn']): ?>
                        <button onclick="showCommentForm(<?= $post['id'] ?>, '<?= htmlspecialchars($_SESSION['user_full_name'] ?? $_SESSION['user_login'] ?? 'Пользователь') ?>')"
                                style="background: #28a745; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px;">
                            Добавить комментарий
                        </button>
                    <?php else: ?>
                        <p style="color: #6c757d; font-style: italic; margin-top: 10px;">
                            Для добавления комментария <a href="/user/login">войдите</a> в систему
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Загружаем комментарии через AJAX -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    loadComments(<?= $post['id'] ?>);
                });
            </script>
        <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($totalPages > 1): ?>
            <?php
            // Настройки - показываем по 1 странице с каждой стороны от текущей (всего 3 в центре)
            $range = 1;
            ?>

            <div style="display: flex; justify-content: center; margin-top: 30px;">
                <?php
                // Первая страница
                if ($page > $range + 1): ?>
                    <a href="/blog/posts?page=1"
                       style="display: inline-block; padding: 8px 12px; margin: 0 2px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #667eea; background: white;">
                        1
                    </a>
                    <?php if ($page > $range + 2): ?>
                        <span style="padding: 8px 12px; margin: 0 2px;">...</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php
                // Пять центральных страниц
                $start = max(1, $page - $range);
                $end = min($totalPages, $page + $range);

                for ($i = $start; $i <= $end; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span style="display: inline-block; padding: 8px 12px; margin: 0 2px; border: 1px solid #667eea; border-radius: 4px; text-decoration: none; color: white; background: #667eea;">
                    <?= $i ?>
                </span>
                    <?php else: ?>
                        <a href="/blog/posts?page=<?= $i ?>"
                           style="display: inline-block; padding: 8px 12px; margin: 0 2px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #667eea; background: white;">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php
                // Последняя страница
                if ($page < $totalPages - $range): ?>
                    <?php if ($page < $totalPages - $range - 1): ?>
                        <span style="padding: 8px 12px; margin: 0 2px;">...</span>
                    <?php endif; ?>
                    <a href="/blog/posts?page=<?= $totalPages ?>"
                       style="display: inline-block; padding: 8px 12px; margin: 0 2px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #667eea; background: white;">
                        <?= $totalPages ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Модальное окно для добавления комментария -->
<div id="commentModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 10% auto; padding: 20px; width: 80%; max-width: 500px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
        <h3 style="margin-top: 0;">Добавить комментарий</h3>
        <p id="commentAuthorInfo" style="color: #6c757d; margin-bottom: 15px;"></p>

        <form id="commentForm" method="POST" style="display: none;">
            <input type="hidden" id="commentPostId" name="post_id" value="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div style="margin-bottom: 15px;">
                <label for="commentContent" style="display: block; margin-bottom: 5px; font-weight: bold;">Текст комментария:</label>
                <textarea id="commentContent" name="content" rows="4"
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                          required></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeCommentForm()"
                        style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Отмена
                </button>
                <button type="submit"
                        style="padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    Отправить
                </button>
            </div>
        </form>
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

    // Текущий postId для комментария
    var currentPostId = 0;
    var currentAuthorName = '';

    // Показать форму для комментария
    function showCommentForm(postId, authorName) {
        console.log('showCommentForm called:', postId, authorName);
        currentPostId = postId;
        currentAuthorName = authorName;

        document.getElementById('commentPostId').value = postId;
        document.getElementById('commentAuthorInfo').textContent = 'Комментарий от: ' + authorName;
        document.getElementById('commentForm').style.display = 'block';
        document.getElementById('commentModal').style.display = 'block';
        document.getElementById('commentContent').focus();
    }

    // Закрыть форму комментария
    function closeCommentForm() {
        document.getElementById('commentModal').style.display = 'none';
        document.getElementById('commentForm').reset();
        document.getElementById('commentForm').style.display = 'none';
    }

    // ===========================================
    // ФУНКЦИИ ДЛЯ РЕДАКТИРОВАНИЯ ЗАПИСЕЙ
    // ===========================================

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
        var postContainer = document.getElementById('post-' + postId);
        if (!postContainer) return;

        // Обновляем заголовок
        var titleElement = postContainer.querySelector('h3');
        if (titleElement) {
            titleElement.textContent = newTitle;
        }

        // Обновляем содержание
        var contentElement = postContainer.querySelector('div[style*="line-height: 1.6"]');
        if (contentElement) {
            contentElement.innerHTML = newContent.replace(/\n/g, '<br>');
        }

        // Обновляем автора
        var authorElement = document.getElementById('author-' + postId);
        if (authorElement) {
            authorElement.textContent = newAuthor || 'Аноним';
        }

        console.log('Запись ID ' + postId + ' обновлена на странице');
    }

    // ===========================================
    // ФУНКЦИИ ДЛЯ КОММЕНТАРИЕВ
    // ===========================================

    // Загрузка комментариев через Fetch API
    function loadComments(postId) {
        console.log('Loading comments for post:', postId);

        fetch('/blog/comments?post_id=' + postId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка сети: ' + response.status);
                }
                return response.text();
            })
            .then(xmlText => {
                console.log('Received XML:', xmlText);

                const parser = new DOMParser();
                const xmlDoc = parser.parseFromString(xmlText, 'text/xml');

                // Проверяем на ошибки парсинга XML
                const parserError = xmlDoc.getElementsByTagName('parsererror');
                if (parserError.length > 0) {
                    console.error('XML parse error:', parserError[0].textContent);
                    throw new Error('Ошибка разбора XML');
                }

                const commentsContainer = document.getElementById('comments-' + postId);
                const comments = xmlDoc.getElementsByTagName('comment');

                if (comments.length === 0) {
                    commentsContainer.innerHTML = '<p style="color: #6c757d; font-style: italic;">Комментариев пока нет</p>';
                    return;
                }

                let html = '';
                for (let i = 0; i < comments.length; i++) {
                    const comment = comments[i];

                    // Безопасное получение элементов
                    const getText = (tagName) => {
                        const elements = comment.getElementsByTagName(tagName);
                        return elements.length > 0 ? elements[0].textContent : '';
                    };

                    const author = getText('author');
                    const content = getText('content');
                    const date = getText('date_formatted') || getText('created_at');

                    html += `
                <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 10px; border-left: 3px solid #007bff;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <strong style="color: #495057;">${author}</strong>
                        <span style="color: #6c757d; font-size: 12px;">${date}</span>
                    </div>
                    <div style="color: #212529; white-space: pre-wrap;">
                        ${content.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>')}
                    </div>
                </div>`;
                }

                commentsContainer.innerHTML = html;
            })
            .catch(error => {
                console.error('Ошибка при загрузке комментариев:', error);
                document.getElementById('comments-' + postId).innerHTML =
                    '<p style="color: #dc3545;">Ошибка загрузки комментариев: ' + error.message + '</p>';
            });
    }

    // ===========================================
    // ИНИЦИАЛИЗАЦИЯ И ОБРАБОТЧИКИ СОБЫТИЙ
    // ===========================================

    // Инициализация после загрузки DOM
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, setting up forms...');

        // Настройка формы комментария
        const commentForm = document.getElementById('commentForm');

        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Comment form submitted');

            // Проверяем авторизацию
            <?php if (!isset($_SESSION['isLoggedIn']) || !$_SESSION['isLoggedIn']): ?>
            alert('Требуется авторизация для добавления комментария');
            return;
            <?php endif; ?>

            // Собираем данные формы
            const formData = new FormData(commentForm);

            // Отправляем запрос
            fetch('/comment/add', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error('HTTP error: ' + response.status);
                    }
                    return response.text();
                })
                .then(xmlText => {
                    console.log('Response XML:', xmlText);

                    const parser = new DOMParser();
                    const xmlDoc = parser.parseFromString(xmlText, 'text/xml');

                    // Проверяем на ошибки парсинга
                    const parserError = xmlDoc.getElementsByTagName('parsererror');
                    if (parserError.length > 0) {
                        throw new Error('Неверный ответ сервера');
                    }

                    const statusElement = xmlDoc.getElementsByTagName('status');
                    const messageElement = xmlDoc.getElementsByTagName('message');

                    if (statusElement.length === 0 || messageElement.length === 0) {
                        throw new Error('Неверный формат ответа');
                    }

                    const status = statusElement[0].textContent;
                    const message = messageElement[0].textContent;

                    if (status === 'success') {
                        // Закрываем модальное окно
                        closeCommentForm();

                        // Обновляем комментарии
                        loadComments(currentPostId);

                        // Показываем сообщение об успехе
                        alert('Комментарий успешно добавлен!');
                    } else {
                        alert('Ошибка: ' + message);
                    }
                })
                .catch(error => {
                    console.error('Ошибка при отправке комментария:', error);
                    alert('Ошибка при отправке комментария: ' + error.message);
                });
        });

        // Закрытие модальных окон при клике вне их
        document.getElementById('commentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCommentForm();
            }
        });

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        // Закрытие модальных окон при нажатии Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (document.getElementById('commentModal').style.display === 'block') {
                    closeCommentForm();
                }
                if (document.getElementById('editModal').style.display === 'block') {
                    closeEditModal();
                }
            }
        });

        // Отладочная информация
        console.log('All systems initialized');
    });
</script>