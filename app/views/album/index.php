<div class="content">
    <h1>Фотоальбом</h1>

    <?php
    // Проверяем существование папки photos
    $photosDir = 'photos/';
    $hasPhotosDir = is_dir($photosDir);

    // Проверяем каждое фото
    $missingPhotos = [];
    foreach ($photos as $photo) {
        $photoPath = $photosDir . $photo['filename'];
        if (!file_exists($photoPath)) {
            $missingPhotos[] = $photo['filename'];
        }
    }
    ?>

    <?php if (!$hasPhotosDir): ?>
        <div class="alert alert-warning">
            <h3>⚠️ Папка с фотографиями не найдена!</h3>
            <p>Создайте папку <code>photos/</code> в корне проекта (рядом с index.php)</p>
        </div>
    <?php elseif (!empty($missingPhotos)): ?>
        <div class="alert alert-info">
            <h3>📁 Некоторые фото не найдены:</h3>
            <p>Добавьте эти файлы в папку <code>photos/</code>:</p>
            <ul>
                <?php foreach ($missingPhotos as $missing): ?>
                    <li><?= htmlspecialchars($missing) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="photo-grid">
        <?php foreach ($photos as $index => $photo):
            $photoPath = $photosDir . $photo['filename'];
            $photoExists = file_exists($photoPath);
            $imgSrc = $photoExists ? $photoPath : 'https://via.placeholder.com/300x200/667eea/ffffff?text=' . urlencode($photo['name']);
            ?>
            <div class="photo-item" onclick="openGallery(<?= $index ?>)" title="Нажмите для увеличения">
                <img src="<?= $imgSrc ?>"
                     alt="<?= htmlspecialchars($photo['name']) ?>"
                     data-index="<?= $index ?>"
                     onerror="this.src='https://via.placeholder.com/300x200/dc3545/ffffff?text=Ошибка+загрузки'">
                <p><strong><?= htmlspecialchars($photo['name']) ?></strong></p>
                <p><small><?= htmlspecialchars($photo['description']) ?></small></p>
                <?php if (!$photoExists): ?>
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">
                        ⚠️ Файл "<?= htmlspecialchars($photo['filename']) ?>" не найден
                    </p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Модальное окно для галереи -->
    <div id="galleryModal" class="modal">
        <span class="close" onclick="closeGallery()">&times;</span>
        <span class="prev" onclick="changePhoto(-1)">&#10094;</span>
        <span class="next" onclick="changePhoto(1)">&#10095;</span>

        <div class="modal-content">
            <img id="modalImage" src="" alt="">
            <div id="modalCaption">
                <h3 id="modalTitle"></h3>
                <p id="modalDescription"></p>
                <p id="modalCounter"></p>
            </div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="/" class="btn btn-primary">На главную страницу</a>
    </div>
    <script src="/public/assets/js/gallery.js"></script>
    <script>
        // Инициализация галереи при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            const photos = <?= json_encode($photos) ?>;
            const photosDir = '<?= $photosDir ?>';

            // Инициализируем галерею
            const gallery = initGallery(photos, photosDir);

            // Добавляем обработчики для миниатюр
            document.querySelectorAll('.photo-item').forEach((item, index) => {
                item.addEventListener('click', () => {
                    gallery.open(index);
                });
            });
        });
    </script>
</div>

