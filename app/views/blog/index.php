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
        <form action="/blog/add" method="POST" enctype="multipart/form-data">
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

    <!-- Список записей блога -->
    <div style="margin-bottom: 40px;">
        <h2 style="color: #764ba2; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
            Записи блога
        </h2>

        <?php if (empty($posts)): ?>
            <div style="text-align: center; padding: 40px; color: #6c757d;">
                <p>Записей пока нет. Будьте первым, кто добавит запись!</p>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
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
                            <button onclick="if(confirm('Вы уверены, что хотите удалить эту запись?')) window.location.href='/blog/delete?id=<?= $post['id'] ?>'"
                                    style="background: #dc3545; color: white; padding: 5px 15px; border: none; border-radius: 4px; cursor: pointer;">
                                Удалить
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Пагинация -->
            <?php if ($totalPages > 1): ?>
                <?php
                // Настройки - показываем по 2 страницы с каждой стороны от текущей (всего 5 в центре)
                $range = 1;
                ?>

                <div style="display: flex; justify-content: center; margin-top: 30px;">
                    <?php
                    // Первая страница
                    if ($page > $range + 1): ?>
                        <a href="/blog?page=1"
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
                        <a href="/blog?page=<?= $i ?>"
                           style="display: inline-block; padding: 8px 12px; margin: 0 2px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: <?= $i == $page ? 'white' : '#667eea' ?>; background: <?= $i == $page ? '#667eea' : 'white' ?>;">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php
                    // Последняя страница
                    if ($page < $totalPages - $range): ?>
                        <?php if ($page < $totalPages - $range - 1): ?>
                            <span style="padding: 8px 12px; margin: 0 2px;">...</span>
                        <?php endif; ?>
                        <a href="/blog?page=<?= $totalPages ?>"
                           style="display: inline-block; padding: 8px 12px; margin: 0 2px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #667eea; background: white;">
                            <?= $totalPages ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>