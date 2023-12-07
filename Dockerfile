FROM php:8.2-apache

# Install additional PHP extensions
WORKDIR /var/www/html/

RUN chmod -R 777 /var/www

RUN pecl install xdebug \
    && apt update \
    && apt install libzip-dev -y \
    && docker-php-ext-enable xdebug \
    && a2enmod rewrite \
    && docker-php-ext-install zip mysqli pdo pdo_mysql

# Set Apache document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN echo "upload_max_filesize = 1G" > $PHP_INI_DIR/conf.d/php.ini
RUN echo "post_max_size = 1G" >> $PHP_INI_DIR/conf.d/php.ini

# Augmenter la limite de taille des requêtes pour Apache
RUN echo "LimitRequestBody 1073741824" >> /etc/apache2/apache2.conf

RUN service apache2 restart

