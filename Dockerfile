FROM php:8.2-apache

# Install required PHP extensions for MySQL access
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache rewrite module and allow .htaccess overrides
RUN a2enmod rewrite \
    && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
