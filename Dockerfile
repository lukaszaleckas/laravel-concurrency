FROM composer:2.2 as builder

COPY composer.json /app/

RUN composer install \
  --ignore-platform-reqs \
  --no-ansi \
  --no-autoloader \
  --no-interaction \
  --no-scripts

COPY . /app/

RUN composer dump-autoload --optimize --classmap-authoritative

FROM php:8.1-cli as base

RUN  apt-get update \
    && apt-get install -y --no-install-recommends build-essential git python \
    && pecl install pcov \
    && docker-php-ext-enable pcov \
    && docker-php-ext-install pdo_mysql

# Cleanup
RUN rm -rf /var/lib/apt/lists/*
RUN rm -rf /tmp/pear/

# Setup working directory
WORKDIR /app

COPY --from=builder /app /app
