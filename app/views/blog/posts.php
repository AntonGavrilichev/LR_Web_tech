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
                            <button onclick="if(confirm('Вы уверены, что хотите удалить эту запись?')) window.location.href='/admin/blog/delete?id=<?= $post['id'] ?>&redirect=blog/posts'"
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
        <?php endif; ?>
    </div>
</div>