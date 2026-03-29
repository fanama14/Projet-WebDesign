FROM php:8.2-apache

# Install required PHP extensions for MySQL access
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache modules for rewrite (pour les URLs), deflate (pour Gzip) et expires (pour le cache
RUN a2enmod rewrite deflate expires headers \
    && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
