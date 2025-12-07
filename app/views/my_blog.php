<?php
require_once '../config/database.php';
require_once '../app/models/BlogModel.php';
require_once '../app/core/Paginator.php';

$page = $_GET['page'] ?? 1;
$paginated = BlogModel::paginate($page, Paginator::PER_PAGE, 'created_at DESC');
$paginationHtml = Paginator::generate($page, $paginated['total_pages'], '?page=');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Мой блог</title>
</head>
<body>
<h1>Мой блог</h1>

<?= $paginationHtml ?>

<?php foreach ($paginated['items'] as $post): ?>
    <article>
        <h2><?= htmlspecialchars($post->title) ?></h2>
        <p><em><?= $post->created_at ?></em></p>

        <?php if ($post->image_path): ?>
            <img src="../public/<?= $post->image_path ?>" alt="" style="max-width: 500px;">
        <?php endif; ?>

        <div><?= nl2br(htmlspecialchars($post->content)) ?></div>
        <p><strong>Автор:</strong> <?= htmlspecialchars($post->author) ?></p>
    </article>
    <hr>
<?php endforeach; ?>

<?= $paginationHtml ?>
</body>
</html>