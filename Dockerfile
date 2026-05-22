# ==========================================
# STAGE 1: Build Frontend Assets
# ==========================================
FROM node:20-alpine AS asset-builder
WORKDIR /app

# Copy dependency configs and lock files
COPY package.json package-lock.json* ./

# Install npm packages
RUN npm ci || npm install

# Copy configuration files and resources
COPY vite.config.js* tailwind.config.js* postcss.config.js* jsconfig.json* tsconfig.json* ./
COPY resources/ resources/
COPY public/ public/

# Build assets with Vite
RUN npm run build

# ==========================================
# STAGE 2: PHP Application Runner
# ==========================================
FROM php:8.2-fpm-alpine AS app-runner
WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache \
    unzip \
    git \
    bash \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev \
    shadow

# Install and configure PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo_pgsql \
        pgsql \
        zip \
        opcache \
        bcmath \
        exif \
        pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Install Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy configuration files for OPcache and PHP production settings
RUN echo "expose_php = Off" > /usr/local/etc/php/conf.d/security.ini \
    && echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory.ini \
    && echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini

RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.enable_cli=1'; \
        echo 'opcache.memory_consumption=256'; \
        echo 'opcache.interned_strings_buffer=16'; \
        echo 'opcache.max_accelerated_files=20000'; \
        echo 'opcache.revalidate_freq=0'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache-prod.ini

# Copy Composer files
COPY composer.json composer.lock* ./

# Install composer dependencies (without dev packages and scripts)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy application source code
COPY . .

# Copy built frontend assets from Stage 1
COPY --from=asset-builder /app/public/build ./public/build

# Generate optimized autoload dump
RUN composer dump-autoload --optimize --no-dev

# Configure correct directory ownership and permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Change active user to www-data for security
USER www-data

EXPOSE 9000
CMD ["php-fpm"]
