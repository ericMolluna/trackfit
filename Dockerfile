FROM php:8.3-apache

# Document root apunta a /public de Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf \
    && a2enmod rewrite headers

# Extensión zip para Composer + bcmath para Laravel
RUN apt-get update && apt-get install -y libzip-dev unzip \
    && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install bcmath zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copiamos el proyecto (ver .dockerignore)
COPY . .

# Dependencias de producción
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader

# Permisos para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache database

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
