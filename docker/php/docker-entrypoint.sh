#!/bin/sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

if [ "$1" = 'php-fpm' ] || [ "$1" = 'bin/console' ]; then
	mkdir -p var/cache var/log
	#setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
	#setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var

	#bin/console doctrine:migrations:migrate --no-interaction
    #bin/console doctrine:fixtures:load -n
fi

exec docker-php-entrypoint "$@"
