FROM php:8.0-fpm-alpine3.13
ENV PROJECT_DIR=/var/app

RUN apk add --no-cache --virtual .ext-deps postgresql-dev bash libzip-dev openssl-dev $PHPIZE_DEPS && \
	docker-php-ext-configure pdo_pgsql && \
	docker-php-ext-install pdo_pgsql pdo_mysql pdo sockets && \
    docker-php-ext-install opcache

ENV PATH /var/app/bin:/var/app/vendor/bin:$PATH
WORKDIR /var/app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache git
RUN pecl install xdebug && \
    docker-php-ext-enable xdebug
RUN mkdir -p $PROJECT_DIR/var/cache/dev && chown -R www-data:www-data $PROJECT_DIR/var/cache/dev

COPY .deployment/docker/php/config/php_dev.ini /usr/local/etc/php/php.ini
COPY .deployment/docker/php/config/www.conf /usr/local/etc/php-fpm.d/www.conf
