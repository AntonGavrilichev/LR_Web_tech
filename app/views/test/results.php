<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: white;
            color: #667eea;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .back-link:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }

        .content {
            padding: 30px;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .results-table th {
            background: #667eea;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .results-table td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: top;
        }

        .results-table tr:hover {
            background: #f8f9fa;
        }

        .correct {
            color: #28a745;
            font-weight: bold;
        }

        .incorrect {
            color: #dc3545;
            font-weight: bold;
        }

        .question-details {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin: 5px 0;
        }

        .correct-answer {
            color: #28a745;
        }

        .user-answer {
            color: #007bff;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: bold;
            margin: 2px;
        }

        .status-correct {
            background: #d4edda;
            color: #155724;
        }

        .status-incorrect {
            background: #f8d7da;
            color: #721c24;
        }

        .no-results {
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 1.2rem;
        }

        .score-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 auto;
        }

        .score-high {
            background: #d4edda;
            color: #155724;
        }

        .score-medium {
            background: #fff3cd;
            color: #856404;
        }

        .score-low {
            background: #f8d7da;
            color: #721c24;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #dc3545;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
            }

            .results-table {
                display: block;
                overflow-x: auto;
            }

            .header h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        <p>Всего записей: <?= count($results) ?></p>
        <a href="/test" class="back-link">← Вернуться к тесту</a>
    </div>

    <div class="content">
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($results)): ?>
            <div class="no-results">
                <p>Нет сохраненных результатов тестирования.</p>
            </div>
        <?php else: ?>
            <table class="results-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>ФИО</th>
                    <th>Дата</th>
                    <th>Результаты</th>
                    <th>Баллы</th>
                </tr>
                </thead>
                <tbody>
                <?php
                // Создаем вспомогательную функцию прямо в шаблоне
                function getAnswerTextTemplate($questionNum, $answerCode) {
                    $answers = [
                            1 => [
                                    '1' => 'Численная мера возможности наступления события',
                                    '2' => 'Отношение числа неблагоприятных исходов к общему числу исходов',
                                    '3' => 'Сумма всех возможных исходов',
                                    '4' => 'Произведение всех возможных исходов'
                            ],
                            2 => [
                                    '1' => 'Нормальное распределение',
                                    '2' => 'Биномиальное распределение',
                                    '3' => 'Равномерное распределение',
                                    '4' => 'Распределение Пуассона'
                            ],
                            3 => [
                                    '1' => 'Среднее значение случайной величины',
                                    '2' => 'Разброс значений относительно среднего',
                                    '3' => 'Наиболее вероятное значение',
                                    '4' => 'Медианное значение'
                            ]
                    ];
                    return $answers[$questionNum][$answerCode] ?? 'Ответ не выбран';
                }
                ?>

                <?php foreach ($results as $result): ?>
                    <tr>
                        <td><?= $result['id'] ?></td>
                        <td><strong><?= htmlspecialchars($result['full_name']) ?></strong></td>
                        <td><?= $result['created_at_formatted'] ?></td>
                        <td>
                            <?php if (isset($result['user_answers_array'])): ?>
                                <?php foreach ($result['user_answers_array'] as $question => $answer): ?>
                                    <?php if (strpos($question, 'q') === 0): ?>
                                        <?php
                                        $qNum = substr($question, 1);
                                        $correct = $result['correct_answers_array'][$question] ?? '';
                                        $isCorrect = $answer == $correct;
                                        ?>
                                        <div class="question-details">
                                            <strong>Вопрос <?= $qNum ?>:</strong><br>
                                            <span class="user-answer">Ответ: <?= getAnswerTextTemplate($qNum, $answer) ?></span><br>
                                            <span class="correct-answer">Правильно: <?= getAnswerTextTemplate($qNum, $correct) ?></span><br>
                                            <span class="status-badge <?= $isCorrect ? 'status-correct' : 'status-incorrect' ?>">
                                                <?= $isCorrect ? '✓ Верно' : '✗ Неверно' ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
<!--                        <td>-->
<!--                            <span class="--><?php //= $result['is_correct'] ? 'correct' : 'incorrect' ?><!--">-->
<!--                                --><?php //= $result['is_correct'] ? 'ДА' : 'НЕТ' ?>
<!--                            </span>-->
<!--                        </td>-->
                        <td>
                            <div class="score-circle
                                        <?= $result['score'] >= 80 ? 'score-high' :
                                    ($result['score'] >= 60 ? 'score-medium' : 'score-low') ?>">
                                <?= $result['score'] ?>%
                            </div>
                            <div style="text-align: center; margin-top: 5px; font-size: 0.9rem;">
                                <?= $result['correct_count'] ?>/<?= $result['total_questions'] ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>