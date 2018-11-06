craft-backend-sf
================

A Symfony project created on May 9, 2016, 12:03 pm.

## Tests
To run the tests

- run the migrations on the test environment

    `php bin/console doctrine:migrations:migrate --env=test`

- execute phpunit

    `vendor/bin/phpunit` 


## Dev (w/ docker)
### Fresh install
```sh
docker-compose build
docker-compose up
. ./alias
aphp composer install
dropNmigrate && dLoad
# avoid symlinks break
aphp bin/console assets:install web
```

### Tests
```sh
# clear cache in test envenv && run phpunit
runtests
```
