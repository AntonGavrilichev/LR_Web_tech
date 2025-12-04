<div class="content">
    <h1>Учеба</h1>

    <div style="margin-bottom: 30px;">
        <h2>Университет</h2>
        <p style="font-size: 1.2em; color: #667eea; font-weight: bold;">
            Национальный исследовательский университет "МЭИ"
        </p>

        <h2>Кафедра</h2>
        <p style="font-size: 1.2em; color: #764ba2; font-weight: bold;">
            Кафедра информационных систем
        </p>
    </div>

    <h2>ПЛАН УЧЕБНОГО ПРОЦЕССА</h2>

    <div style="overflow-x: auto; margin: 30px 0;">
        <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 14px; text-align: center;">
            <thead>
            <tr style="background: #4a6baf; color: white;">
                <th rowspan="2" style="padding: 12px; border: 1px solid #ddd; width: 5%;">№</th>
                <th rowspan="2" style="padding: 12px; border: 1px solid #ddd; width: 30%;">Дисциплина</th>
                <th rowspan="2" style="padding: 12px; border: 1px solid #ddd; width: 10%;">Кафедра</th>
                <th colspan="6" style="padding: 12px; border: 1px solid #ddd;">Всего часов</th>
            </tr>
            <tr style="background: #5a7bbf; color: white;">
                <th style="padding: 8px; border: 1px solid #ddd; width: 8%;">Всего</th>
                <th style="padding: 8px; border: 1px solid #ddd; width: 8%;">Ауд</th>
                <th style="padding: 8px; border: 1px solid #ddd; width: 8%;">Лк</th>
                <th style="padding: 8px; border: 1px solid #ddd; width: 8%;">Лб</th>
                <th style="padding: 8px; border: 1px solid #ddd; width: 8%;">Пр</th>
                <th style="padding: 8px; border: 1px solid #ddd; width: 8%;">СРС</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $subjects = [
                ['№' => 1, 'Дисциплина' => 'Экология', 'Кафедра' => 'БЖ', 'Всего' => 54, 'Ауд' => 27, 'Лк' => 18, 'Лб' => 0, 'Пр' => 9, 'СРС' => 27],
                ['№' => 2, 'Дисциплина' => 'Высшая математика', 'Кафедра' => 'ВМ', 'Всего' => 540, 'Ауд' => 282, 'Лк' => 141, 'Лб' => 0, 'Пр' => 141, 'СРС' => 258],
                ['№' => 3, 'Дисциплина' => 'Русский язык и культура речи', 'Кафедра' => 'НГиГ', 'Всего' => 108, 'Ауд' => 54, 'Лк' => 18, 'Лб' => 0, 'Пр' => 36, 'СРС' => 54],
                ['№' => 4, 'Дисциплина' => 'Основы дискретной математики', 'Кафедра' => 'ИС', 'Всего' => 216, 'Ауд' => 139, 'Лк' => 87, 'Лб' => 0, 'Пр' => 52, 'СРС' => 77],
                ['№' => 5, 'Дисциплина' => 'Основы программирования и алгоритмические языки', 'Кафедра' => 'ИС', 'Всего' => 405, 'Ауд' => 210, 'Лк' => 105, 'Лб' => 87, 'Пр' => 18, 'СРС' => 195],
                ['№' => 6, 'Дисциплина' => 'Основы экологии', 'Кафедра' => 'ПЭОП', 'Всего' => 54, 'Ауд' => 27, 'Лк' => 18, 'Лб' => 0, 'Пр' => 9, 'СРС' => 27],
                ['№' => 7, 'Дисциплина' => 'Теория вероятностей и математическая статистика', 'Кафедра' => 'ИС', 'Всего' => 162, 'Ауд' => 72, 'Лк' => 54, 'Лб' => 18, 'Пр' => 0, 'СРС' => 90],
                ['№' => 8, 'Дисциплина' => 'Физика', 'Кафедра' => 'Физики', 'Всего' => 324, 'Ауд' => 194, 'Лк' => 106, 'Лб' => 88, 'Пр' => 0, 'СРС' => 130],
                ['№' => 9, 'Дисциплина' => 'Основы электротехники и электроники', 'Кафедра' => 'ИС', 'Всего' => 108, 'Ауд' => 72, 'Лк' => 36, 'Лб' => 18, 'Пр' => 18, 'СРС' => 36],
                ['№' => 10, 'Дисциплина' => 'Численные методы в информатике', 'Кафедра' => 'ИС', 'Всего' => 189, 'Ауд' => 89, 'Лк' => 36, 'Лб' => 36, 'Пр' => 17, 'СРС' => 100],
                ['№' => 11, 'Дисциплина' => 'Методы исследования операций', 'Кафедра' => 'ИС', 'Всего' => 216, 'Ауд' => 104, 'Лк' => 52, 'Лб' => 35, 'Пр' => 17, 'СРС' => 112]
            ];

            foreach ($subjects as $index => $subject):
                $bgColor = $index % 2 == 0 ? '#f8f9fa' : '#ffffff';
                ?>
                <tr style="background: <?= $bgColor ?>;">
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $subject['№'] ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: left;"><?= htmlspecialchars($subject['Дисциплина']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($subject['Кафедра']) ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $subject['Всего'] ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $subject['Ауд'] ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $subject['Лк'] ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $subject['Лб'] ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $subject['Пр'] ?></td>
                    <td style="padding: 10px; border: 1px solid #ddd;"><?= $subject['СРС'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px; text-align: center;">
        <a href="/test" class="btn btn-primary" style="padding: 15px 30px; font-size: 1.1em; text-decoration: none;">
            Пройти тест по "Теории вероятностей и математической статистике"
        </a>
    </div>
</div>