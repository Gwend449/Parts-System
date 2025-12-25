# 🚀 Production Deployment Guide

## Prerequisites
- Docker and Docker Compose installed on production server
- Domain name configured and pointing to your server
- SSL certificate (Let's Encrypt recommended)

---

## Step 1: Clone and Setup

```bash
# Clone your repository
git clone <your-repo-url> /var/www/parts-system
cd /var/www/parts-system

# Checkout production branch
git checkout main  # or your production branch

# Copy production env file
cp .env.production .env
```

---

## Step 2: Update Production Environment Variables

Edit `.env` file with your production settings:

```bash
nano .env
```

**Critical changes needed:**

```env
# Update domain
APP_URL=https://your-domain.com

# Update database credentials (CHANGE THESE!)
DB_PASSWORD=your_secure_db_password_here
MYSQL_PASSWORD=your_secure_db_password_here
MYSQL_ROOT_PASSWORD=your_secure_root_password_here

# Update mail settings (optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password

# Optional: Update other services
PUSHER_APP_KEY=your_pusher_key
PUSHER_APP_SECRET=your_pusher_secret
```

---

## Step 3: Build and Start Containers

```bash
# Build images
docker-compose -f docker-compose.prod.yml build --no-cache

# Start containers in background
docker-compose -f docker-compose.prod.yml up -d

# Verify containers are running
docker-compose -f docker-compose.prod.yml ps
```

---

## Step 4: Optimize Laravel Application

```bash
# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Cache configuration (MUST DO in production)
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache

# Cache routes (improves performance)
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache

# Cache views (improves performance)
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache

# Optimize autoloader
docker-compose -f docker-compose.prod.yml exec app composer install --optimize-autoloader --no-dev

# Optimize Laravel
docker-compose -f docker-compose.prod.yml exec app php artisan optimize
```

---

## Step 5: Setup SSL with Let's Encrypt

```bash
# Install certbot
sudo apt update && sudo apt install certbot python3-certbot-nginx

# Get certificate
sudo certbot certonly --standalone -d your-domain.com

# Copy certificates to project
mkdir -p phpdocker/nginx/ssl
sudo cp /etc/letsencrypt/live/your-domain.com/fullchain.pem phpdocker/nginx/ssl/cert.pem
sudo cp /etc/letsencrypt/live/your-domain.com/privkey.pem phpdocker/nginx/ssl/key.pem
sudo chown $(whoami):$(whoami) phpdocker/nginx/ssl/*

# Restart nginx
docker-compose -f docker-compose.prod.yml restart nginx
```

---

## Step 6: Configure nginx for HTTPS

Update `phpdocker/nginx/nginx.conf` to include SSL:

```nginx
server {
    listen 80;
    server_name _;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name _;

    ssl_certificate /etc/nginx/ssl/cert.pem;
    ssl_certificate_key /etc/nginx/ssl/key.pem;

    root /application/public;
    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass php-fpm:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    location ~ /\.(?!well-known).* {
        deny all;
        return 404;
    }

    access_log /var/log/nginx/access.log combined;
    error_log /var/log/nginx/error.log warn;
}
```

---

## Step 7: Setup Auto-renewal for SSL

```bash
# Test renewal
sudo certbot renew --dry-run

# Create renewal script
sudo tee /usr/local/bin/renew-certs.sh << 'EOF'
#!/bin/bash
cd /var/www/parts-system
certbot renew --quiet
cp /etc/letsencrypt/live/your-domain.com/fullchain.pem phpdocker/nginx/ssl/cert.pem
cp /etc/letsencrypt/live/your-domain.com/privkey.pem phpdocker/nginx/ssl/key.pem
docker-compose -f docker-compose.prod.yml restart nginx
EOF

sudo chmod +x /usr/local/bin/renew-certs.sh

# Add to crontab (runs daily at 3 AM)
(crontab -l 2>/dev/null; echo "0 3 * * * /usr/local/bin/renew-certs.sh") | crontab -
```

---

## Step 8: Verify Deployment

```bash
# Check all containers
docker-compose -f docker-compose.prod.yml ps

# Check logs
docker-compose -f docker-compose.prod.yml logs -f nginx
docker-compose -f docker-compose.prod.yml logs -f app

# Test application
curl https://your-domain.com
```

---

## 📊 Useful Production Commands

```bash
# View logs
docker-compose -f docker-compose.prod.yml logs -f app     # App logs
docker-compose -f docker-compose.prod.yml logs -f nginx   # Nginx logs
docker-compose -f docker-compose.prod.yml logs -f db      # Database logs

# Database backup
docker-compose -f docker-compose.prod.yml exec db mysqldump -u forge -p forge forge > backup_$(date +%Y%m%d_%H%M%S).sql

# Database restore
docker-compose -f docker-compose.prod.yml exec -T db mysql -u forge -p forge forge < backup.sql

# Restart services
docker-compose -f docker-compose.prod.yml restart app
docker-compose -f docker-compose.prod.yml restart nginx
docker-compose -f docker-compose.prod.yml restart queue

# Update and restart
git pull origin main
docker-compose -f docker-compose.prod.yml build --no-cache
docker-compose -f docker-compose.prod.yml up -d
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker-compose -f docker-compose.prod.yml restart nginx
```

---

## 🔒 Security Best Practices

✅ **Always:**
- Use strong passwords for database and root
- Keep `.env` file private (not in git)
- Use HTTPS only
- Enable firewall
- Keep Docker images updated
- Set up regular backups
- Monitor logs for errors

❌ **Never:**
- Commit `.env` to git
- Use default passwords
- Enable SSH to containers
- Expose ports unnecessarily

---

## 🚨 Troubleshooting

### 500 Errors
```bash
docker-compose -f docker-compose.prod.yml logs app | tail -50
```

### Database Connection Issues
```bash
docker-compose -f docker-compose.prod.yml exec app php artisan tinker
>>> DB::connection()->getPdo();
```

### Permission Issues
```bash
docker-compose -f docker-compose.prod.yml exec app chown -R www-data:www-data storage bootstrap/cache
```

---

## 📝 Notes

- Redis is used for caching and queue management
- The app service includes health checks
- Logging is configured with rotation (max 10MB, 3 files)
- Database backups should be scheduled separately
- Monitor disk space regularly

---

**Deployment complete! Your application is now running in production.** 🎉
