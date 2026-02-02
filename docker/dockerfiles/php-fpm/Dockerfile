
ARG PHP_FPM_VERSION
FROM php:${PHP_FPM_VERSION}

ARG PROJECT_PATH
RUN mkdir -p ${PROJECT_PATH}

RUN apt-get update
RUN apt-get install -y libonig-dev libzip-dev unzip msmtp msmtp-mta mailutils libfcgi-bin
# libfcgi-bin
RUN docker-php-ext-install mbstring zip bcmath pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer