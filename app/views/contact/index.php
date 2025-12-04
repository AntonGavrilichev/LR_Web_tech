<?php if ($success): ?>
    <div style="background: #d4edda; color: #155724; padding: 20px; border-radius: 5px; margin: 20px 0;">
        <h3>Сообщение успешно отправлено!</h3>
        <p>Спасибо за ваше сообщение. Я свяжусь с вами в ближайшее время.</p>
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
        <label>Пол:</label>
        <div class="radio-group">
            <label>
                <input type="radio" name="gender" value="male"
                    <?= ($_POST['gender'] ?? 'male') === 'male' ? 'checked' : '' ?>>
                Мужской
            </label>
            <label>
                <input type="radio" name="gender" value="female"
                    <?= ($_POST['gender'] ?? '') === 'female' ? 'checked' : '' ?>>
                Женский
            </label>
        </div>
    </div>

    <div class="form-group">
        <label for="age">Возраст:</label>
        <select id="age" name="age" required>
            <option value="">Выберите возраст</option>
            <?php for ($i = 18; $i <= 65; $i++): ?>
                <option value="<?= $i ?>"
                    <?= ($_POST['age'] ?? '') == $i ? 'selected' : '' ?>>
                    <?= $i ?> лет
                </option>
            <?php endfor; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="message">Сообщение:</label>
        <textarea id="message" name="message" rows="6" required><?=
            htmlspecialchars($_POST['message'] ?? '')
            ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Отправить</button>
        <button type="reset" class="btn btn-secondary">Очистить форму</button>
    </div>
</form>