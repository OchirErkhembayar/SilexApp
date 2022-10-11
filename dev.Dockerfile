FROM php:8.1-fpm

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions pdo_mysql zip

WORKDIR /var/www/project

# Composer
COPY --from=docker.io/composer:2.3.4 /usr/bin/composer /usr/bin/