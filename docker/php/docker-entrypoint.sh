#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ ! -e 'vendor/autoload.php' ]; then
    composer install --no-progress --no-suggest
    bin/console assets:install web

    echo Fixing assets access rights...
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var

    bin/console doctrine:migrations:migrate -n
    bin/console doctrine:fixtures:load -n
fi

exec docker-php-entrypoint "$@"
