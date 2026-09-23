# ========================================================
# Dockerfile for PLP Faculty Profiling System
# Optimized for Render Web Services / Cloud Hosting
# ========================================================

FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions needed for MySQL, DomPDF, and PHPMailer
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        mysqli \
        pdo_mysql \
        gd \
        zip \
        intl

# Enable Apache modules
RUN a2enmod rewrite headers

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Create required upload directories if they don't exist
RUN mkdir -p /var/www/html/uploads/credentials \
    /var/www/html/uploads/teaching_loads \
    /var/www/html/auth_2/uploads/credentials \
    /var/www/html/auth_2/uploads/teaching_loads

# Set file permissions for Apache user
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/uploads \
    && chmod -R 775 /var/www/html/auth_2/uploads

# Install PHP dependencies via Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configure Apache port mapping for Render (Render dynamically sets $PORT)
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

ENV PORT=80

EXPOSE 80

CMD ["apache2-foreground"]

