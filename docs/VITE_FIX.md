# 🔧 ИСПРАВЛЕНИЕ ОШИБКИ VITE

## ❌ Была ошибка
```
Unable to locate file in Vite manifest: resources/js/script.js
```

## 🔍 Причина
В `footer.blade.php` подключались оба файла отдельно:
```blade
@vite(['resources/js/app.js', 'resources/js/script.js'])
```

Но `app.js` уже импортирует `script.js`:
```javascript
// resources/js/app.js
import './bootstrap';
import './script'  // ← уже импортирован!
```

В `vite.config.js` оба файла были указаны как входные:
```javascript
input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/script.js', ...]
```

Это вызывало конфликт в манифесте.

## ✅ Решение

### 1. Обновлен `vite.config.js`
```javascript
// Было
input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/script.js', 'resources/css/styles.css']

// Стало
input: ['resources/css/app.css', 'resources/css/styles.css', 'resources/js/app.js']
```

**Почему:** `app.js` импортирует `script.js`, поэтому нужно указать только `app.js` как входную точку.

### 2. Обновлен `footer.blade.php`
```blade
<!-- Было -->
@vite(['resources/js/app.js', 'resources/js/script.js'])

<!-- Стало -->
@vite(['resources/js/app.js'])
```

**Почему:** `app.js` сам подгружает `script.js` через `import './script'`.

## 🚀 Результат
```
✅ VITE v7.2.2 ready in 143 ms
✅ Local: http://localhost:5173/
✅ Нет ошибок в манифесте
```

## 📝 Что нужно помнить
- Если файл импортируется (`import './something'`), его не нужно указывать отдельно в `vite.config.js`
- В `@vite()` подключай только главные входные файлы
- Остальные подтянутся автоматически через `import`

---

**Status: ✅ FIXED**
