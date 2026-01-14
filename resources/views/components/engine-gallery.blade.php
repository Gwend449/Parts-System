@props(['images'])

<div class="engine-gallery" id="engineGallery" data-images='@json($images)'>
   <!-- Main Image -->
   <div class="main-image-wrapper">
      <img id="mainImage" class="main-image" alt="Main engine image" style="cursor: pointer;">
   </div>

   <!-- Thumbnails -->
   <div class="thumbnails-container" id="thumbnailsContainer">
      @forelse($images as $index => $image)
         <img src="{{ $image['thumb'] }}" class="thumbnail" alt="Thumbnail {{ $index + 1 }}" data-index="{{ $index }}"
            style="cursor: pointer;">
      @empty
         <p class="text-muted">Нет изображений</p>
      @endforelse
   </div>

   <!-- Modal Gallery -->
   <div id="galleryModal" class="gallery-modal" style="display: none;">
      <button id="modalClose" class="modal-close" title="Закрыть">✕</button>

      <div class="modal-content">
         <img id="modalImage" class="modal-image" alt="Engine image fullsize">

         <!-- Navigation -->
         <button id="prevBtn" class="nav-btn prev-btn" title="Предыдущее">‹</button>
         <button id="nextBtn" class="nav-btn next-btn" title="Следующее">›</button>
      </div>

      <!-- Indicators -->
      <div class="indicators" id="indicators">
         <span id="imageCounter"></span>
      </div>
   </div>
</div>

<script>
   document.addEventListener('DOMContentLoaded', function () {
      const gallery = document.getElementById('engineGallery');
      if (!gallery) return;

      const imagesData = JSON.parse(gallery.getAttribute('data-images'));
      if (!imagesData || imagesData.length === 0) return;

      let currentIndex = 0;
      const modal = document.getElementById('galleryModal');
      const mainImage = document.getElementById('mainImage');
      const modalImage = document.getElementById('modalImage');
      const thumbnailsContainer = document.getElementById('thumbnailsContainer');
      const indicators = document.getElementById('indicators');

      // Инициализация главного изображения
      function updateMainImage() {
         // Показываем оригинал, чтобы исключить артефакты конверсий
         mainImage.src = imagesData[currentIndex].original || imagesData[currentIndex].preview;
      }

      // Обновить модальное изображение
      function updateModalImage() {
         // В полноэкранной галерее показываем оригинал без сжатия для максимального качества
         modalImage.src = imagesData[currentIndex].original || imagesData[currentIndex].preview;
         document.getElementById('imageCounter').textContent = `${currentIndex + 1} / ${imagesData.length}`;
         indicators.style.display = imagesData.length > 1 ? 'block' : 'none';
      }

      // Обновить активный thumbnail
      function updateThumbnails() {
         document.querySelectorAll('#thumbnailsContainer .thumbnail').forEach((thumb, i) => {
            thumb.classList.toggle('active', i === currentIndex);
         });
      }

      // Открыть модальное окно
      function openModal() {
         if (imagesData.length === 0) return;
         modal.style.display = 'flex';
         document.body.style.overflow = 'hidden';
         updateModalImage();
      }

      // Закрыть модальное окно
      function closeModal() {
         modal.style.display = 'none';
         document.body.style.overflow = 'auto';
      }

      // Переключить изображение
      function goToImage(index) {
         currentIndex = (index + imagesData.length) % imagesData.length;
         updateMainImage();
         updateThumbnails();
      }

      // События
      mainImage.addEventListener('click', openModal);
      document.getElementById('modalClose').addEventListener('click', closeModal);
      document.getElementById('prevBtn').addEventListener('click', () => {
         currentIndex = (currentIndex - 1 + imagesData.length) % imagesData.length;
         updateModalImage();
      });
      document.getElementById('nextBtn').addEventListener('click', () => {
         currentIndex = (currentIndex + 1) % imagesData.length;
         updateModalImage();
      });

      // Клик на моду закрывает её
      modal.addEventListener('click', (e) => {
         if (e.target === modal) closeModal();
      });

      // Навигация с клавиатуры
      document.addEventListener('keydown', (e) => {
         if (modal.style.display !== 'flex') return;
         if (e.key === 'ArrowLeft') {
            currentIndex = (currentIndex - 1 + imagesData.length) % imagesData.length;
            updateModalImage();
         }
         if (e.key === 'ArrowRight') {
            currentIndex = (currentIndex + 1) % imagesData.length;
            updateModalImage();
         }
         if (e.key === 'Escape') closeModal();
      });

      // Клики на thumbnails
      thumbnailsContainer.addEventListener('click', (e) => {
         if (e.target.classList.contains('thumbnail')) {
            const index = parseInt(e.target.getAttribute('data-index'));
            goToImage(index);
         }
      });

      // Инициализация
      updateMainImage();
      updateThumbnails();
      document.getElementById('prevBtn').style.visibility = imagesData.length <= 1 ? 'hidden' : 'visible';
      document.getElementById('nextBtn').style.visibility = imagesData.length <= 1 ? 'hidden' : 'visible';
   });
</script>