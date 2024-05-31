init:
	@make build
	@make up
	@make composer-install
	docker compose exec app cp .env.example .env
	docker compose exec app php artisan key:generate
	@make npm-install

up:
	docker compose up -d

up-with-logs:
	docker compose up

db-migrate-seed:
	docker compose exec app php artisan migrate:fresh --seed

ps:
	docker compose ps

down:
	docker compose down

down-v:
	docker compose down -v

restart:
	@make down
	@make up

login-app:
	docker compose exec app bash

login-db:
	docker compose exec db bash

build:
	docker compose build

composer-install:
	docker compose exec app composer install

npm-install:
	docker compose exec app npm install

larastan:
	docker compose exec app ./vendor/bin/phpstan analyse -c phpstan.neon

pint:
	docker compose exec app ./vendor/bin/pint