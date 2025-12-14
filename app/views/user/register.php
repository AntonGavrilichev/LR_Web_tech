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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginInput = document.getElementById('login');
            const checkButton = document.createElement('button');
            const statusDiv = document.createElement('div');

            // Создаем кнопку проверки
            checkButton.type = 'button';
            checkButton.textContent = 'Проверить занятость';
            checkButton.className = 'btn btn-secondary';
            checkButton.style.marginLeft = '10px';
            checkButton.style.marginTop = '5px';

            // Создаем div для отображения статуса
            statusDiv.id = 'login-status';
            statusDiv.style.marginTop = '5px';
            statusDiv.style.fontWeight = 'bold';

            // Добавляем элементы после поля ввода логина
            loginInput.parentNode.appendChild(checkButton);
            loginInput.parentNode.appendChild(statusDiv);

            // Функция проверки логина через Fetch API
            function checkLoginAvailability() {
                const login = loginInput.value.trim();

                if (!login) {
                    statusDiv.textContent = 'Введите логин для проверки';
                    statusDiv.style.color = '#ff9800';
                    return;
                }

                // Минимальная длина логина
                if (login.length < 3) {
                    statusDiv.textContent = 'Логин должен быть не менее 3 символов';
                    statusDiv.style.color = '#ff9800';
                    return;
                }

                // Показываем загрузку
                statusDiv.textContent = 'Проверяем...';
                statusDiv.style.color = '#2196f3';
                checkButton.disabled = true;

                // ОТПРАВЛЯЕМ ЗАПРОС НА ПРАВИЛЬНЫЙ URL
                // Используйте абсолютный или относительный путь к check_login.php
                fetch('../api/check_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        login: login,
                        // Можно добавить CSRF токен если есть
                        // csrf_token: '<?php //echo $_SESSION["csrf_token"] ?? ""; ?>'
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            statusDiv.textContent = 'Ошибка: ' + data.error;
                            statusDiv.style.color = '#f44336';
                        } else {
                            if (data.isTaken) {
                                statusDiv.textContent = data.message;
                                statusDiv.style.color = '#f44336';
                            } else {
                                statusDiv.textContent = data.message;
                                statusDiv.style.color = '#4caf50';
                            }
                        }
                    })
                    .catch(error => {
                        statusDiv.textContent = 'Ошибка при проверке логина: ' + error.message;
                        statusDiv.style.color = '#f44336';
                        console.error('Error details:', error);
                    })
                    .finally(() => {
                        checkButton.disabled = false;
                    });
            }

            // Обработчик клика по кнопке
            checkButton.addEventListener('click', checkLoginAvailability);

            // Обработчик события blur (потеря фокуса)
            loginInput.addEventListener('blur', function() {
                if (this.value.trim().length >= 3) {
                    checkLoginAvailability();
                }
            });

            // Очистка статуса при изменении логина
            loginInput.addEventListener('input', function() {
                statusDiv.textContent = '';
            });
        });
    </script>
</div>