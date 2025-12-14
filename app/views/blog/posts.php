<?php
// Начинаем сессию если еще не начата
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
            <div style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
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
                        <strong>Автор:</strong> <?= htmlspecialchars($post['author']) ?>
                        <br>
                        <strong>Дата:</strong> <?= date('d.m.Y H:i', strtotime($post['created_at'])) ?>
                    </div>
                    <div>
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
                        <button onclick="showCommentForm(<?= $post['id'] ?>, '<?= htmlspecialchars($_SESSION['user_full_name'] ?? $_SESSION['user_login']) ?>')"
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

        <form id="commentForm" method="POST" target="commentIframe" style="display: none;">
            <input type="hidden" id="commentPostId" name="post_id" value="">
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

<!-- Скрытый iframe для отправки формы -->
<iframe id="commentIframe" name="commentIframe" style="display: none;"></iframe>

<!-- JavaScript для работы с комментариями -->
<script>
    // Текущий postId для комментария
    var currentPostId = 0;
    var currentAuthorName = '';

    // Показать форму для комментария
    function showCommentForm(postId, authorName) {
        currentPostId = postId;
        currentAuthorName = authorName;

        document.getElementById('commentPostId').value = postId;
        document.getElementById('commentAuthorInfo').textContent = 'Комментарий от: ' + authorName;
        document.getElementById('commentForm').style.display = 'block';
        document.getElementById('commentModal').style.display = 'block';
        document.getElementById('commentContent').focus();
    }

    // Закрыть форму
    function closeCommentForm() {
        document.getElementById('commentModal').style.display = 'none';
        document.getElementById('commentForm').reset();
        document.getElementById('commentForm').style.display = 'none';
    }

    // Загрузка комментариев через Fetch API
    function loadComments(postId) {
        fetch('/blog/comments?post_id=' + postId)
            .then(response => response.text())
            .then(xmlText => {
                const parser = new DOMParser();
                const xmlDoc = parser.parseFromString(xmlText, 'text/xml');

                const commentsContainer = document.getElementById('comments-' + postId);
                const comments = xmlDoc.getElementsByTagName('comment');

                if (comments.length === 0) {
                    commentsContainer.innerHTML = '<p style="color: #6c757d; font-style: italic;">Комментариев пока нет</p>';
                    return;
                }

                let html = '';
                for (let i = 0; i < comments.length; i++) {
                    const comment = comments[i];
                    const author = comment.getElementsByTagName('author')[0].textContent;
                    const content = comment.getElementsByTagName('content')[0].textContent;
                    const date = comment.getElementsByTagName('date_formatted')[0].textContent;

                    html += `
                <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 10px; border-left: 3px solid #007bff;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <strong style="color: #495057;">${author}</strong>
                        <span style="color: #6c757d; font-size: 12px;">${date}</span>
                    </div>
                    <div style="color: #212529;">
                        ${content.replace(/\n/g, '<br>')}
                    </div>
                </div>`;
                }

                commentsContainer.innerHTML = html;
            })
            .catch(error => {
                console.error('Ошибка при загрузке комментариев:', error);
                document.getElementById('comments-' + postId).innerHTML =
                    '<p style="color: #dc3545;">Ошибка загрузки комментариев</p>';
            });
    }

    // Инициализация iframe обработчика
    document.addEventListener('DOMContentLoaded', function() {
        const iframe = document.getElementById('commentIframe');

        iframe.onload = iframe.onreadystatechange = function() {
            if (this.readyState && this.readyState != 'complete') return;

            try {
                const iframeDoc = this.contentDocument || this.contentWindow.document;
                const xmlText = iframeDoc.body.innerHTML;

                const parser = new DOMParser();
                const xmlDoc = parser.parseFromString(xmlText, 'text/xml');

                const status = xmlDoc.getElementsByTagName('status')[0].textContent;
                const message = xmlDoc.getElementsByTagName('message')[0].textContent;

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
            } catch (e) {
                console.error('Ошибка обработки ответа:', e);
                alert('Ошибка при отправке комментария');
            }
        };

        // Настройка формы
        const form = document.getElementById('commentForm');
        form.action = '/comment/add';
        form.method = 'POST';
        form.enctype = 'application/x-www-form-urlencoded';
        form.target = 'commentIframe';
    });
</script>