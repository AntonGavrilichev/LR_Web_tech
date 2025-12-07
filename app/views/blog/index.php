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
</div>