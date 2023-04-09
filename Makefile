start:
	docker-compose up -d

phpcs-test:
	php -d memory_limit=1024M vendor/bin/phpcs --standard=phpcs.xml -p

unit-test:
	php vendor/bin/phpunit --configuration phpunit.xml --coverage-text

test:
	docker-compose exec php make unit-test \
	&& docker-compose exec php make phpcs-test

fix-cs:
	vendor/bin/phpcbf
