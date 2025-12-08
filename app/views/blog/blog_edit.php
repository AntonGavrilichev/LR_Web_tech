<?php
require_once 'F:\Учеба\Веб-технологии\lb8\config\database.php';
require_once 'F:\Учеба\Веб-технологии\lb8\app\models\BlogModel.php';
require_once 'F:\Учеба\Веб-технологии\lb8\app\core\Paginator.php';

// Добавление записи
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagePath = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'F:\Учеба\Веб-технологии\lb8\public\uploads';
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename);
        $imagePath = 'F:\Учеба\Веб-технологии\lb8\public\uploads' . $filename;
    }

    $post = new BlogModel([
        'title' => $_POST['title'],
        'image_path' => $imagePath,
        'content' => $_POST['content']
    ]);

    $post->save();
    header('Location: ?success=1');
    exit;
}

// Пагинация
//$page = $_GET['page'] ?? 1;
//$paginated = BlogModel::paginate($page, Paginator::PER_PAGE, 'created_at DESC');
//$paginationHtml = Paginator::generate($page, $paginated['total_pages'], '?page=');
//?>
<!DOCTYPE html>
<html>
<head>
    <title>Редактор блога</title>
</head>
<body>
<h1>Редактор блога</h1>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Тема" required>
    <input type="file" name="image" accept="image/*">
    <textarea name="content" placeholder="Текст" rows="10" required></textarea>
    <button type="submit">Добавить</button>
</form>

<h2>Записи:</h2>
<?= $paginationHtml ?>

<?php foreach ($paginated['items'] as $post): ?>
    <div class="post">
        <h3><?= htmlspecialchars($post->title) ?></h3>
        <?php if ($post->image_path): ?>
            <img src="F:\Учеба\Веб-технологии\lb8\public\<?= $post->image_path ?>" alt="" style="max-width: 300px;">
        <?php endif; ?>
        <p><?= nl2br(htmlspecialchars($post->content)) ?></p>
        <small><?= $post->created_at ?></small>
    </div>
    <hr>
<?php endforeach; ?>

<?= $paginationHtml ?>
</body>
</html>