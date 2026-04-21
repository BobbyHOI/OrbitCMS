# Use the official PHP image with Apache.
FROM php:8.0-apache

# Install necessary PHP extensions.
RUN docker-php-ext-install pdo pdo_mysql opcache

# Configure PHP for production on Cloud Run.
RUN set -ex; \
  { \
    echo "; Cloud Run enforces memory & timeouts"; \
    echo "memory_limit = -1"; \
    echo "max_execution_time = 0"; \
    echo "; File upload at Cloud Run network limit"; \
    echo "upload_max_filesize = 32M"; \
    echo "post_max_size = 32M"; \
    echo "; Configure Opcache for Containers"; \
    echo "opcache.enable = On"; \
    echo "opcache.validate_timestamps = Off"; \
    echo "opcache.memory_consumption = 64"; \
  } > "$PHP_INI_DIR/conf.d/cloud-run.ini"

# Copy application code.
WORKDIR /var/www/html
COPY . ./

# Set ownership of the web root.
RUN chown -R www-data:www-data /var/www/html

# Enable Apache rewrite module for clean URLs.
RUN a2enmod rewrite

# Use the PORT environment variable in Apache configuration.
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Switch to the production php.ini for better security and performance.
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
