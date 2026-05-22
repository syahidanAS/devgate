# ==========================================
# STAGE 1: Build Frontend Assets
# ==========================================
FROM node:22-alpine AS asset-builder

WORKDIR /app

# ------------------------------------------------
# Copy package files
# ------------------------------------------------
COPY package.json package-lock.json* ./

# ------------------------------------------------
# Install Node dependencies
# ------------------------------------------------
RUN npm ci || npm install

# ------------------------------------------------
# Copy frontend resources
# ------------------------------------------------
COPY vite.config.js* tailwind.config.js* postcss.config.js* jsconfig.json* tsconfig.json* ./
COPY resources ./resources
COPY public ./public

# ------------------------------------------------
# Build Vite assets
# ------------------------------------------------
RUN npm run build

# ==========================================
# STAGE 2: PHP Application Runner
# ==========================================
FROM php:8.2-fpm-alpine AS app-runner

WORKDIR /var/www/devgate

# ------------------------------------------------
# Install system dependencies
# ------------------------------------------------
RUN apk add --no-cache \
    unzip \
    git \
    bash \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    postgresql-dev \
    oniguruma-dev \
    shadow

# ------------------------------------------------
# Install PHP extensions
# ------------------------------------------------
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo_pgsql \
    pgsql \
    zip \
    bcmath \
    exif \
    pcntl \
    opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# ------------------------------------------------
# Install Composer
# ------------------------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ------------------------------------------------
# PHP Production Configuration
# ------------------------------------------------
RUN echo "expose_php = Off" > /usr/local/etc/php/conf.d/security.ini \
    && echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory.ini \
    && echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" > /usr/local/etc/php/conf.d/execution.ini

# ------------------------------------------------
# OPCache Configuration
# ------------------------------------------------
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=1'; \
    echo 'opcache.memory_consumption=256'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# ------------------------------------------------
# Copy Full Application
# ------------------------------------------------
COPY . .

# ------------------------------------------------
# Install PHP Dependencies
# ------------------------------------------------
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader

# ------------------------------------------------
# Copy Built Frontend Assets
# ------------------------------------------------
COPY --from=asset-builder /app/public/build ./public/build

# ------------------------------------------------
# Create Laravel Directories
# ------------------------------------------------
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# ------------------------------------------------
# Set Permissions
# ------------------------------------------------
RUN chown -R www-data:www-data /var/www/devgate \
    && chmod -R 775 storage bootstrap/cache

# ------------------------------------------------
# Switch User
# ------------------------------------------------
USER www-data

EXPOSE 9000

CMD ["php-fpm"]