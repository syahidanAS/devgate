# ==========================================
# STAGE 1: Build Frontend Assets
# ==========================================
FROM node:22-alpine AS asset-builder
WORKDIR /app

COPY package.json package-lock.json* ./

RUN npm ci || npm install

COPY vite.config.js* tailwind.config.js* postcss.config.js* jsconfig.json* tsconfig.json* ./
COPY resources/ resources/
COPY public/ public/

RUN npm run build

# ==========================================
# STAGE 2: PHP Application Runner
# ==========================================
FROM php:8.2-fpm-alpine AS app-runner

WORKDIR /var/www/devgate

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

# Install PHP extensions + Redis
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

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP production configs
RUN echo "expose_php = Off" > /usr/local/etc/php/conf.d/security.ini \
    && echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memory.ini \
    && echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini

# OPCache config
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

# Install composer deps
COPY composer.json composer.lock* ./

RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy project files
COPY . .

# Copy built assets
COPY --from=asset-builder /app/public/build ./public/build

# Optimize autoload
RUN composer dump-autoload --optimize --no-dev

# Permissions
RUN chown -R www-data:www-data /var/www/devgate/storage /var/www/devgate/bootstrap/cache \
    && chmod -R 775 /var/www/devgate/storage /var/www/devgate/bootstrap/cache

USER www-data

EXPOSE 9000

CMD ["php-fpm"]