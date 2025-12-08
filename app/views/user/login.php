<?php
// Проверяем, определен ли макет
if (!isset($layout)) {
    $layout = 'layout';
}
?>

<div class="container">
    <h2>Вход в систему</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="/user/login" method="POST">
        <div class="form-group">
            <label for="login">Логин:</label>
            <input type="text" id="login" name="login" required class="form-control"
                   value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Войти</button>

        <p style="margin-top: 15px;">
            Нет аккаунта? <a href="/user/register">Зарегистрироваться</a>
        </p>
    </form>
</div>