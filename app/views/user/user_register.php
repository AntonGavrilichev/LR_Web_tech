<div class="register-form">
    <h2>Регистрация пользователя</h2>

    <?php if (isset($data['error'])): ?>
        <div class="error"><?php echo $data['error']; ?></div>
    <?php endif; ?>

    <?php if (isset($data['success'])): ?>
        <div class="success"><?php echo $data['success']; ?></div>
    <?php endif; ?>

    <form action="/user/register" method="POST">
        <div class="form-group">
            <label for="full_name">ФИО:</label>
            <input type="text" id="full_name" name="full_name" required
                   value="<?php echo $_POST['full_name'] ?? ''; ?>">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required
                   value="<?php echo $_POST['email'] ?? ''; ?>">
        </div>

        <div class="form-group">
            <label for="login">Логин:</label>
            <input type="text" id="login" name="login" required
                   value="<?php echo $_POST['login'] ?? ''; ?>">
        </div>

        <div class="form-group">
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">Зарегистрироваться</button>
    </form>

    <p>Уже есть аккаунт? <a href="/user/login">Войдите</a></p>
</div>