<?php
class UserModel extends BaseActiveRecord {
    private $db;
    private $table = 'users';

    function __construct() {
        $this->db = Database::getInstance();
    }

    function register($fullName, $email, $login, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO {$this->table} (full_name, email, login, password) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssss', $fullName, $email, $login, $hashedPassword);
        return $stmt->execute();
    }

    function login($login, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE login = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                return $row;
            }
        }
        return false;
    }

    function isLoginExists($login) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE login = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] > 0;
    }

    function getUserById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}