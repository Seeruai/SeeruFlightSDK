FROM php:8.2-cli-alpine

# Install required PHP extensions and composer
RUN apk add --no-cache \
    git \
    unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /app

# Copy the entire application
COPY . .

# Install dependencies and optimize autoloader
RUN composer install --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app 