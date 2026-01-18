# 🔧 ДИАГНОСТИКА: Почему не работает callback

## 🎯 СИМПТОМЫ

Ты видишь в логах:
```
[2026-01-18 11:05:31] production.INFO: AmoCRM OAuth: запрос на авторизацию
[2026-01-18 11:05:31] production.DEBUG: AmoCRM OAuth: state сгенерирован
[2026-01-18 11:05:31] production.INFO: AmoCRM OAuth: перенаправление на авторизацию
```

Но **ПОТОМ НИЧЕГО!** ❌

Нет логов:
- `AmoCRM CALLBACK DEBUG`
- `AmoCRM OAuth: callback получен`
- `AmoCRM успешно авторизована`

---

## 🔍 ЧТО ЭТО ЗНАЧИТ

```
Шаг 1: /amocrm/install ✅ РАБОТАЕТ
       └─ Контроллер находится
       └─ State сохраняется в БД
       └─ Редирект на AmoCRM работает

Шаг 2: Авторизация в AmoCRM ✅ (твоя часть)
       └─ Вводишь пароль
       └─ Разрешаешь доступ

Шаг 3: /amocrm/callback ❌ НИКОГДА НЕ ВЫЗЫВАЕТСЯ!
       └─ AmoCRM отправляет callback на неправильный URL
       └─ ИЛИ контроллер не находится
       └─ ИЛИ происходит какая-то ошибка до логирования
```

---

## 🚨 ДИАГНОСТИКА

### Проблема #1: Redirect URI в AmoCRM неправильный (90% случаев)

**СИМПТОМЫ:**
- Callback никогда не приходит
- Пользователь остается на странице AmoCRM
- В логах ничего не появляется

**ПРОВЕРКА:**
```
AmoCRM Dashboard → Приложение → Редактировать → Redirect URI

ДОЛЖНО БЫТЬ: https://avrb1749.ru/amocrm/callback
МОЖЕТ БЫТЬ ОШИБОЧНО:
❌ https://avrb1749.ru/amocrm/install (install вместо callback)
❌ http://avrb1749.ru/amocrm/callback (http вместо https)
❌ https://avrb1749.ru/amocrm/callback/ (слэш в конце)
❌ https://avrb1749.ru/api/callback (неправильный путь)
```

**РЕШЕНИЕ:**
1. Открыть AmoCRM
2. Приложение → Редактировать
3. Сменить Redirect URI на: `https://avrb1749.ru/amocrm/callback`
4. Сохранить
5. Повторить авторизацию

---

### Проблема #2: .env не совпадает с AmoCRM

**СИМПТОМЫ:**
- Callback приходит, но с неправильными параметрами
- Ошибка: "неверный или устаревший state параметр"

**ПРОВЕРКА:**
```bash
# На сервере
cat .env | grep AMOCRM_REDIRECT_URI
# Должно быть: AMOCRM_REDIRECT_URI=https://avrb1749.ru/amocrm/callback

# Проверить что конфиг прочитал
php artisan tinker
> config('amocrm.redirect_uri')
# Должно быть: "https://avrb1749.ru/amocrm/callback"
```

**РЕШЕНИЕ:**
```bash
# 1. Отредактировать .env
nano .env
# Найти AMOCRM_REDIRECT_URI и сменить на: https://avrb1749.ru/amocrm/callback

# 2. Перезагрузить конфиг
php artisan config:cache
```

---

### Проблема #3: Миграции не применены

**СИМПТОМЫ:**
- Ошибка: "SQLSTATE[42S01]: Table 'amocrm_oauth_states' doesn't exist"
- Контроллер падает при попытке сохранить state

**ПРОВЕРКА:**
```bash
# Посмотреть есть ли таблица
docker exec app php artisan tinker
> DB::table('amocrm_oauth_states')->count()

# Если ошибка "table doesn't exist" - миграции не применены
```

**РЕШЕНИЕ:**
```bash
docker exec app php artisan migrate --force
```

---

### Проблема #4: Route не кэширован или удален

**СИМПТОМЫ:**
- Ошибка 404 при попытке открыть `/amocrm/callback`
- Route не находится

**ПРОВЕРКА:**
```bash
docker exec app php artisan route:list | grep amocrm

# Должны быть:
GET    /amocrm/install
GET    /amocrm/callback
ANY    /amocrm/callback-debug
```

**РЕШЕНИЕ:**
```bash
docker exec app php artisan route:clear
docker exec app php artisan route:cache
```

---

### Проблема #5: Middleware блокирует callback

**СИМПТОМЫ:**
- Callback приходит от AmoCRM (GET запрос)
- Но middleware требует CSRF token или авторизацию

