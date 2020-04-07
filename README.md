craft-backend-sf
================

A Symfony project created on May 9, 2016, 12:03 pm.

##### Staging Status

[![Build Status](https://semaphoreci.com/api/v1/projects/1ab935cc-487c-4be9-92a0-b0c90098cd58/1038377/shields_badge.svg)](https://semaphoreci.com/lmem/kraft-backend) [![Assertible status](https://assertible.com/apis/0a0c7a46-c0ff-4fc8-87fc-1cc1e5f933d4/status?api_token=2eF0Xz6R6s6CKi3Y&environment=staging)](https://assertible.com/dashboard#/services/0a0c7a46-c0ff-4fc8-87fc-1cc1e5f933d4/results)


## Tests

Setup and migrate test database and run phpunit:

```
$ runtests
```


## Dev (w/ docker)

The first run may take few minutes since images are to be built,
composer has to install project dependencies and doctrine has to
migrate databases and load fixtures. Subsequent runs are much faster.

```
$ docker-compose up
```

#### Aliases

Some aliases are conveniently made available... 

```
$ . ./alias
$ aphp composer install
$ aphp bin/console assets:install web
$ dMigrate && dLoad
```

##### Code style
To fix code style automatically, run the following command:
```
aphp composer fix
```

#### Admin access
[http://localhost:8088](http://localhost:8088)
