FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install required PHP extensions for MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Install additional useful extensions
RUN docker-php-ext-install opcache

# Copy Apache configuration for proper rewrite handling
COPY ./docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port 80
EXPOSE 80
