<?php
require_once '../config/database.php';
require_once '../app/models/BlogPost.php';
require_once '../validation/FormValidation.php'; // Из ЛР8

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    if ($_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['csv_file']['tmp_name'];

        $db = Database::getConnection();
        $stmt = $db->prepare(
            "INSERT INTO blog_posts (title, content, author, created_at) 
             VALUES (:title, :content, :author, :created_at)"
        );

        $handle = fopen($tmpName, 'r');
        $successCount = 0;

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) >= 4) {
                // Валидация с использованием FormValidation
                $validator = new FormValidation();
                $validator->setRule('title', 'required|min_length:3');
                $validator->setRule('content', 'required|min_length:10');

                if ($validator->validate(['title' => $data[0], 'content' => $data[1]])) {
                    $stmt->bindValue(':title', $data[0]);
                    $stmt->bindValue(':content', $data[1]);
                    $stmt->bindValue(':author', $data[2] ?? 'Аноним');
                    $stmt->bindValue(':created_at', $data[3] ?? date('Y-m-d H:i:s'));
                    $stmt->execute();
                    $successCount++;
                }
            }
        }

        fclose($handle);
        echo "Загружено $successCount записей!";
    }
}
?>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="csv_file" accept=".csv" required>
    <button type="submit">Загрузить CSV</button>
</form>