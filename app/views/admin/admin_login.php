<div class="login-form">
    <h2>Вход администратора</h2>
    <?php if (isset($data['error'])): ?>
        <div class="error"><?php echo $data['error']; ?></div>
    <?php endif; ?>

    <form action="/admin/authorization/auth" method="POST">
        <div class="form-group">
            <label for="login">Логин:</label>
            <input type="text" id="login" name="login" required value="admin@gmail.com">
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">Войти</button>
    </form>
    <p>Пароль для теста: qwerty</p>
</div>