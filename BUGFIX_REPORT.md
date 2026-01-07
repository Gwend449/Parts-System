# 🔧 Исправления ошибок в оптимизации изображений

## ✅ Что было исправлено:

### 1. ❌ Ошибка: "Call to undefined method addMediaConversion()"
**Причина:** Неправильное использование API Media Library
**Решение:** Переместили регистрацию конверсий из сервиса прямо в метод `registerMediaConversions()` модели

**Было:**
```php
public function registerMediaConversions($media = null): void
{
    ImageOptimizationService::registerConversions($media);
}
```

**Стало:**
```php
public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
{
    $this->addMediaConversion('thumb')
        ->crop(250, 250)
        ->quality(75)
        ->nonQueued();

    $this->addMediaConversion('preview')
        ->crop(600, 600)
        ->quality(75)
        ->nonQueued();
}
```

---

### 2. ❌ Ошибка: "htmlspecialchars(): Argument #1 ($string) must be of type string, array given"
**Причина:** `getAllImages()` возвращает массив структурированных объектов, но в Blade использовались как строка
**Решение:** Обновили все шаблоны для правильного доступа к данным

**Было в шаблонах:**
```blade
<img src="{{ $engine->getAllImages()[0] ?? asset('placeholder.jpg') }}">
```

**Стало:**
```blade
@php
    $images = $engine->getAllImages();
    $imageUrl = isset($images[0]) ? $images[0]['thumb'] : asset('placeholder.jpg');
@endphp
<img src="{{ $imageUrl }}">
```

**Обновлены файлы:**
- ✅ `resources/views/livewire/catalog/recent-engines.blade.php`
- ✅ `resources/views/livewire/catalog/engines-catalog.blade.php`
- ✅ `resources/views/livewire/engine-show-page.blade.php`

---

## 📝 Структура данных getAllImages()

Метод теперь возвращает правильную структуру:

```php
[
    [
        'original' => '/storage/1/engine.jpg',      // оригинал (2.5 MB)
        'thumb' => '/storage/1/...-thumb.jpg',      // превью 250x250 (45 KB)
        'preview' => '/storage/1/...-preview.jpg',  // превью 600x600 (120 KB)
        'id' => 123,                                 // ID медиа
        'type' => 'uploaded'                         // тип (uploaded или folder)
    ],
    // ...
]
```

---

## 🎯 Как использовать в шаблонах:

### Для каталога (быстрая загрузка):
```blade
@php
    $images = $engine->getAllImages();
    $thumbnail = isset($images[0]) ? $images[0]['thumb'] : asset('placeholder.jpg');
@endphp
<img src="{{ $thumbnail }}" alt="{{ $engine->title }}">
```

### Для страницы товара (хорошее качество):
```blade
@php
    $images = $engine->getAllImages();
    $preview = isset($images[0]) ? $images[0]['preview'] : asset('placeholder.jpg');
@endphp
<img src="{{ $preview }}" alt="{{ $engine->title }}">
```

### Для галереи миниатюр:
```blade
@foreach($engine->getAllImages() as $image)
    <img src="{{ $image['thumb'] }}" 
         onclick="showLarge('{{ $image['preview'] }}')"
         class="thumbnail">
@endforeach
```

---

## ✨ Проверка работы:

```bash
# 1. Очистите кэш
php artisan cache:clear

# 2. Загрузите фотографию в админке
# (Admin → Edit Мотора → Фотографии → Save)

# 3. Проверьте что конверсии создались
php artisan images:test-conversions

# 4. Откройте каталог - должно работать без ошибок
# 5. Откройте страницу товара - галерея должна работать
```

---

## 📋 Список измененных файлов:

**Функциональность:**
- ✅ `app/Models/Engine.php` - исправлены конверсии
- ✅ `app/Services/ImageOptimizationService.php` - упрощен до конфиг-сервиса

**Шаблоны:**
- ✅ `resources/views/livewire/catalog/recent-engines.blade.php`
- ✅ `resources/views/livewire/catalog/engines-catalog.blade.php`
- ✅ `resources/views/livewire/engine-show-page.blade.php`

---

## 🚀 Следующие шаги:

1. Закоммитьте исправления:
```bash
git add .
git commit -m "fix: correct media conversions and image display in views"
git push origin dev
```

2. На сервере:
```bash
git pull origin dev
php artisan cache:clear
php artisan config:clear
systemctl reload nginx
```

3. Протестируйте:
- Загрузите фотографию в админке
- Проверьте каталог
- Проверьте страницу товара

---

**Статус:** ✅ Исправлено
**Версия:** 1.1 (с исправлениями)
**Дата:** 30 декабря 2025
