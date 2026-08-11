FROM php:8.2-fpm

# Установка расширения Redis Cluster
RUN pecl install redis \
    && docker-php-ext-enable redis

# Установка других расширений
RUN docker-php-ext-install opcache session

# Копирование php.ini
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Устанавливаем Redis Cluster клиент
RUN apt-get update && apt-get install -y \
    git \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /data/mysite.local

EXPOSE 9000

CMD ["php-fpm"]