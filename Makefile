EXEC_PHP = docker-compose exec php
EXEC_PHP_ND = docker-compose exec -e SYMFONY_DEPRECATIONS_HELPER=disabled php
SYMFONY = $(EXEC_PHP) bin/console
COMPOSER = $(EXEC_PHP) composer

up:
	docker-compose build
	docker-compose up -d
	sleep 20
	$(COMPOSER) install --no-interaction
	$(SYMFONY) cache:clear
	$(SYMFONY) assets:install public --symlink
	$(SYMFONY) doctrine:migrations:migrate -n
	$(SYMFONY) doctrine:fixtures:load -n

composer-update:
	$(COMPOSER) update

test:
	$(SYMFONY) doctrine:migrations:migrate -n --env=test
	$(SYMFONY) cache:clear --env=test
	$(EXEC_PHP) bin/phpunit

test-no-deprecation:
	$(SYMFONY) doctrine:migrations:migrate -n --env=test
	$(SYMFONY) cache:clear --env=test
	$(EXEC_PHP_ND) bin/phpunit

retest:
	$(EXEC_PHP) bin/phpunit

retest-no-deprecation:
	$(EXEC_PHP_ND) bin/phpunit

cs-fix:
	$(COMPOSER) fix

stop:
	docker-compose stop

down:
	docker-compose down
