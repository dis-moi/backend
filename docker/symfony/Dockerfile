FROM php:7.1

RUN apt-get update
RUN apt-get install -q -y  git zip libcurl4-gnutls-dev libmcrypt-dev libicu-dev g++

RUN docker-php-ext-install pdo pdo_mysql curl json mcrypt intl bcmath pcntl

WORKDIR /usr/local/bin/
RUN php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php
RUN php composer-setup.php
RUN rm composer-setup.php
RUN mv composer.phar composer
WORKDIR /var/www

RUN echo "date.timezone = \"Europe/Paris\"" >> /usr/local/etc/php/php.ini
RUN echo "intl.default_locale = \"fr\"" >> /usr/local/etc/php/php.ini

VOLUME /var/www
