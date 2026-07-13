FROM php:8.5-fpm

RUN apt-get update && apt-get install -y \
    autoconf \
    automake \
    build-essential \
    curl \
    wget \
    git \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libonig-dev \
    libwebp-dev \
    libicu-dev \
    libzip-dev \
    zlib1g-dev \
    libpq-dev \
    libssl-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libmagickwand-dev \
    libmagickcore-dev \
    libmemcached-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j$(nproc) \
    bcmath \
    exif \
    intl \
    iconv \
    mbstring \
    mysqli \
    pdo_mysql \
    pgsql \
    pdo_pgsql \
    soap \
    pcntl \
    zip

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    && docker-php-ext-install -j$(nproc) gd

RUN set -ex; \
    pecl install xdebug && docker-php-ext-enable xdebug; \
    pecl install redis && docker-php-ext-enable redis; \
    pecl install memcached && docker-php-ext-enable memcached; \
    pecl install imagick && docker-php-ext-enable imagick

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV PATH="$PATH:/usr/local/bin"

USER 1000:1000

WORKDIR /var/www/adtechtracker.local

CMD ["php-fpm"]