FROM php:8.3-cli-alpine

WORKDIR /var/www/html

# Install required system packages and PHP extensions
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    oniguruma-dev \
    mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring gd zip bcmath pcntl

# Copy Composer binary
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure OPcache for ultra-fast performance in CLI server
COPY docker/opcache.ini /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Copy and setup entrypoint script with clean UNIX line endings
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN tr -d '\r' < /usr/local/bin/entrypoint.sh > /usr/local/bin/entrypoint_unix.sh \
    && mv /usr/local/bin/entrypoint_unix.sh /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
