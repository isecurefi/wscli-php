# Minimal image for running wscli inside a container.
#
#   docker build -t wscli:latest .
#   docker run -v "$HOME/.wscli:/root/.wscli" wscli session:login --json
#
# This image builds the phar from source and ships only the runtime
# dependencies (PHP 8.3.36 + ext-curl + ext-json + ext-openssl + ext-zlib).

# ---- build stage ----------------------------------------------------------
FROM php:8.3.36-cli@sha256:74caa458ad8d6c5e401eb9f8841a62faeab9b7be42b54cd37986d7685f857618 AS build

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev && \
    docker-php-ext-install zip && \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_NO_INTERACTION=1

WORKDIR /src
COPY tools/box /src/tools/box
RUN cd tools/box && composer install --no-progress

COPY wscli-php-sdk /src/wscli-php-sdk
COPY wscli-php     /src/wscli-php

RUN cd wscli-php-sdk && composer install --no-progress
RUN cd wscli-php && composer install --no-progress && \
    ../tools/box/vendor/bin/box compile -v

# ---- runtime stage --------------------------------------------------------
FROM php:8.3.36-cli@sha256:74caa458ad8d6c5e401eb9f8841a62faeab9b7be42b54cd37986d7685f857618

RUN apt-get update && apt-get install -y --no-install-recommends ca-certificates && \
    rm -rf /var/lib/apt/lists/*

COPY --from=build /src/wscli-php/wscli.phar /usr/local/bin/wscli
RUN chmod +x /usr/local/bin/wscli

ENTRYPOINT ["/usr/local/bin/wscli"]
CMD ["list"]
