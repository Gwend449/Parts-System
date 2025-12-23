```bash
# Перестроить образы
docker-compose build --no-cache

# Запустить контейнеры
docker-compose up -d

# Проверить что все работает
docker-compose ps
```

## 📝 Полезные команды

```bash
# Просмотр логов
docker-compose logs -f app      # логи приложения
docker-compose logs -f nginx    # логи вебсервера
docker-compose logs -f db       # логи базы данных


# Работа с БД
docker-compose exec db mysql -u forge -p forge

# Перезагрузка
docker-compose restart
docker-compose down && docker-compose up -d

# Очистка
docker-compose down -v  # удалит данные!
```

## 🔐 Дополнительно (для production)

### 1. Измените пароли БД в .env

```bash
nano public/.env
# Измените:
# DB_PASSWORD=121 → DB_PASSWORD=<strong-password>
# MYSQL_PASSWORD=121 → <same-password>
```

### 2. Включите SSL (Let's Encrypt)

```bash
# Получить сертификат
sudo certbot certonly --standalone -d your-domain.com

# Скопировать в проект
mkdir -p phpdocker/nginx/ssl
sudo cp /etc/letsencrypt/live/your-domain.com/fullchain.pem phpdocker/nginx/ssl/cert.pem
sudo cp /etc/letsencrypt/live/your-domain.com/privkey.pem phpdocker/nginx/ssl/key.pem
sudo chown $(whoami):$(whoami) phpdocker/nginx/ssl/*

# Раскомментировать HTTPS в nginx.conf и перезагрузить
docker-compose restart nginx
```

