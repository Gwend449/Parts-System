# 📸 Система управления фотографиями моторов

## ✅ Статус: ГОТОВО К ИСПОЛЬЗОВАНИЮ

Система загрузки и управления фотографиями моторов **полностью реализована** на базе **Livewire 3** и **Spatie Media Library**.

---

## 🚀 БЫСТРЫЙ СТАРТ

### 1. Откройте админ-панель
```
/admin/engines
```

### 2. Выберите мотор для редактирования

### 3. Заполните основные данные и сохраните форму

### 4. Внизу появится блок "Управление фотографиями"

### 5. Загрузите фото
- Нажмите "Выбрать файлы"
- Выберите 1-6 картинок (JPG, PNG, WEBP, max 5 MB)
- Нажмите "Загрузить фото"

### 6. Удалите фото
- Нажмите кнопку 🗑️ на фотографии

**✅ ВСЁ!** Фото теперь управляются с админ-панели!

---

## 📚 ДОКУМЕНТАЦИЯ

Прочитайте в этом порядке:

1. **[MEDIA_QUICKSTART.md](MEDIA_QUICKSTART.md)** ⭐ Начните здесь!
   - Быстрый старт
   - Как использовать
   - Часто задаваемые вопросы

2. **[MEDIA_GALLERY_IMPLEMENTATION.md](MEDIA_GALLERY_IMPLEMENTATION.md)**
   - Полная техническая документация
   - Примеры кода
   - Настройка и кастомизация

3. **[MEDIA_SYSTEM_SUMMARY.md](MEDIA_SYSTEM_SUMMARY.md)**
   - Техническая архитектура
   - Потоки данных
   - Все проверки и верификация

4. **[FILES_REFERENCE.md](FILES_REFERENCE.md)**
   - Справка по всем файлам
   - Быстрая навигация

5. **[GIT_COMMIT_GUIDE.md](GIT_COMMIT_GUIDE.md)**
   - Как закоммитить изменения
   - Git команды

---

## 📦 ЧТО БЫЛО СОЗДАНО

| Файл | Описание | Строк |
|------|----------|-------|
| `app/Livewire/EngineMediaManager.php` | Компонент Livewire | 132 |
| `resources/views/livewire/engine-media-manager.blade.php` | UI компонента | 134 |
| `resources/views/vendor/backpack/crud/engines_edit.blade.php` | Форма редактирования | 98 |
| `app/Http/Controllers/Admin/EnginesCrudController.php` | Контроллер (обновлен) | - |

---

## ⚙️ ТЕХНОЛОГИЯ

- **Livewire 3** - реактивные компоненты
- **Spatie Media Library** - управление медиа
- **Bootstrap 5** - стили
- **Laravel 11** - фреймворк
- **MySQL** - база данных

---

## ✨ ФУНКЦИОНАЛЬНОСТЬ

✅ Загрузка фотографий  
✅ Предпросмотр перед загрузкой  
✅ Удаление фото  
✅ Валидация (JPG, PNG, WEBP, max 5 MB)  
✅ Уведомления об успехе/ошибке  
✅ Автоматическое создание превью (thumb, preview)  
✅ Кэширование результатов  
✅ Интеграция с Spatie Media Library  
✅ Встроено в админку  

---

## 🎯 ОСНОВНЫЕ ФАЙЛЫ

### Компонент Livewire
```php
// app/Livewire/EngineMediaManager.php
class EngineMediaManager extends Component
{
    use WithFileUploads;
    
    public Engine $engine;
    public $images = [];
    public $uploadedFiles = [];
    
    public function saveMedia()    // Загрузить фото
    public function deleteImage()  // Удалить фото
    public function loadImages()   // Список фото
}
```

### View
```blade
<!-- resources/views/livewire/engine-media-manager.blade.php -->
<div class="card">
    <!-- Галерея фото -->
    @foreach($images as $image)
        <img src="{{ $image['thumb'] }}">
        <button wire:click="deleteImage({{ $image['id'] }})">🗑️</button>
    @endforeach
    
    <!-- Форма загрузки -->
    <input type="file" wire:model="uploadedFiles" multiple>
    <button wire:click="saveMedia">Загрузить</button>
</div>
```

