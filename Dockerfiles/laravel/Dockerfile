FROM php:8.5-fpm-alpine

ARG user
ARG uid

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    postgresql-dev \
    zip \
    nodejs \
    npm \
    unzip \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    autoconf \
    make \
    g++ \
    linux-headers

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype=/usr/include --with-jpeg=/usr/include && \
    docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    sockets

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN adduser -D -g www-data -u $uid $user && \
    mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Install Redis extension
RUN pecl install redis && \
    docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www

USER $user
