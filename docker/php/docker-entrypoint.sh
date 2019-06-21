#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ ! -e 'vendor/autoload.php' ]; then
    composer install --no-progress --no-suggest
    bin/console assets:install web

    bin/console doctrine:migrations:migrate -n
    bin/console doctrine:fixtures:load -n
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
fi

exec docker-php-entrypoint "$@"
