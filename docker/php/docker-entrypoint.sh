#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

fresh_install () {
    composer install --no-progress --no-suggest
    bin/console assets:install web

    bin/console doctrine:migrations:migrate -n
    bin/console doctrine:fixtures:load -n
}

if [ ! -e 'vendor/autoload.php' ]; then
    fresh_install
fi

sleep 5
if bin/console doctrine:migrations:status -n --show-versions | grep 'not migrated'; then
    fresh_install
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
    mkdir -p web/media
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX web/media
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX web/media

    mkdir -p web/uploads
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX web/uploads
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX web/uploads

	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
fi

exec docker-php-entrypoint "$@"
