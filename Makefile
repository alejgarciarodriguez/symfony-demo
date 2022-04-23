UID=$(shell id -u)
GID=$(shell id -g)
DOCKER_PHP_SERVICE=php

init: stop erase start install init_db fixtures

start:
	docker-compose up -d

stop:
	docker-compose stop

build:
	docker-compose build --pull

erase:
	docker-compose down

install:
	mkdir -p ~/.composer && chown ${UID}:${GID} ~/.composer
	docker-compose run --rm -u ${UID}:${GID} ${DOCKER_PHP_SERVICE} composer install

unit-test:
	docker-compose run --rm -u ${UID}:${GID} ${DOCKER_PHP_SERVICE} bin/phpunit

functional-test:
	docker-compose run --rm -u ${UID}:${GID} ${DOCKER_PHP_SERVICE} vendor/bin/behat

test: unit-test functional-test

init_db:
	docker-compose run --rm -u ${UID}:${GID} ${DOCKER_PHP_SERVICE} sh -c "./bin/console d:d:d --force --if-exists"
	docker-compose run --rm -u ${UID}:${GID} ${DOCKER_PHP_SERVICE} sh -c "./bin/console d:d:c"
	docker-compose run --rm -u ${UID}:${GID} ${DOCKER_PHP_SERVICE} sh -c "./bin/console d:m:m --no-interaction"

fixtures:
	docker-compose run --rm -u ${UID}:${GID} ${DOCKER_PHP_SERVICE} sh -c "./bin/console d:f:l --env=test --no-interaction"

sh:
	docker-compose run --rm -u ${UID}:${GID} ${DOCKER_PHP_SERVICE} sh

lint:
	docker-compose run --rm -u ${UID}:${GID} ${DOCKER_PHP_SERVICE} grumphp run
