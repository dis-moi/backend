FROM php:7.0

RUN apt-get update
RUN apt-get install -q -y  git zip libcurl4-gnutls-dev libmcrypt-dev libicu-dev g++ mysql-client

RUN docker-php-ext-install pdo pdo_mysql curl json mcrypt intl bcmath pcntl

WORKDIR /usr/local/bin/
RUN php -r "readfile('https://getcomposer.org/installer');" > composer-setup.php
RUN php composer-setup.php
RUN rm composer-setup.php
RUN mv composer.phar composer

RUN echo "date.timezone = \"Europe/Paris\"" >> /usr/local/etc/php/php.ini
RUN echo "intl.default_locale = \"fr\"" >> /usr/local/etc/php/php.ini

WORKDIR /root
RUN /usr/local/bin/composer global require phpunit/phpunit
RUN ln -s /root/.composer/vendor/bin/phpunit /usr/local/bin/phpunit

WORKDIR /var/www
VOLUME /var/www

COPY docker_run.sh /tmp/docker_run.sh
RUN chmod a+x /tmp/docker_run.sh
CMD ["/tmp/docker_run.sh"]
