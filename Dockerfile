FROM php:8.2-fpm

# Arguments for the user and group UID/GID
ARG GROUP_ID=1000
ARG USER_ID=1000
ENV USER_NAME=www-data
ARG GROUP_NAME=www-data

# Set the working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    libonig-dev \
    nano \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create group if it doesn't already exist, and create the user
RUN getent group $GROUP_NAME || groupadd -g $GROUP_ID $GROUP_NAME \
    && getent passwd $USER_NAME || useradd -u $USER_ID -g $GROUP_ID -m -s /bin/bash $USER_NAME

# Create Composer cache directory with correct permissions
RUN mkdir -p /tmp/composer/cache \
    && chown -R www-data:www-data /tmp/composer/cache

# Copy application files
COPY . /var/www/html

# Fix user and group IDs for existing files (to avoid permission issues)
RUN usermod -u ${USER_ID} ${USER_NAME} \
    && groupmod -g ${GROUP_ID} ${GROUP_NAME} \
    && chown -R ${USER_NAME}:${GROUP_NAME} /var/www/html /var/www /var/log/ \
    && chown -R ${USER_NAME}:${GROUP_NAME} /var/www/html/laravel/storage /var/www/html/laravel/bootstrap/cache \
    && chmod -R 775 /var/www/html/laravel/storage /var/www/html/laravel/bootstrap/cache \
    && chmod -R 775 /var/www/html/laravel \
    && find /var/www/html/laravel -type d -exec chmod 775 {} \; \
    && find /var/www/html/laravel -type f -exec chmod 664 {} \;

# Expose the PHP-FPM port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
