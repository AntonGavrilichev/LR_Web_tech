<?php

class CommentModel
{
    private $db;

    public function __construct()
    {
        $this->connectDB();
    }

    private function connectDB()
    {
        try {
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
            die("Ошибка подключения к БД: " . $e->getMessage());
        }
    }

    /**
     * Сохранить комментарий
     */
    public function saveComment($blogPostId, $userId, $content)
    {
        $sql = "INSERT INTO blog_comments (blog_post_id, user_id, content) 
                VALUES (:blog_post_id, :user_id, :content)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':blog_post_id', $blogPostId, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':content', $content);

            return $stmt->execute();

        } catch (PDOException $e) {
            throw new Exception("Ошибка при сохранении комментария: " . $e->getMessage());
        }
    }

    /**
     * Получить комментарии для записи блога
     */
    public function getCommentsByPostId($blogPostId)
    {
        $sql = "SELECT 
                    c.*,
                    u.full_name as author_name,
                    u.login as author_login
                FROM blog_comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.blog_post_id = :blog_post_id
                ORDER BY c.created_at DESC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':blog_post_id', $blogPostId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();

        } catch (PDOException $e) {
            throw new Exception("Ошибка при получении комментариев: " . $e->getMessage());
        }
    }

    /**
     * Получить количество комментариев для записи
     */
    public function getCommentsCount($blogPostId)
    {
        $sql = "SELECT COUNT(*) FROM blog_comments WHERE blog_post_id = :blog_post_id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':blog_post_id', $blogPostId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchColumn();

        } catch (PDOException $e) {
            throw new Exception("Ошибка при подсчете комментариев: " . $e->getMessage());
        }
    }
}
?>