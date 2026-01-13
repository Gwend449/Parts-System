# Диагностика проблемы с CSS на localhost

## 🔍 Найденные проблемы

### 1. **Vite не подключен корректно в layout**

**Текущее в `resources/views/layouts/header.blade.php`:**
```blade
@vite(['resources/css/styles.css'])
```

**Проблема:**
- Только `styles.css` импортируется
- `app.css` (Tailwind) **не подключен** 
- `app.js` не подключен
- Tailwind не компилируется

---

### 2. **Что должно быть:**

```blade
@vite(['resources/css/app.css', 'resources/css/styles.css', 'resources/js/app.js'])
```

**Или разделить по файлам:**

HEAD:
```blade
@vite(['resources/css/app.css'])
```

BODY перед закрытием:
```blade
@vite(['resources/js/app.js'])
```

---

### 3. **Bootstrap CDN есть, но нужно проверить загрузку**

```blade
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
```

✅ Это работает (по HTTPS).

---

## 🛠️ Проверка на localhost

### Шаг 1: Остановить старый процесс Vite
```bash
# Найти процесс
lsof -i :5173

# Убить процесс
kill -9 <PID>
```

### Шаг 2: Запустить Vite в dev режиме
```bash
npm run dev
```

**Должно появиться:**
```
VITE v7.x.x  ready in xxx ms

➜  Local:   http://localhost:5173/
```

### Шаг 3: Проверить в браузере (на localhost:8000 или dev.local)

Открыть DevTools → Console:
```javascript
// Там не должно быть ошибок типа:
// GET http://localhost:5173/@vite/client 404 (Not Found)
```

Открыть DevTools → Sources:
```
Должны быть файлы:
- app.css (скомпилированный Tailwind)
- styles.css
- app.js
```

---

## ✅ Как исправить на Production

### Билд для продакшена:
```bash
npm run build
```

**Создаст в `public/build/`:**
```
build/
  ├── app-ABC123.css
  ├── styles-DEF456.css
  ├── app-GHI789.js
  └── manifest.json
```

Laravel автоматически подхватит эти файлы через `@vite()`.

---

## 🚨 Возможные причины на localhost

### 1. **Vite не запущен**
- Процесс killed/crashed
- HMR host неправильно настроен

### 2. **Firewall блокирует порт 5173**
- На некоторых сетях порт закрыт
- Решение: изменить в `vite.config.js`

### 3. **Кэш браузера**
- Ctrl+Shift+R (hard refresh)
- Очистить LocalStorage

### 4. **Node modules не установлены**
```bash
npm install
```

---

## 📝 Правильная конфигурация HMR для разных сценариев

### На localhost с Docker:
```javascript
// vite.config.js
export default defineConfig({
    // ...
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
            port: 5173,
            protocol: 'ws',  // ws для локальной сети
        },
    },
});
```

### На production (avrb1749.ru):
```javascript
hmr: {
    host: 'avrb1749.ru',
    port: 443,
    protocol: 'wss',  // wss для HTTPS
},
```

### На любом домене (автоматическое определение):
```javascript
hmr: {
    host: window.location.hostname,
    port: window.location.protocol === 'https:' ? 443 : 80,
}
```

---

## 🔧 Быстрое решение (для тестирования на localhost)

### Вариант 1: Использовать только Bootstrap
Если Tailwind не нужен, просто удалить:
```blade
<!-- Удалить -->
@vite(['resources/css/styles.css'])

<!-- Оставить только Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
```

### Вариант 2: Собрать Tailwind локально
```bash
# Установить Tailwind CSS CLI
npm install -D tailwindcss

# Построить CSS
npx tailwindcss -i ./resources/css/app.css -o ./public/css/app.css

# Подключить статический файл
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
```

### Вариант 3: Использовать Tailwind CDN (для разработки)
```blade
<script src="https://cdn.tailwindcss.com"></script>
```

⚠️ **Внимание:** Только для разработки, не для продакшена!

