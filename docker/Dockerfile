# Mirrors the CI environment (.github/workflows): PHP 8.1 floor, extensions
# "dom fileinfo filter libxml xmlreader zip gd". Override PHP_VERSION (8.1-8.5)
# to reproduce the test matrix.
ARG PHP_VERSION=8.1

FROM php:${PHP_VERSION}-cli-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Writable HOME/cache so the container can run as an arbitrary host UID:GID.
ENV COMPOSER_HOME=/tmp/composer \
    COMPOSER_CACHE_DIR=/tmp/composer/cache \
    HOME=/tmp

# Pre-create vendor world-writable so the named volume (created root-owned by
# Docker) inherits writable permissions for the non-root runtime UID.
RUN mkdir -p /app/vendor && chmod 0777 /app/vendor

WORKDIR /app
