# 🔄 ИНСТРУКЦИЯ ДЛЯ GIT КОММИТА

## Что делать когда вы готовы сохранить изменения

### Шаг 1: Проверьте статус

```bash
cd /Users/nlyapin/Templates/Parts-System
git status
```

Вы должны увидеть:

```
On branch dev

Untracked files:
  app/Livewire/EngineMediaManager.php
  resources/views/livewire/engine-media-manager.blade.php
  resources/views/vendor/backpack/crud/engines_edit.blade.php
  MEDIA_QUICKSTART.md
  MEDIA_GALLERY_IMPLEMENTATION.md
  MEDIA_SYSTEM_SUMMARY.md
  GIT_COMMIT_SUMMARY.md
  FILES_REFERENCE.md

Changes not staged for commit:
  modified: app/Http/Controllers/Admin/EnginesCrudController.php
```

### Шаг 2: Добавьте файлы в staging

```bash
# Добавить основные файлы
git add app/Livewire/EngineMediaManager.php
git add resources/views/livewire/engine-media-manager.blade.php
git add resources/views/vendor/backpack/crud/engines_edit.blade.php

# Добавить обновленный контроллер
git add app/Http/Controllers/Admin/EnginesCrudController.php

# Добавить документацию
git add MEDIA_QUICKSTART.md
git add MEDIA_GALLERY_IMPLEMENTATION.md
git add MEDIA_SYSTEM_SUMMARY.md
git add GIT_COMMIT_SUMMARY.md
git add FILES_REFERENCE.md
```

**Или все сразу:**
```bash
git add app/Livewire/EngineMediaManager.php \
        resources/views/livewire/engine-media-manager.blade.php \
        resources/views/vendor/backpack/crud/engines_edit.blade.php \
        app/Http/Controllers/Admin/EnginesCrudController.php \
        MEDIA_QUICKSTART.md \
        MEDIA_GALLERY_IMPLEMENTATION.md \
        MEDIA_SYSTEM_SUMMARY.md \
        GIT_COMMIT_SUMMARY.md \
        FILES_REFERENCE.md
```

### Шаг 3: Проверьте что добавлено

```bash
git status
```

Все файлы должны быть в секции "Changes to be committed"

### Шаг 4: Создайте коммит

```bash
git commit -m "✨ Реализация системы управления фотографиями моторов

Полностью переделана система управления фотографиями на базе Livewire 3 и Spatie Media Library.

Созданные файлы:
- app/Livewire/EngineMediaManager.php (Livewire компонент)
- resources/views/livewire/engine-media-manager.blade.php (UI)
- resources/views/vendor/backpack/crud/engines_edit.blade.php (форма редактирования)

Обновленные файлы:
- app/Http/Controllers/Admin/EnginesCrudController.php (добавлена CRUD::setEditView)

Функциональность:
✅ Загрузка фотографий (JPG, PNG, WEBP, max 5 MB)
✅ Предпросмотр перед загрузкой
✅ Удаление фото с кнопкой
✅ Валидация формата и размера
✅ Уведомления об успехе/ошибке
✅ Кэширование результатов
✅ Интеграция с Spatie Media Library
✅ Встроено в форму редактирования в админке

Технология:
- Livewire 3 с WithFileUploads
- Spatie Media Library
- Bootstrap 5 для UI
- MySQL таблица media

Документация:
- MEDIA_QUICKSTART.md (быстрый старт)
- MEDIA_GALLERY_IMPLEMENTATION.md (полная документация)
- MEDIA_SYSTEM_SUMMARY.md (технический отчет)
- GIT_COMMIT_SUMMARY.md (сводка изменений)
- FILES_REFERENCE.md (справка по файлам)"
```

Или более короткий вариант:

```bash
git commit -m "feat: Добавлена система управления фотографиями моторов на Livewire 3"
```

### Шаг 5: Отправьте на сервер

```bash
git push origin dev
```

---

## 📋 Коммит Message Template

Если хотите использовать структурированное сообщение:

```
feat: Добавлена система управления фотографиями

- Создан Livewire компонент EngineMediaManager
- Реализована загрузка, просмотр и удаление фотографий
- Встроено в форму редактирования мотора
- Добавлена валидация файлов (JPG, PNG, WEBP)
- Интегрирована с Spatie Media Library

Файлы:
- app/Livewire/EngineMediaManager.php (new)
- resources/views/livewire/engine-media-manager.blade.php (new)
- resources/views/vendor/backpack/crud/engines_edit.blade.php (new)
- app/Http/Controllers/Admin/EnginesCrudController.php (modified)

Документация:
- MEDIA_QUICKSTART.md
- MEDIA_GALLERY_IMPLEMENTATION.md
- MEDIA_SYSTEM_SUMMARY.md
```

---

## 🔍 Проверка перед коммитом

Перед коммитом убедитесь:

```bash
# 1. PHP синтаксис OK?
php -l app/Livewire/EngineMediaManager.php
php -l app/Http/Controllers/Admin/EnginesCrudController.php

# 2. Все файлы есть?
ls -la app/Livewire/EngineMediaManager.php
ls -la resources/views/livewire/engine-media-manager.blade.php
ls -la resources/views/vendor/backpack/crud/engines_edit.blade.php

# 3. Контроллер обновлен?
grep "setEditView" app/Http/Controllers/Admin/EnginesCrudController.php

# 4. Документация на месте?
ls -la MEDIA_*.md GIT_COMMIT_SUMMARY.md FILES_REFERENCE.md
```

---

## 🚀 После коммита

### Смотрите изменения

```bash
git log --oneline -10
git show HEAD
```

### Если нужно отменить коммит

```bash
# Отменить последний коммит (но оставить файлы)
git reset --soft HEAD~1

# Отменить и удалить файлы
git reset --hard HEAD~1
```

---

## 📝 Соглашение о коммитах (Conventional Commits)

Если используете conventional commits:

```
feat:    Новая функциональность ✨
fix:     Исправление ошибки 🐛
docs:    Только документация 📝
style:   Форматирование кода
refactor: Переписывание кода без изменения функциональности
perf:    Оптимизация производительности
test:    Добавление тестов
chore:   Обновление зависимостей и т.п.
ci:      Изменения CI конфигурации
```

**Для этого проекта:**
```bash
git commit -m "feat: Система управления фотографиями моторов на Livewire 3"
```

---

## 💡 Рекомендации

1. **Один коммит на одну функцию** - в этом случае всё вместе, так что один коммит
2. **Описывайте ЧТО и ПОЧЕМУ** - не только ЧТО
3. **Используйте present tense** - "Add feature", не "Added feature"
4. **Первая строка < 50 символов** - для хорошего отображения в логах
5. **Пустая строка перед подробным описанием** - git convention

---

## 📚 Полезные команды

```bash
# Просмотр staged changes
git diff --cached

# Просмотр unstaged changes
git diff

# История изменений для файла
git log -p app/Http/Controllers/Admin/EnginesCrudController.php

# Все коммиты на ветке dev
git log origin/dev

# Красивый лог
git log --oneline --graph --all
```

---

**Готово к коммиту!** ✅

Просто выполните `git commit -m "..."` и `git push origin dev`

