<div class="content">
    <h1 style="color: #764ba2; margin-bottom: 30px;">Гостевая книга</h1>

    <!-- Форма для добавления сообщения -->
    <div style="margin-bottom: 40px; padding: 25px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e0e0e0;">
        <h2 style="color: #667eea; margin-bottom: 20px;">Оставьте ваш отзыв</h2>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($formErrors)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <strong>Ошибки:</strong>
                <ul style="margin: 10px 0 0 20px;">
                    <?php foreach ($formErrors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Фамилия *</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($formData['last_name'] ?? '') ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Имя *</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($formData['first_name'] ?? '') ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Отчество</label>
                <input type="text" name="patronymic" value="<?= htmlspecialchars($formData['patronymic'] ?? '') ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">E-mail *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>

            <div style="grid-column: 1 / -1;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Текст отзыва *</label>
                <textarea name="message" rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;"><?= htmlspecialchars($formData['message'] ?? '') ?></textarea>
            </div>

            <div style="grid-column: 1 / -1;">
                <button type="submit" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; border: none; border-radius: 25px; font-size: 16px; cursor: pointer;">
                    Отправить отзыв
                </button>
                <small style="margin-left: 15px; color: #666;">* Обязательные поля</small>
            </div>
        </form>
    </div>

    <!-- Таблица сообщений -->
    <div>
        <h2 style="color: #667eea; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #764ba2;">
            Сообщения гостей (<?= count($messages) ?>)
        </h2>

        <?php if (empty($messages)): ?>
            <div style="text-align: center; padding: 40px; background: white; border-radius: 8px; border: 1px solid #e0e0e0;">
                <p style="color: #666; font-size: 18px;">Пока нет сообщений. Будьте первым!</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden;">
                    <thead>
                    <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <th style="padding: 15px; text-align: left; font-weight: bold;">Дата</th>
                        <th style="padding: 15px; text-align: left; font-weight: bold;">ФИО</th>
                        <th style="padding: 15px; text-align: left; font-weight: bold;">E-mail</th>
                        <th style="padding: 15px; text-align: left; font-weight: bold;">Отзыв</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($messages as $index => $message): ?>
                        <tr style="border-bottom: 1px solid #e0e0e0; <?= $index % 2 == 0 ? 'background: #f9f9f9;' : '' ?>">
                            <td style="padding: 15px; vertical-align: top; width: 100px;">
                                    <span style="display: inline-block; background: #667eea; color: white; padding: 5px 10px; border-radius: 15px; font-size: 14px;">
                                        <?= htmlspecialchars($message['date']) ?>
                                    </span>
                            </td>
                            <td style="padding: 15px; vertical-align: top;">
                                <strong><?= htmlspecialchars($message['last_name'] . ' ' . $message['first_name']) ?></strong>
                                <?php if (!empty($message['patronymic'])): ?>
                                    <br><small><?= htmlspecialchars($message['patronymic']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 15px; vertical-align: top;">
                                <a href="mailto:<?= htmlspecialchars($message['email']) ?>" style="color: #667eea; text-decoration: none;">
                                    <?= htmlspecialchars($message['email']) ?>
                                </a>
                            </td>
                            <td style="padding: 15px; vertical-align: top;">
                                <?= nl2br(htmlspecialchars($message['message'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    input:focus, textarea:focus {
        outline: none;
        border-color: #667eea !important;
        box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
    }

    .content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    @media (max-width: 768px) {
        table {
            font-size: 14px;
        }

        th, td {
            padding: 10px !important;
        }

        form {
            grid-template-columns: 1fr !important;
        }
    }
</style>