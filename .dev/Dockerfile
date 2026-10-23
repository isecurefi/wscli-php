FROM php:8.3.36-cli@sha256:74caa458ad8d6c5e401eb9f8841a62faeab9b7be42b54cd37986d7685f857618

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git unzip libzip-dev libgmp-dev libicu-dev libonig-dev; \
    docker-php-ext-install -j$(nproc) zip gmp intl bcmath; \
    pecl install gnupg && docker-php-ext-enable gnupg || true; \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.9 /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1 \
    COMPOSER_MEMORY_LIMIT=-1

WORKDIR /app
