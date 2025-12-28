# 🚀 AmoCRM Integration - Quick Start for VS Code

## 📦 Что нужно сделать ТЫ (не код!)

### 1. Получить учетные данные в AmoCRM (5 минут)

```
https://www.amocrm.ru
   ↓
Левое меню → Параметры → Интеграции
   ↓
Создать интеграцию → заполнить форму
   ↓
Скопировать: Client ID, Client Secret, Subdomain
```

**Форма интеграции:**
- Название: Parts System
- Перенаправления: `https://abc123.ngrok.io/amocrm/callback`

### 2. Настроить ngrok для localhost (2 минуты)

В **новом терминале** запусти:

```bash
ngrok http 8000
```

Скопируй выданный https URL (вроде `https://abc123.ngrok.io`)

### 3. Обновить .env (30 секунд)

```bash
# Если его еще нет, создать из example:
cp .env.example .env

# Добавить эти строки:
AMO_CLIENT_ID=abc123def456
AMO_CLIENT_SECRET=xyz789uvw012
AMO_REDIRECT_URI=https://abc123.ngrok.io/amocrm/callback
AMOCRM_SUBDOMAIN=example
```

### 4. Запустить приложение (2 минуты)

```bash
# Если используется artisan serve:
php artisan serve

# Или если Docker:
docker compose up -d
```

### 5. Авторизовать приложение (3 минуты)

Перейди на одну из этих ссылок:

**Локально с ngrok:**
```
https://abc123.ngrok.io/amocrm/install
```

**На production:**
```
https://yourdomain.com/amocrm/install
```

На странице:
1. Введи логин AmoCRM
2. Введи пароль
3. Нажми "Авторизовать"
4. Будешь перенаправлен обратно с сообщением об успехе

### 6. Проверить что работает (1 минута)

```bash
php artisan amo:status
```

Должно вывести:
```
✅ AmoCRM подключена!
Domain: example.amocrm.ru
...
```

---

## 📚 Где найти информацию

| Файл | Описание |
|---|---|
| `AMOCRM_README.md` | 👈 **ПРОЧИТАЙ ЭТОТ ПЕРВЫМ** - резюме |
| `AMOCRM_INTEGRATION.md` | Полный гайд со всеми деталями |
| `AMOCRM_CHECKLIST.md` | Пошаговый чек-лист |
| `AMOCRM_FAQ.md` | Ответы на вопросы |
| `AMOCRM_FLOW.md` | Диаграммы и архитектура |
| `app/Services/AmoExamples.php` | Примеры кода |

---

## 🎯 Что можешь делать после авторизации

```php
use App\Services\AmoService;
use AmoCRM\Models\LeadModel;

public function createLead(AmoService $amo)
{
    $lead = new LeadModel();
    $lead->setName('Новый заказ');
    
    $amo->api()->leads()->addOne($lead);
    
    return 'Готово!';
}
```

Примеры для всего остального - смотри в `app/Services/AmoExamples.php`

---

## ⚡ Шпаргалка команд

```bash
# Проверить статус
php artisan amo:status

# Посмотреть токен в БД
php artisan tinker
>>> \App\Models\AmocrmToken::first()

# Запустить миграции (если еще не запускал)
php artisan migrate

# Проверить routes
php artisan route:list | grep amocrm

# Посмотреть логи в реальном времени
tail -f storage/logs/laravel.log
```

---

## ⚠️ Частые ошибки

**Ошибка: "Invalid redirect_uri"**
→ Проверь что URL в `.env` совпадает с тем, что в интеграции AmoCRM

**Ошибка: "amoCRM не подключена"**
→ Запусти миграции: `php artisan migrate`

**Не видишь ngrok URL?**
→ Запусти: `ngrok http 8000` в новом терминале

**Localhost не работает?**
→ AmoCRM требует HTTPS, нужно использовать ngrok или production

---

## 📱 Использование в контроллере

```php
namespace App\Http\Controllers;

use App\Services\AmoService;

class OrderController extends Controller
{
    public function store(AmoService $amo)
    {
        try {
            // API готов к использованию
            $contacts = $amo->api()->contacts()->list();
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
```

---

**⏱️ Полная интеграция займет ~15-20 минут!** ⏱️

Начни с пункта 1️⃣ выше и следи по чек-листу 📋
