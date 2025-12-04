<div class="content">
    <h1>Мои интересы</h1>

    <div style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
        <h2>Быстрая навигация:</h2>
        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
            <?php foreach ($interests as $interest): ?>
                <a href="#<?= $interest['id'] ?>" class="btn" style="background: #667eea; color: white; padding: 8px 15px; border-radius: 20px; text-decoration: none;">
                    <?= htmlspecialchars($interest['title']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php foreach ($interests as $interest): ?>
        <div id="<?= $interest['id'] ?>" style="margin-bottom: 40px; padding-top: 20px;">
            <h2 style="color: #764ba2; border-bottom: 2px solid #667eea; padding-bottom: 10px;">
                <?= htmlspecialchars($interest['title']) ?>
            </h2>
            <div style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea; margin-top: 15px;">
                <?= $interest['description'] ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>