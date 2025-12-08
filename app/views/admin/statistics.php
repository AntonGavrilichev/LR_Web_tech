<div class="container">
    <!-- Кнопка выхода -->
    <div class="text-end mb-3">
        <a href="/admin/logout" class="btn btn-danger btn-sm">
            <i class="bi bi-box-arrow-right"></i> Выйти
        </a>
    </div>

    <h2>Статистика посещений</h2>
    <p>Всего записей: <?php echo $data['total_records']; ?></p>

    <table class="table">
        <thead>
        <tr>
            <th>Дата и время</th>
            <th>Страница</th>
            <th>IP-адрес</th>
            <th>Имя хоста</th>
            <th>Браузер</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data['statistics'] as $stat): ?>
            <tr>
                <td><?php echo $stat['time_formatted']; ?></td>
                <td><?php echo htmlspecialchars($stat['web_page']); ?></td>
                <td><?php echo $stat['ip_address']; ?></td>
                <td><?php echo htmlspecialchars($stat['host_name']); ?></td>
                <td><?php echo htmlspecialchars(substr($stat['browser_name'], 0, 100)); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Пагинация -->
    <?php if ($data['total_pages'] > 1): ?>
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $data['total_pages']; $i++): ?>
                    <li class="page-item <?php echo $i == $data['current_page'] ? 'active' : ''; ?>">
                        <a class="page-link" href="/admin/statistics?page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>