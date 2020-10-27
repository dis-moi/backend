# Database migrations
./bin/console assets:install
./bin/console doctrine:migration:migrate --allow-no-migration --no-interaction
