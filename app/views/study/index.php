<div class="content">
    <h1>Учеба</h1>

    <div style="margin-bottom: 30px;">
        <h2>Университет</h2>
        <p style="font-size: 1.2em; color: #667eea; font-weight: bold;">
            <?= htmlspecialchars($university) ?>
        </p>

        <h2>Кафедра</h2>
        <p style="font-size: 1.2em; color: #764ba2; font-weight: bold;">
            <?= htmlspecialchars($department) ?>
        </p>
    </div>

    <h2>Перечень изучаемых дисциплин</h2>

    <div style="overflow-x: auto;">
        <table>
            <thead>
            <tr>
                <th rowspan="2">№</th>
                <th rowspan="2">Дисциплина</th>
                <th rowspan="2">Кафедра</th>
                <th colspan="2">Всего часов</th>
                <th colspan="4">Аудиторные</th>
                <th rowspan="2">СРС</th>
            </tr>
            <tr>
                <th>Всего</th>
                <th>Ауд</th>
                <th>Лк</th>
                <th>Лб</th>
                <th>Пр</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($subjects as $subject): ?>
                <tr>
                    <td><?= $subject['№'] ?></td>
                    <td><?= htmlspecialchars($subject['Дисциплина']) ?></td>
                    <td><?= htmlspecialchars($subject['Кафедра']) ?></td>
                    <td><?= $subject['Всего часов'] ?></td>
                    <td><?= $subject['Аудиторные'] ?></td>
                    <td><?= $subject['Лк'] ?></td>
                    <td><?= $subject['Лб'] ?></td>
                    <td><?= $subject['Пр'] ?></td>
                    <td><?= $subject['СРС'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px; text-align: center;">
        <a href="/test" class="btn btn-primary" style="padding: 15px 30px; font-size: 1.1em;">
            Пройти тест по "Теории вероятностей и математической статистике"
        </a>
    </div>
</div>