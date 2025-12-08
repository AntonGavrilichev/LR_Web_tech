<?php
class StatisticModel {
    private $db;
    private $table = 'statistics';

    function __construct() {
        $this->db = Database::getInstance();
    }

    // Метод для сохранения статистики
    function saveVisit($page) {
        $time = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'];
        $host = gethostbyaddr($ip);
        $browser = $_SERVER['HTTP_USER_AGENT'];

        $sql = "INSERT INTO {$this->table} (time_statistic, web_page, ip_address, host_name, browser_name) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssss', $time, $page, $ip, $host, $browser);
        return $stmt->execute();
    }

    // Получение всей статистики с пагинацией
    function getAllStatistics($limit = 20, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} ORDER BY time_statistic DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        $statistics = array();
        while ($row = $result->fetch_assoc()) {
            $statistics[] = $row;
        }
        return $statistics;
    }

    // Получение общего количества записей
    function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}