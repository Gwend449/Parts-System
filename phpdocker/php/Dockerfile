FROM php:8.3-fpm

# Системные зависимости
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /application

# Копируем весь Laravel проект (находится в папке public)
COPY public/ ./

# Создаем .env из production (если .env.production существует)
RUN if [ -f .env.production ]; then cp .env.production .env; fi

# Очищаем composer cache перед установкой
RUN composer clear-cache

# Устанавливаем зависимости через composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Создаем нужные директории если их нет
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

# Права на storage и cache для www-data
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 755 storage bootstrap/cache \
    && chmod -R 775 storage/logs storage/framework

EXPOSE 9000
CMD ["php-fpm"]
