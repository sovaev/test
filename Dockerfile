FROM php:7.2-fpm-stretch

RUN pecl install redis && docker-php-ext-install bcmath
RUN apt-get update && apt-get install wget && wget https://phar.phpunit.de/phpunit.phar \
    && chmod +x phpunit.phar && mv phpunit.phar /usr/local/bin/phpunit

RUN echo "extension=redis.so" >> "/usr/local/etc/php/conf.d/zz-redis.ini"

RUN mkdir /app

WORKDIR /app
COPY ./app /app
