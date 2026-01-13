# Анализ проблемы отображения изображений

## 🔍 Текущее состояние

### В карточке каталога (`engines-catalog.blade.php`):
```html
<div class="ratio ratio-4x3 rounded-top overflow-hidden bg-white">
    <img src="{{ $imageUrl }}" class="img-fluid w-100 h-100" 
         style="object-fit: contain;" alt="{{ $engine->title }}">
</div>
```

**Проблемы:**
- `ratio ratio-4x3` — фиксирует пропорции 4:3 (может не совпадать с реальными фото)
- `object-fit: contain` + `w-100 h-100` — может увеличивать изображение, если оно меньше контейнера
- Комбинация `img-fluid` + явные ширина/высота — конфликт стилей

### На странице engine-show (`engine-show-page.blade.php`):
```html
<div class="ratio ratio-1x1 mb-3 bg-white rounded overflow-hidden">
    <img id="mainImage" src="{{ $mainImageUrl }}" class="img-fluid w-100 h-100"
        style="object-fit: contain;" alt="{{ $engine->title }}">
</div>
```

**Проблемы:**
- `ratio ratio-1x1` — квадратный контейнер, может исказить прямоугольные фото
- Увеличение для маленьких изображений (MediaLibrary конверсии 600x600, может быть меньше)

---

## ✅ Решение

### Стратегия:
1. **Не навязывать ratio** — использовать `max-width` и `auto` высоту
2. **Правильный object-fit** — `cover` для каталога, `contain` для полной версии
3. **Tailwind-first подход** — убрать bootstrap классы где можно
4. **Адаптивность** — разные размеры для мобилы

### Предложенный CSS (Tailwind):

```css
/* Для карточек каталога */
.engine-card-image {
    @apply w-full h-auto max-h-64 bg-white rounded-t overflow-hidden flex items-center justify-center;
}

.engine-card-image img {
    @apply w-full h-full object-cover;
    max-width: 100%;
    max-height: 100%;
}

/* Для полной версии товара */
.engine-preview-image {
    @apply w-full h-auto bg-white rounded overflow-hidden;
    max-width: 600px;
    margin: 0 auto;
}

.engine-preview-image img {
    @apply w-full h-auto object-contain;
    max-width: 100%;
    max-height: 600px;
}

/* Для превью */
.engine-thumbnail {
    @apply w-24 h-24 bg-white border rounded cursor-pointer transition;
    object-fit: cover;
}

.engine-thumbnail:hover {
    @apply border-brand shadow-md;
}
```

### HTML Blade изменения:

**Каталог:**
```blade
<div class="engine-card-image">
    <img src="{{ $imageUrl }}" class="img-fluid" alt="{{ $engine->title }}">
</div>
```

**Engine Show:**
```blade
<div class="engine-preview-image">
    <img id="mainImage" src="{{ $mainImageUrl }}" alt="{{ $engine->title }}">
</div>

<!-- Thumbnails -->
<div class="d-flex gap-2 flex-wrap justify-content-start mt-3">
    @foreach($engine->getAllImages() as $img)
        <img src="{{ $img['thumb'] }}" class="engine-thumbnail"
            onclick="selectThumbnail(this, '{{ $img['preview'] }}')" 
            alt="Thumbnail">
    @endforeach
</div>
```

---

## 🚀 Результат:
- ✅ Изображения в нормальном размере без искажений
- ✅ Адаптивны к разным экранам
- ✅ Нет лишнего приближения/уменьшения
- ✅ Красивые переходы и интеракция
