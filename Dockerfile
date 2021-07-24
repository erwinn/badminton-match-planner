FROM php:8.0.8-cli

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apt update && apt install -y git && install-php-extensions zip gd

COPY . /app
WORKDIR /app