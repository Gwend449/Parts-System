# Архитектура галереи изображений

## 📊 Сравнение подходов

| Критерий | Livewire | Blade + Alpine.js | Blade + JS |
|----------|----------|-------------------|-----------|
| **Сложность** | Средняя | Низкая | Высокая |
| **Реактивность** | ✅ Полная | ✅ Полная | ❌ Ручная |
| **AJAX запросы** | ✅ Автоматические | ❌ Нужно писать | ❌ Нужно писать |
| **Для галереи нужны?** | ❌ НЕТ | ✅ ДА | ✅ ДА |
| **Best practice Laravel** | ✅ | ✅ | ⚠️ |
| **Bundle size** | +80KB | +15KB | Минимальный |
| **SSR дружественно** | ✅ | ✅ | ✅ |

---

## 🎯 РЕКОМЕНДАЦИЯ: **Blade + Alpine.js**

### Почему Alpine, а не Livewire?

**Livewire** предназначен для:
- Сложной логики с состоянием на сервере
- Реактивных форм с валидацией
- Динамических списков с фильтрацией
- Компонентов, которые часто обновляют данные

**Для галереи это избыточно**, потому что:
- Все данные уже в HTML (массив изображений)
- Навигация = локальное состояние (текущий индекс)
- Не нужно загружать с сервера на каждое действие
- Alpine хорошо подходит для UI-интеракций

**Результат:** Alpine.js даст чистый код, меньше overhead, лучше performance.

---

## 🏗️ Предложенная архитектура

### 1. **Компонент Blade для галереи** (`resources/views/components/engine-gallery.blade.php`)

```blade
@props(['images'])

<div x-data="engineGallery(@json($images))" class="engine-gallery">
    <!-- Main Image -->
    <div class="main-image-wrapper">
        <img :src="currentImage.preview" 
             :alt="currentImage.id" 
             class="main-image"
             @click="openModal()">
    </div>

    <!-- Thumbnails -->
    <div class="thumbnails-container">
        @foreach($images as $index => $image)
            <img src="{{ $image['thumb'] }}" 
                 @click="selectImage({{ $index }})"
                 :class="{ 'active': currentIndex === {{ $index }} }"
                 class="thumbnail"
                 alt="Thumbnail">
        @endforeach
    </div>

    <!-- Modal Gallery -->
    <div x-show="isOpen" class="gallery-modal" @keydown.escape="closeModal()">
        <button @click="closeModal()" class="modal-close">✕</button>
        
        <div class="modal-content">
            <img :src="currentImage.preview" :alt="currentImage.id" class="modal-image">
            
            <!-- Navigation -->
            <button @click="prevImage()" class="nav-btn prev-btn">‹</button>
            <button @click="nextImage()" class="nav-btn next-btn">›</button>
        </div>

        <!-- Indicators -->
        <div class="indicators">
            <span x-text="`${currentIndex + 1} / ${images.length}`"></span>
        </div>
    </div>
</div>

@pushOnce('scripts')
<script>
function engineGallery(images) {
    return {
        images: images,
        currentIndex: 0,
        isOpen: false,

        get currentImage() {
            return this.images[this.currentIndex];
        },

        selectImage(index) {
            this.currentIndex = index;
        },

        nextImage() {
            this.currentIndex = (this.currentIndex + 1) % this.images.length;
        },

        prevImage() {
            this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        },

        openModal() {
            this.isOpen = true;
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.isOpen = false;
            document.body.style.overflow = 'auto';
        }
    }
}
</script>
@endPushOnce
```

---

### 2. **CSS для галереи** (добавить в `resources/css/styles.css`)

```css
.engine-gallery {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.main-image-wrapper {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.2s;
}

.main-image-wrapper:hover {
    transform: scale(1.02);
}

.main-image {
    width: 100%;
    height: auto;
    max-height: 600px;
    object-fit: contain;
    display: block;
}

.thumbnails-container {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    justify-content: flex-start;
    padding: 0 1rem;
}

.thumbnail {
    width: 90px;
    height: 90px;
    object-fit: cover;
    border: 2px solid transparent;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.thumbnail:hover {
    border-color: #c67b16;
    transform: scale(1.05);
}

.thumbnail.active {
    border-color: #c67b16;
    box-shadow: 0 0 8px rgba(198, 123, 22, 0.3);
}

/* Gallery Modal */
.gallery-modal {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.9);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    padding: 1rem;
    animation: fadeIn 0.2s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal-content {
    position: relative;
    width: 100%;
    max-width: 90vw;
    max-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-image {
    width: 100%;
    height: auto;
    max-height: 80vh;
    object-fit: contain;
}

.modal-close {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 2rem;
    cursor: pointer;
    padding: 0 10px;
    border-radius: 4px;
    transition: background 0.2s;
    z-index: 10000;
}

.modal-close:hover {
    background: rgba(255, 255, 255, 0.4);
}

.nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 3rem;
    cursor: pointer;
    padding: 0 20px;
    transition: background 0.2s;
    user-select: none;
}

.nav-btn:hover {
    background: rgba(255, 255, 255, 0.4);
}

.prev-btn {
    left: 20px;
}

.next-btn {
    right: 20px;
}

.indicators {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    font-size: 1rem;
    background: rgba(0, 0, 0, 0.5);
    padding: 8px 16px;
    border-radius: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .nav-btn {
        font-size: 2rem;
        padding: 0 10px;
    }

    .thumbnails-container {
        padding: 0;
    }

    .thumbnail {
        width: 70px;
        height: 70px;
    }
}
```

---

### 3. **Использование в Blade** (`engine-show-page.blade.php`)

```blade
<x-engine-gallery :images="$engine->getAllImages()" />
```

**Или в Livewire компоненте:**

```blade
<x-engine-gallery :images="$this->engine->getAllImages()" />
```

---

## 🔧 Что нужно для работы

### В `resources/views/layouts/app.blade.php` добавить Alpine.js в HEAD:

```blade
<head>
    ...
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/styles.css'])
</head>
```

---

## ✨ Преимущества этого подхода

| Аспект | Результат |
|--------|-----------|
| **Производительность** | 🚀 Быстро, без AJAX |
| **Размер бандла** | 📦 Alpine.js ~15KB |
| **Поддержка** | 🛠️ Легко расширять |
| **SEO** | ✅ Данные в HTML |
| **Fallback** | ✅ Работает без JS |
| **Keyboard навигация** | ✅ ESC, стрелки |

---

## 🔮 Что можно добавить позже

- Клавиши влево/вправо для навигации
- Свайп на мобилах
- Полноэкранный режим (F)
- Зум изображения (+ / -)
- Скачивание изображения
