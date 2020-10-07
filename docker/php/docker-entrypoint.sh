#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

composer install --no-progress --no-suggest
bin/console assets:install web

wait-for db:3306 -- bin/console doctrine:migrations:migrate -n
wait-for db:3306 -- bin/console doctrine:fixtures:load -n

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
    mkdir -p web/media
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX web/media
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX web/media

    mkdir -p public/uploads
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX public/uploads
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX public/uploads

	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var
fi

exec docker-php-entrypoint "$@"
