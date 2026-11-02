.PHONY: up down install assets migrate reset test console

up:
	docker compose up -d --build

down:
	docker compose down

install: up
	docker compose exec -T php-fpm composer install
	docker compose exec -T php-fpm php bin/console assets:install public --symlink --relative
	docker compose exec -T php-fpm php bin/console doctrine:migrations:migrate --no-interaction

assets:
	docker compose exec -T php-fpm php bin/console assets:install public --symlink --relative

migrate:
	docker compose exec -T php-fpm php bin/console doctrine:migrations:migrate --no-interaction

reset:
	docker compose exec -T php-fpm php bin/console doctrine:database:drop --force --if-exists
	docker compose exec -T php-fpm php bin/console doctrine:database:create
	docker compose exec -T php-fpm php bin/console doctrine:migrations:migrate --no-interaction

test:
	docker compose exec -T -e APP_ENV=test php-fpm php bin/phpunit

console:
	docker compose exec php-fpm bash
