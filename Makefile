init: docker-down docker-pull docker-build docker-up app-init

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build --no-cache

app-init: app-install-composer app-schema-update append-fixtures

app-install-composer:
	docker-compose run --rm php-cli composer install --ignore-platform-reqs

app-console:
	docker-compose run --rm php-cli bin/console ${command}

app-migrate:
	docker-compose run --rm php-cli php bin/console doctrine:migrations:migrate --quiet

app-schema-update:
	docker-compose run --rm php-cli php bin/console doctrine:schema:update --force

app-test:
	docker-compose run --rm php-cli ./bin/phpunit

composer-update:
	yes | docker-compose run --rm php-cli composer update --ignore-platform-reqs

append-fixtures:
	docker-compose run --rm php-cli bin/console doctrine:fixtures:load --append

events:
	docker-compose run --rm php-cli bin/console debug:messenger

queue-listen:
	docker-compose run --rm php-cli bin/console messenger:consume async -vv

queue-stop-workers:
	docker-compose run --rm php-cli bin/console messenger:stop-workers
