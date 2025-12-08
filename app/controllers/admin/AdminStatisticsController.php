<?php


class AdminStatisticsController extends AdminController {

    public function __construct() {
        parent::__construct();
    }

    // Статистика посещений (п.3 задания)
    public function index() {
        // Настройка пагинации
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Получение общей статистики
        $totalStmt = $this->db->query("SELECT COUNT(*) as total FROM statistics");
        $total = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Получение данных с пагинацией
        $stmt = $this->db->prepare("SELECT * FROM statistics ORDER BY time_statistic DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $statistics = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Форматирование дат
        foreach ($statistics as &$stat) {
            $stat['time_formatted'] = date('d.m.Y H:i:s', strtotime($stat['time_statistic']));
        }

        $data = [
            'title' => 'Статистика посещений',
            'pageTitle' => 'Статистика посещений сайта',
            'statistics' => $statistics,
            'current_page' => $page,
            'total_pages' => ceil($total / $limit),
            'total_records' => $total
        ];

        $this->view->render('admin/statistics', $data);
    }
}
?>