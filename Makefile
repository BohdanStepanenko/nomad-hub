-include .env

restart: down up

up: # create and start containers
	@docker compose -f ${DOCKER_CONFIG} up -d

down: # stop and destroy containers
	@docker compose -f ${DOCKER_CONFIG} down

down-volume: #  WARNING: stop and destroy containers with volumes
	@docker compose -f ${DOCKER_CONFIG} down -v

start: # start already created containers
	@docker compose -f ${DOCKER_CONFIG} start

stop: # stop containers, but not destroy
	@docker compose -f ${DOCKER_CONFIG} stop

ps: # show started containers and their status
	@docker compose -f ${DOCKER_CONFIG} ps

build: # build all dockerfile, if not built yet
	@docker compose -f ${DOCKER_CONFIG} build

connect_app: # app command line
	@docker compose -f ${DOCKER_CONFIG} exec -w /var/www/html/laravel app bash

connect_db: # database command line
	@docker compose -f ${DOCKER_CONFIG} exec db bash

vendor_self_update:
	@docker compose -f ${DOCKER_CONFIG} exec -w /var/www/html/laravel app composer self-update

vendor: # composer install
	@docker compose -f ${DOCKER_CONFIG} exec -w /var/www/html/laravel app composer install

cs_check: # check code style
	@docker compose -f ${DOCKER_CONFIG} exec -w /var/www/html/laravel app ./vendor/bin/php-cs-fixer fix -vvv --dry-run --show-progress=dots

cs_fix: # try to fix code style
	@docker compose -f ${DOCKER_CONFIG} exec -w /var/www/html/laravel app ./vendor/bin/php-cs-fixer fix -vvv --show-progress=dots

test:
	@docker compose -f ${DOCKER_CONFIG} exec -w /var/www/html/laravel app php artisan test

passport_install:
	@docker compose -f ${DOCKER_CONFIG} exec -w /var/www/html/laravel app php artisan passport:install --no-interaction

fresh: # refresh the database and run all database seeds
	@docker compose -f ${DOCKER_CONFIG} exec -w /var/www/html/laravel app php artisan migrate:fresh --seed
	@docker compose -f ${DOCKER_CONFIG} exec -w /var/www/html/laravel app php artisan passport:install --no-interaction

passport: # refresh the jwt secret
	@docker compose -f ${DOCKER_CONFIG} exec -w /var/www/html/laravel app  php artisan passport:client --personal

app_composer_dump: # composer dump-autoload
	@docker compose -f ${DOCKER_CONFIG} exec -w /var/www/html/laravel app composer dump-autoload
