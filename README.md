craft-backend-sf
================

A Symfony project created on May 9, 2016, 12:03 pm.

##### Staging Status

[![Build Status](https://semaphoreci.com/api/v1/projects/1ab935cc-487c-4be9-92a0-b0c90098cd58/1038377/shields_badge.svg)](https://semaphoreci.com/lmem/kraft-backend) [![Assertible status](https://assertible.com/apis/0a0c7a46-c0ff-4fc8-87fc-1cc1e5f933d4/status?api_token=2eF0Xz6R6s6CKi3Y&environment=staging)](https://assertible.com/dashboard#/services/0a0c7a46-c0ff-4fc8-87fc-1cc1e5f933d4/results)


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
dMigrate && dLoad
# avoid symlinks break
aphp bin/console assets:install web
```

#### admin access
[http://localhost:8088](http://localhost:8088)


### Tests
```sh
# clear cache in test envenv && run phpunit
runtests
```
