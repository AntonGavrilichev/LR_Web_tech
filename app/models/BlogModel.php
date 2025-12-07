<?php

class BlogModel
{
    private $db;

    public function __construct()
    {
        $this->connectDB();
    }

    private function connectDB()
    {
        try {
            // Используем настройки из вашего проекта
            $host = 'localhost';
            $port = '3307';
            $dbname = 'lab9_db';
            $username = 'root';
            $password = '';

            $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
            $this->db = new PDO($dsn, $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            // Используем стиль ошибок как в вашем проекте
            die("<div style='padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px;'>
                    <h3 style='color: #721c24;'>Ошибка подключения к БД</h3>
                    <p style='color: #721c24;'>" . htmlspecialchars($e->getMessage()) . "</p>
                </div>");
        }
    }

    public function savePost($title, $content, $author = 'Аноним', $image_path = null)
    {
        $sql = "INSERT INTO blog_posts (title, content, author, image_path) 
                VALUES (:title, :content, :author, :image_path)";

        $stmt = $this->db->prepare($sql);

        $params = [
            ':title' => $title,
            ':content' => $content,
            ':author' => $author,
            ':image_path' => $image_path
        ];

        return $stmt->execute($params);
    }

    public function getPosts($page = 1, $perPage = 5)
    {
        $offset = ($page - 1) * $perPage;

        // Получаем общее количество записей
        $totalStmt = $this->db->query("SELECT COUNT(*) FROM blog_posts");
        $total = $totalStmt->fetchColumn();

        // Получаем записи с пагинацией
        $sql = "SELECT * FROM blog_posts 
                ORDER BY created_at DESC 
                LIMIT :offset, :perPage";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();

        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'posts' => $posts,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    public function deletePost($id)
    {
        $sql = "DELETE FROM blog_posts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM blog_posts WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}