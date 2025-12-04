<?php
// Проверяем переменные
$errors = $errors ?? [];
$success = $success ?? false;
$formData = $formData ?? [];
?>

<div class="content">
    <h1>Контактная форма</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <h3>✅ Сообщение успешно отправлено!</h3>
            <p>Спасибо за ваше сообщение. Я свяжусь с вами в ближайшее время.</p>
            <p><a href="/contact" class="btn btn-primary">Отправить еще одно сообщение</a></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <h3>⚠️ Обнаружены ошибки:</h3>
            <?php foreach ($errors as $field => $error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="full_name">Фамилия Имя Отчество:</label>
            <input type="text" id="full_name" name="full_name" required
                   value="<?= htmlspecialchars($formData['full_name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Пол:</label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="gender" value="male"
                            <?= ($formData['gender'] ?? 'male') === 'male' ? 'checked' : '' ?>>
                    Мужской
                </label>
                <label>
                    <input type="radio" name="gender" value="female"
                            <?= ($formData['gender'] ?? '') === 'female' ? 'checked' : '' ?>>
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
                            <?= ($formData['age'] ?? '') == $i ? 'selected' : '' ?>>
                        <?= $i ?> лет
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($formData['email'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="message">Сообщение:</label>
            <textarea id="message" name="message" rows="6" required><?=
                htmlspecialchars($formData['message'] ?? '')
                ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Отправить</button>
            <button type="reset" class="btn btn-secondary">Очистить форму</button>
        </div>
    </form>
</div>

<style>
    .alert {
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
        border: 1px solid transparent;
    }

    .alert-success {
        background: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .alert-danger {
        background: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    .form-group {
        margin-bottom: 25px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #555;
    }

    input, select, textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        transition: border-color 0.3s;
    }

    input:focus, select:focus, textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .radio-group {
        display: flex;
        gap: 30px;
        margin-top: 10px;
    }

    .radio-group label {
        display: flex;
        align-items: center;
        font-weight: normal;
        cursor: pointer;
    }

    .radio-group input {
        width: auto;
        margin-right: 10px;
    }

    .form-actions {
        margin-top: 30px;
        display: flex;
        gap: 15px;
    }

    .btn {
        padding: 12px 30px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary {
        background: #667eea;
        color: white;
    }

    .btn-primary:hover {
        background: #5a6fd8;
        transform: translateY(-2px);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }
</style>

<script>
    // Очистка формы
    document.addEventListener('DOMContentLoaded', function() {
        const resetBtn = document.querySelector('button[type="reset"]');
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();

                // Сброс всех полей формы
                const form = this.closest('form');
                form.reset();

                // Убираем сообщения об ошибках
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => alert.style.display = 'none');

                // Фокус на первое поле
                const firstInput = form.querySelector('input, select, textarea');
                if (firstInput) {
                    firstInput.focus();
                }
            });
        }

        // Валидация на стороне клиента
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

                // Убираем предыдущие ошибки
                document.querySelectorAll('.field-error').forEach(el => el.remove());

                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        isValid = false;
                        showFieldError(input, 'Это поле обязательно для заполнения');
                    }

                    // Проверка email
                    if (input.type === 'email' && input.value.trim()) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(input.value)) {
                            isValid = false;
                            showFieldError(input, 'Введите корректный email адрес');
                        }
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                }
            });
        }

        function showFieldError(input, message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.style.color = '#dc3545';
            errorDiv.style.fontSize = '14px';
            errorDiv.style.marginTop = '5px';
            errorDiv.textContent = message;

            input.parentNode.appendChild(errorDiv);
            input.style.borderColor = '#dc3545';

            // Убираем ошибку при вводе
            input.addEventListener('input', function() {
                errorDiv.remove();
                this.style.borderColor = '#ddd';
            });
        }
    });
</script>