**ПРОВЕРКА:**
```
Route group в web.php может иметь:
- middleware(['web']) - требует CSRF
- middleware(['auth']) - требует авторизацию
- middleware(['throttle:60,1']) - может быть rate limit
```

**РЕШЕНИЕ:**
- Callback route должна быть **без защиты**
- Или добавить исключение для `/amocrm/callback`

---

## 🧪 ТЕСТ 1: Проверить что route существует

```bash
curl -v https://avrb1749.ru/amocrm/callback?code=test&state=test 2>&1 | head -20
```

**ПРАВИЛЬНО:**
```
< HTTP/2 302
< location: https://avrb1749.ru/
```
(Редирект на главную с ошибкой - это нормально для тестового запроса)

**НЕПРАВИЛЬНО:**
```
< HTTP/2 404
< Content-Type: text/html
```
(404 значит route не существует)

---

## 🧪 ТЕСТ 2: Проверить что миграции применены

```bash
docker exec app php artisan tinker << 'EOF'
DB::table('amocrm_oauth_states')->count()
exit
EOF
```

**ПРАВИЛЬНО:**
```
= 0 или 1 или любое число
```

**НЕПРАВИЛЬНО:**
```
Exception: Table 'amocrm_oauth_states' doesn't exist
```

---

## 🧪 ТЕСТ 3: Проверить конфиг

```bash
docker exec app php artisan tinker << 'EOF'
config('amocrm')
exit
EOF
```

**ПРАВИЛЬНО:**
```
{
  "client_id" => "f1457407-e1a5-4694-b976-3bc088294be4",
  "client_secret" => "AVlSbzrtacTHv62djQC8AubIqVa4bXKiOafaXlEcRfx7YeNAPgRatdJgANeYID38",
  "redirect_uri" => "https://avrb1749.ru/amocrm/callback",
  "subdomain" => "fastis02",
}
```

**НЕПРАВИЛЬНО:**
```
{
  "client_id" => null,
  "redirect_uri" => null,
  ...
}
```
(null значит переменные не прочитались из .env)

---

## 📊 ТАБЛИЦА ДИАГНОСТИКИ

| Проблема | Симптом | Проверка | Решение |
|----------|---------|----------|---------|
| Redirect URI неправильный | Callback не приходит | Посмотри в AmoCRM Dashboard | Сменить на `https://avrb1749.ru/amocrm/callback` |
| .env не совпадает | Ошибка state | `config('amocrm.redirect_uri')` | Обновить .env и перезагрузить |
| Миграции не применены | Table doesn't exist | `php artisan migrate --force` | Запустить миграции |
| Route удален | 404 ошибка | `php artisan route:list` | `php artisan route:clear` |
| Middleware блокирует | 403/500 ошибка | Посмотреть routes/web.php | Убрать middleware с /amocrm/callback |

---

## ✅ ПРАВИЛЬНЫЙ ШАГ ЗА ШАГОМ

### 1. Убедись что в AmoCRM правильный Redirect URI

```
Лог → AmoCRM (https://fastis02.amocrm.ru)
Интеграции → Разработчикам → Мои приложения → [Приложение]
Redirect URI: https://avrb1749.ru/amocrm/callback ✅
```

### 2. Убедись что .env правильный

```bash
ssh user@avrb1749.ru
grep AMOCRM .env | grep REDIRECT
# AMOCRM_REDIRECT_URI=https://avrb1749.ru/amocrm/callback ✅
```

### 3. Перезагрузи конфиг

```bash
docker exec app php artisan config:cache
docker exec app php artisan route:clear
docker exec app php artisan cache:clear
```

### 4. Запусти миграции

```bash
docker exec app php artisan migrate --force
```

### 5. Проверь что все работает

```bash
curl https://avrb1749.ru/amocrm/callback?code=test&state=test
# Должен вернуть редирект (302)

docker exec app tail -f storage/logs/laravel.log | grep -i amocrm
# Смотри логи в реальном времени
```

### 6. Повтори авторизацию

1. Открыть https://avrb1749.ru/amocrm/install
2. Авторизоваться в AmoCRM
3. Посмотреть логи
4. Должны быть `AmoCRM OAuth: callback получен`

---

## 🎯 ЕСЛИ ПОСЛЕ ВСЕГО НЕ РАБОТАЕТ

Собери диагностику:

```bash
echo "=== REDIRECT URI ===" && \
grep AMOCRM .env && \
echo "" && \
echo "=== ROUTE LIST ===" && \
php artisan route:list | grep amocrm && \
echo "" && \
echo "=== CONFIG ===" && \
php artisan tinker << 'EOF'
config('amocrm')
exit
EOF
```

И отправь вывод!

---

**🔴 ГЛАВНОЕ: 90% случаев - это Redirect URI в AmoCRM неправильный!**
