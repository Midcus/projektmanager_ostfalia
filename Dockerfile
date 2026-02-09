FROM php:8.3-apache


RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    unzip \
    cron \
    default-mysql-client \ 
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


WORKDIR /var/www/html


COPY . /var/www/html


RUN composer install --optimize-autoloader --no-dev


RUN composer require getbrevo/brevo-php

COPY .docker/apache.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite


RUN usermod -u 1000 www-data && groupmod -g 1000 www-data


COPY .docker/crontab /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron \
    && crontab /etc/cron.d/laravel-cron \
    && touch /var/log/cron.log


COPY .docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

RUN echo "upload_max_filesize = 12M\npost_max_size = 16M" > /usr/local/etc/php/php.ini



ENTRYPOINT ["/entrypoint.sh"]