<?php
if (!isset($layout)) {
    $layout = 'layout';
}
?>

<div class="container">
    <h2>Регистрация пользователя</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <p>Регистрация прошла успешно! Вы автоматически вошли в систему.</p>
            <p><a href="/">Перейти на главную</a></p>
        </div>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="/user/register" method="POST">
            <div class="form-group">
                <label for="full_name">ФИО:</label>
                <input type="text" id="full_name" name="full_name" required class="form-control"
                       value="<?php echo htmlspecialchars($formData['full_name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required class="form-control"
                       value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="login">Логин:</label>
                <input type="text" id="login" name="login" required class="form-control"
                       value="<?php echo htmlspecialchars($formData['login'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required class="form-control">
                <small class="form-text text-muted">Минимум 6 символов</small>
            </div>

            <button type="submit" class="btn btn-primary">Зарегистрироваться</button>

            <p style="margin-top: 15px;">
                Уже есть аккаунт? <a href="/user/login">Войти</a>
            </p>
        </form>
    <?php endif; ?>
</div>