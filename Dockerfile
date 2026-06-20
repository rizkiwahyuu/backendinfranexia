FROM php:8.3-cli

# Install system packages and PHP extensions needed by this Laravel backend.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libicu-dev \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    && docker-php-ext-install \
    pdo_pgsql \
    intl \
    mbstring \
    zip \
    opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer from the official Composer image.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node.js for Vite asset builds.
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN npm install
RUN npm run build

RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
    