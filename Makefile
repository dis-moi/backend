EXEC_PHP = docker exec -e SYMFONY_DEPRECATIONS_HELPER=disabled kraft-backend_php
SYMFONY = $(EXEC_PHP) bin/console
COMPOSER = $(EXEC_PHP) composer

setup-tests:
	cp app/config/parameters.yml.ci app/config/parameters.yml
	docker-compose -f docker-compose.yml -f docker-compose.ci.yml build
	docker-compose -f docker-compose.yml -f docker-compose.ci.yml up -d
	sleep 20
	$(COMPOSER) install --no-interaction

test:
	$(SYMFONY) doctrine:migrations:migrate -n --env=test
	$(SYMFONY) cache:clear --env=test
	$(EXEC_PHP) vendor/bin/simple-phpunit
	$(DOCKER_COMPOSE) down

fix:
	$(COMPOSER) fix
