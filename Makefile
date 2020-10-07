EXEC_PHP = docker-compose exec php
EXEC_PHP_ND = docker-compose exec -e SYMFONY_DEPRECATIONS_HELPER=disabled php
SYMFONY = $(EXEC_PHP) bin/console
COMPOSER = $(EXEC_PHP) composer

setup-tests:
	docker-compose -f docker-compose.yml -f docker-compose.ci.yml build
	docker-compose -f docker-compose.yml -f docker-compose.ci.yml up -d
	sleep 20
	$(COMPOSER) install --no-interaction

test:
	$(SYMFONY) doctrine:migrations:migrate -n --env=test
	$(SYMFONY) cache:clear --env=test
	$(EXEC_PHP) bin/phpunit

test-no-deprecation:
	$(SYMFONY) doctrine:migrations:migrate -n --env=test
	$(SYMFONY) cache:clear --env=test
	$(EXEC_PHP_ND) bin/phpunit

qtest:
	$(EXEC_PHP) bin/phpunit

qtest-no-deprecation:
	$(EXEC_PHP_ND) bin/phpunit

down:
	docker-compose down

fix:
	$(COMPOSER) fix