### Форма редактирования
```blade
<!-- resources/views/vendor/backpack/crud/engines_edit.blade.php -->
<form method="post">
    <!-- Основные поля -->
    @include('crud::form_content')
</form>

<!-- Компонент управления фото -->
<livewire:engine-media-manager :engine="$entry" />
```

---

## 🔍 ПРОВЕРКИ

Все проверки пройдены ✅

- [x] Синтаксис PHP
- [x] Класс компонента загружается
- [x] View файл создан
- [x] Контроллер обновлен
- [x] Конверсии изображений настроены
- [x] Disk 'public' конфигурирован
- [x] WithFileUploads поддерживается
- [x] Spatie интегрирована

---

## 💾 ГДЕ ХРАНЯТСЯ ФАЙЛЫ

```
storage/app/public/
├── <guid>/
│   ├── original-image.jpg (оригинал)
│   ├── conversions/
│   │   ├── thumb-250x250.jpg (для каталога)
│   │   └── preview-600x600.jpg (для деталей)
│   └── generated_conversions.json
```

**URL доступа:**
```
/storage/<guid>/original-image.jpg
/storage/<guid>/conversions/thumb-250x250.jpg
```

---

## 🐛 ЧТО ДЕЛАТЬ ЕСЛИ ЧТО-ТО НЕ РАБОТАЕТ

### Фото не загружаются
```bash
mkdir -p storage/livewire-tmp
chmod 755 storage/livewire-tmp
php artisan storage:link
tail -f storage/logs/laravel.log
```

### Компонент не показывается
```bash
php -r "echo class_exists('App\Livewire\EngineMediaManager') ? 'OK' : 'NOT FOUND';"
```

### Подробнее смотрите в
👉 [MEDIA_GALLERY_IMPLEMENTATION.md](MEDIA_GALLERY_IMPLEMENTATION.md#-отладка)

---

## 📝 КОД ПРИМЕРЫ

### Загрузить файлы на фронте
```blade
@foreach($engine->getAllImages() as $image)
    <img src="{{ $image['thumb'] }}" alt="...">
@endforeach
```

### Получить все фото в контроллере
```php
$images = $engine->getAllImages();
$images = $engine->getMedia('images');
```

### Удалить фото программно
```php
$engine->deleteMedia($mediaId);
```

---

## 🎨 КАСТОМИЗАЦИЯ

### Изменить максимальный размер файла
```php
// app/Livewire/EngineMediaManager.php
'uploadedFiles.*' => 'required|file|mimes:jpeg,png,webp|max:10240', // 10 MB
```

### Изменить максимальное количество фото
```php
if ($currentImages + $newFiles > 10) {  // 10 вместо 6
    throw new \Exception('Max 10 photos');
}
```

### Изменить размер конверсий
```php
// app/Models/Engine.php
$this->addMediaConversion('thumb')
    ->crop(500, 500)  // Новый размер
    ->quality(90)     // Новое качество
    ->nonQueued();
```

---

## 💡 СОВЕТЫ И ТРЮКИ

1. **Очистить кэш фото:** `php artisan cache:clear`
2. **Просмотр логов:** `tail -f storage/logs/laravel.log`
3. **Проверить БД:** `sqlite3 database/database.sqlite "SELECT COUNT(*) FROM media;"`
4. **Очистить temp:** `rm -rf storage/livewire-tmp/*`

---

## 🔗 ПОЛЕЗНЫЕ ССЫЛКИ

- [Livewire 3 Docs](https://livewire.laravel.com/)
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary/v10/introduction)
- [Backpack Admin Panel](https://backpackforlaravel.com/)
- [Laravel Documentation](https://laravel.com/docs)

---

## 📞 ПОДДЕРЖКА

Если нужна помощь:

1. Читайте [MEDIA_GALLERY_IMPLEMENTATION.md](MEDIA_GALLERY_IMPLEMENTATION.md)
2. Проверьте раздел "🐛 Отладка"
3. Посмотрите [MEDIA_QUICKSTART.md](MEDIA_QUICKSTART.md#-если-что-то-не-работает)

---

## ✅ ГОТОВО!

Система готова к использованию!

**Начните с:** [MEDIA_QUICKSTART.md](MEDIA_QUICKSTART.md)

---

**Версия:** 1.0  
**Дата:** 30 декабря 2025 г.  
**Статус:** ✅ Production Ready
