<?php if ($results): ?>
    <div style="background: #e9ecef; padding: 25px; border-radius: 8px; margin: 25px 0;">
        <h2 style="color: #667eea;">Результаты теста</h2>
        <p><strong>ФИО:</strong> <?= htmlspecialchars($_POST['full_name']) ?></p>
        <p><strong>Группа:</strong> <?= htmlspecialchars($_POST['group']) ?></p>
        <p><strong>Правильных ответов:</strong> <?= $results['correct'] ?> из <?= $results['total'] ?></p>
        <p><strong>Процент правильных ответов:</strong> <?= $results['percentage'] ?>%</p>

        <h3 style="margin-top: 20px;">Детализация:</h3>
        <?php foreach ($results['details'] as $q => $detail): ?>
            <div style="margin: 10px 0; padding: 10px; background: <?= $detail['is_correct'] ? '#d4edda' : '#f8d7da' ?>; border-radius: 5px;">
                <p><strong>Вопрос <?= substr($q, 1) ?>:</strong>
                    <?= $detail['is_correct'] ? '✅ Правильно' : '❌ Неправильно' ?>
                </p>
                <p>Ваш ответ: <?= $detail['user'] ?></p>
                <?php if (!$detail['is_correct']): ?>
                    <p>Правильный ответ: <?= $detail['correct'] ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div style="margin-top: 20px;">
            <a href="/test" class="btn btn-primary">Пройти тест еще раз</a>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="errors">
        <h3>Обнаружены ошибки:</h3>
        <?php foreach ($errors as $error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="full_name">Фамилия Имя Отчество:</label>
        <input type="text" id="full_name" name="full_name" required
               value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="group">Группа:</label>
        <select id="group" name="group" required>
            <option value="">Выберите группу</option>
            <optgroup label="2 курс">
                <option value="ИС-21" <?= ($_POST['group'] ?? '') === 'ИС-21' ? 'selected' : '' ?>>ИС-21</option>
                <option value="ИС-22" <?= ($_POST['group'] ?? '') === 'ИС-22' ? 'selected' : '' ?>>ИС-22</option>
                <option value="ИС-23" <?= ($_POST['group'] ?? '') === 'ИС-23' ? 'selected' : '' ?>>ИС-23</option>
            </optgroup>
            <optgroup label="3 курс">
                <option value="ИС-31" <?= ($_POST['group'] ?? '') === 'ИС-31' ? 'selected' : '' ?>>ИС-31</option>
                <option value="ИС-32" <?= ($_POST['group'] ?? '') === 'ИС-32' ? 'selected' : '' ?>>ИС-32</option>
                <option value="ИС-33" <?= ($_POST['group'] ?? '') === 'ИС-33' ? 'selected' : '' ?>>ИС-33</option>
            </optgroup>
            <optgroup label="4 курс">
                <option value="ИС-41" <?= ($_POST['group'] ?? '') === 'ИС-41' ? 'selected' : '' ?>>ИС-41</option>
                <option value="ИС-42" <?= ($_POST['group'] ?? '') === 'ИС-42' ? 'selected' : '' ?>>ИС-42</option>
                <option value="ИС-43" <?= ($_POST['group'] ?? '') === 'ИС-43' ? 'selected' : '' ?>>ИС-43</option>
            </optgroup>
        </select>
    </div>

    <hr style="margin: 30px 0;">

    <h2>Вопросы теста</h2>

    <div class="form-group">
        <h3>1. Что такое вероятность события?</h3>
        <div class="radio-group">
            <label><input type="radio" name="q1" value="1" required <?= ($_POST['q1'] ?? '') == '1' ? 'checked' : '' ?>> Численная мера возможности наступления события</label><br>
            <label><input type="radio" name="q1" value="2" <?= ($_POST['q1'] ?? '') == '2' ? 'checked' : '' ?>> Отношение числа неблагоприятных исходов к общему числу исходов</label><br>
            <label><input type="radio" name="q1" value="3" <?= ($_POST['q1'] ?? '') == '3' ? 'checked' : '' ?>> Сумма всех возможных исходов</label><br>
            <label><input type="radio" name="q1" value="4" <?= ($_POST['q1'] ?? '') == '4' ? 'checked' : '' ?>> Произведение всех возможных исходов</label>
        </div>
    </div>

    <div class="form-group">
        <h3>2. Какое распределение используется для моделирования числа успехов в n независимых испытаниях?</h3>
        <div class="radio-group">
            <label><input type="radio" name="q2" value="1" required <?= ($_POST['q2'] ?? '') == '1' ? 'checked' : '' ?>> Нормальное распределение</label><br>
            <label><input type="radio" name="q2" value="2" <?= ($_POST['q2'] ?? '') == '2' ? 'checked' : '' ?>> Биномиальное распределение</label><br>
            <label><input type="radio" name="q2" value="3" <?= ($_POST['q2'] ?? '') == '3' ? 'checked' : '' ?>> Равномерное распределение</label><br>
            <label><input type="radio" name="q2" value="4" <?= ($_POST['q2'] ?? '') == '4' ? 'checked' : '' ?>> Распределение Пуассона</label>
        </div>
    </div>

    <div class="form-group">
        <h3>3. Что показывает дисперсия случайной величины?</h3>
        <div class="radio-group">
            <label><input type="radio" name="q3" value="1" required <?= ($_POST['q3'] ?? '') == '1' ? 'checked' : '' ?>> Среднее значение случайной величины</label><br>
            <label><input type="radio" name="q3" value="2" <?= ($_POST['q3'] ?? '') == '2' ? 'checked' : '' ?>> Разброс значений относительно среднего</label><br>
            <label><input type="radio" name="q3" value="3" <?= ($_POST['q3'] ?? '') == '3' ? 'checked' : '' ?>> Наиболее вероятное значение</label><br>
            <label><input type="radio" name="q3" value="4" <?= ($_POST['q3'] ?? '') == '4' ? 'checked' : '' ?>> Медианное значение</label>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Отправить тест</button>
        <button type="reset" class="btn btn-secondary">Очистить форму</button>
    </div>
</form>