# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/compose/compose-file/#target

ARG PHP_VERSION=7.1
ARG NGINX_VERSION=1.15
ARG VARNISH_VERSION=6.0

FROM php:${PHP_VERSION}-fpm-alpine AS platform_php

# persistent / runtime deps
RUN apk add --no-cache \
		acl \
		file \
		gettext \
		git \
		mysql-client

ARG APCU_VERSION=5.1.12
RUN set -eux
RUN	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		mysql-dev \
		zlib-dev \
		freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev
RUN	docker-php-ext-configure zip --with-libzip && \
	docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include && \
	docker-php-ext-install -j$(nproc) \
		intl \
		pdo_mysql \
		zip \
		gd

RUN pecl install apcu-${APCU_VERSION} && docker-php-ext-enable apcu opcache
RUN pecl install xdebug-2.7.2 && docker-php-ext-enable xdebug
RUN pecl clear-cache

RUN	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)" && \
	apk add --no-cache --virtual .api-phpexts-rundeps $runDeps
RUN	apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/php.ini
COPY docker/php/conf.d/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN set -eux; \
	composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /var/www

COPY docker/wait-for /usr/local/bin/wait-for
COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint /usr/local/bin/wait-for

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM nginx:${NGINX_VERSION}-alpine AS platform_nginx

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www

COPY web/ web/