<style>
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin: 30px 0;
    }

    .photo-item {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }

    .photo-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }

    .photo-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
        transition: transform 0.3s;
    }

    .photo-item:hover img {
        transform: scale(1.05);
    }

    .photo-item p {
        padding: 10px 15px;
        margin: 0;
    }

    .alert {
        padding: 15px;
        border-radius: 5px;
        margin: 20px 0;
    }

    .alert-warning {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        color: #856404;
    }

    .alert-info {
        background: #d1ecf1;
        border: 1px solid #bee5eb;
        color: #0c5460;
    }

    /* Стили для модального окна */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        overflow: auto;
    }

    .modal-content {
        position: relative;
        margin: auto;
        padding: 20px;
        width: 90%;
        max-width: 1000px;
        text-align: center;
    }

    .modal-content img {
        max-width: 100%;
        max-height: 70vh;
        object-fit: contain;
        border-radius: 5px;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
    }

    .close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: white;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 1001;
        transition: color 0.3s;
    }

    .close:hover {
        color: #667eea;
    }

    .prev, .next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        color: white;
        font-size: 50px;
        font-weight: bold;
        cursor: pointer;
        padding: 16px;
        user-select: none;
        transition: background-color 0.3s;
        z-index: 1001;
    }

    .prev {
        left: 30px;
        border-radius: 0 5px 5px 0;
    }

    .next {
        right: 30px;
        border-radius: 5px 0 0 5px;
    }

    .prev:hover, .next:hover {
        background-color: rgba(102, 126, 234, 0.8);
    }

    #modalCaption {
        color: white;
        padding: 20px;
        text-align: center;
    }

    #modalTitle {
        font-size: 24px;
        margin-bottom: 10px;
        color: #667eea;
    }

    #modalDescription {
        font-size: 16px;
        margin-bottom: 10px;
    }

    #modalCounter {
        font-size: 14px;
        color: #aaa;
    }

    @media (max-width: 768px) {
        .photo-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .prev, .next {
            font-size: 30px;
            padding: 10px;
        }

        .close {
            font-size: 30px;
            top: 10px;
            right: 20px;
        }
    }

    @media (max-width: 480px) {
        .photo-grid {
            grid-template-columns: 1fr;
        }

        .modal-content {
            width: 95%;
            padding: 10px;
        }

        .modal-content img {
            max-height: 60vh;
        }
    }
</style>

<script>
    // Данные для галереи
    let currentPhotoIndex = 0;
    const photos = <?= json_encode($photos) ?>;

    // Открыть галерею
    function openGallery(index) {
        currentPhotoIndex = index;
        updateModal();
        document.getElementById('galleryModal').style.display = 'block';
        document.body.style.overflow = 'hidden'; // Блокируем скролл
    }

    // Закрыть галерею
    function closeGallery() {
        document.getElementById('galleryModal').style.display = 'none';
        document.body.style.overflow = 'auto'; // Восстанавливаем скролл
    }

    // Переключить фото
    function changePhoto(direction) {
        currentPhotoIndex += direction;

        // Зацикливание
        if (currentPhotoIndex < 0) {
            currentPhotoIndex = photos.length - 1;
        } else if (currentPhotoIndex >= photos.length) {
            currentPhotoIndex = 0;
        }

        updateModal();
    }

    // Обновить содержимое модального окна
    function updateModal() {
        const photo = photos[currentPhotoIndex];
        const photoDir = '<?= $photosDir ?>';
        const photoPath = photoDir + photo.filename;

        // Проверяем существует ли файл
        fetch(photoPath)
            .then(response => {
                if (!response.ok) {
                    throw new Error('File not found');
                }
                return photoPath;
            })
            .catch(() => {
                return 'https://via.placeholder.com/800x600/667eea/ffffff?text=' + encodeURIComponent(photo.name);
            })
            .then(src => {
                document.getElementById('modalImage').src = src;
            });

        document.getElementById('modalTitle').textContent = photo.name;
        document.getElementById('modalDescription').textContent = photo.description;
        document.getElementById('modalCounter').textContent = `Фото ${currentPhotoIndex + 1} из ${photos.length}`;
    }

    // Закрытие по клику вне изображения
    window.onclick = function(event) {
        const modal = document.getElementById('galleryModal');
        if (event.target == modal) {
            closeGallery();
        }
    }

    // Управление клавиатурой
    document.addEventListener('keydown', function(event) {
        const modal = document.getElementById('galleryModal');
        if (modal.style.display === 'block') {
            switch(event.key) {
                case 'Escape':
                    closeGallery();
                    break;
                case 'ArrowLeft':
                    changePhoto(-1);
                    break;
                case 'ArrowRight':
                    changePhoto(1);
                    break;
            }
        }
    });
</script>