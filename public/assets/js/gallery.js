// public/assets/js/gallery.js
class PhotoGallery {
    constructor(photos, photosDir) {
        this.photos = photos;
        this.photosDir = photosDir;
        this.currentIndex = 0;
        this.modal = null;
        this.init();
    }

    init() {
        // Создаем модальное окно если его нет
        if (!document.getElementById('galleryModal')) {
            this.createModal();
        }

        // Добавляем обработчики событий
        this.addEventListeners();
    }

    createModal() {
        const modalHTML = `
            <div id="galleryModal" class="modal">
                <span class="close">&times;</span>
                <span class="prev">&#10094;</span>
                <span class="next">&#10095;</span>
                
                <div class="modal-content">
                    <img id="modalImage" src="" alt="">
                    <div id="modalCaption">
                        <h3 id="modalTitle"></h3>
                        <p id="modalDescription"></p>
                        <p id="modalCounter"></p>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.getElementById('galleryModal');
    }

    addEventListeners() {
        // Закрытие по клику вне изображения
        window.addEventListener('click', (event) => {
            if (event.target === this.modal) {
                this.close();
            }
        });

        // Управление клавиатурой
        document.addEventListener('keydown', (event) => {
            if (this.modal.style.display === 'block') {
                switch(event.key) {
                    case 'Escape':
                        this.close();
                        break;
                    case 'ArrowLeft':
                        this.changePhoto(-1);
                        break;
                    case 'ArrowRight':
                        this.changePhoto(1);
                        break;
                }
            }
        });

        // Свайпы на мобильных устройствах
        let touchStartX = 0;
        let touchEndX = 0;

        this.modal.addEventListener('touchstart', (event) => {
            touchStartX = event.changedTouches[0].screenX;
        }, false);

        this.modal.addEventListener('touchend', (event) => {
            touchEndX = event.changedTouches[0].screenX;
            this.handleSwipe();
        }, false);
    }

    handleSwipe() {
        const swipeThreshold = 50;
        const diff = touchStartX - touchEndX;

        if (Math.abs(diff) > swipeThreshold) {
            if (diff > 0) {
                this.changePhoto(1); // Свайп влево = следующее фото
            } else {
                this.changePhoto(-1); // Свайп вправо = предыдущее фото
            }
        }
    }

    open(index) {
        this.currentIndex = index;
        this.updateModal();
        this.modal.style.display = 'block';
        document.body.style.overflow = 'hidden';

        // Добавляем обработчики для кнопок
        document.querySelector('.close').onclick = () => this.close();
        document.querySelector('.prev').onclick = () => this.changePhoto(-1);
        document.querySelector('.next').onclick = () => this.changePhoto(1);
    }

    close() {
        this.modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    changePhoto(direction) {
        this.currentIndex += direction;

        // Зацикливание
        if (this.currentIndex < 0) {
            this.currentIndex = this.photos.length - 1;
        } else if (this.currentIndex >= this.photos.length) {
            this.currentIndex = 0;
        }

        this.updateModal();
    }

    async updateModal() {
        const photo = this.photos[this.currentIndex];
        const photoPath = this.photosDir + photo.filename;

        try {
            // Проверяем существует ли файл
            const response = await fetch(photoPath);
            if (!response.ok) throw new Error('File not found');

            document.getElementById('modalImage').src = photoPath;
        } catch (error) {
            // Используем заглушку если файл не найден
            document.getElementById('modalImage').src =
                'https://via.placeholder.com/800x600/667eea/ffffff?text=' + encodeURIComponent(photo.name);
        }

        document.getElementById('modalTitle').textContent = photo.name;
        document.getElementById('modalDescription').textContent = photo.description;
        document.getElementById('modalCounter').textContent =
            `Фото ${this.currentIndex + 1} из ${this.photos.length}`;
    }
}

// Инициализация глобально
let gallery = null;

function initGallery(photos, photosDir) {
    gallery = new PhotoGallery(photos, photosDir);
    return gallery;
}