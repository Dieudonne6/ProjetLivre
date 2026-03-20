# FROM php:8.2-fpm

# RUN apt-get update && apt-get install -y \
#     git \
#     unzip \
#     libzip-dev \
#     && docker-php-ext-install pdo pdo_mysql

# COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# WORKDIR /var/www

# COPY . .

# RUN composer install

# CMD ["php-fpm"]


FROM php:8.2-fpm

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql

# Installer composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /var/www

# Copier le projet
COPY . .

# Installer dépendances Laravel
RUN composer install --no-interaction --optimize-autoloader

RUN chmod -R 775 storage bootstrap/cache

# Port utilisé par Render
EXPOSE 10000

# Lancer serveur Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000
