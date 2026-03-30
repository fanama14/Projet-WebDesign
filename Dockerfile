FROM php:8.2-apache

# Install GD dependencies for image processing (jpeg/png/webp)
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Build PHP extensions: GD + MySQL access
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_mysql mysqli

# Enable Apache modules for rewrite (pour les URLs), deflate (pour Gzip) et expires (pour le cache
RUN a2enmod rewrite deflate expires headers \
    && sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
