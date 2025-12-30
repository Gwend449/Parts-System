FROM php:8.3-fpm

# -------------------------------
# Системные зависимости
# -------------------------------
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev libzip-dev libjpeg62-turbo-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# -------------------------------
# Composer
# -------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -------------------------------
# Рабочая директория
# -------------------------------
WORKDIR /application

# -------------------------------
# Копируем весь проект в контейнер
# -------------------------------
COPY . .

# -------------------------------
# Создаём директории storage и bootstrap/cache ДО composer install
# -------------------------------
RUN mkdir -p storage/logs \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# -------------------------------
# Composer install
# -------------------------------
RUN composer clear-cache \
    && composer install --no-dev --optimize-autoloader --no-interaction

# Copy entrypoint script
COPY phpdocker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# -------------------------------
# Порт и команда запуска
# -------------------------------
EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
