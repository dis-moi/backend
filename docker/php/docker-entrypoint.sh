#!/bin/sh
set -e

if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

composer install --no-progress --no-suggest
bin/console cache:clear
bin/console assets:install public --symlink

mkdir -p public/uploads
setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX public/uploads
setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX public/uploads

setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var

exec docker-php-entrypoint php-fpm
