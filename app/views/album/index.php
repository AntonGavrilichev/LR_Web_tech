<div class="content">
    <h1>Фотоальбом</h1>

    <div class="photo-grid">
        <?php foreach ($photos as $photo): ?>
            <div class="photo-item" title="<?= htmlspecialchars($photo['description']) ?>">
                <img src="https://via.placeholder.com/300x200/667eea/ffffff?text=<?= urlencode($photo['name']) ?>"
                     alt="<?= htmlspecialchars($photo['name']) ?>">
                <p><strong><?= htmlspecialchars($photo['name']) ?></strong></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="/" class="btn btn-primary">На главную страницу</a>
    </div>
</div>