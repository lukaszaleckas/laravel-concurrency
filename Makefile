start:
	docker-compose up -d

phpcs-test:
	php -d memory_limit=1024M vendor/bin/phpcs --standard=phpcs.xml -p

phpstan-test:
	php -d memory_limit=1024M vendor/bin/phpstan analyse

unit-test:
	php -dxdebug.mode=coverage vendor/bin/phpunit --configuration phpunit.xml --coverage-text

test:
	make unit-test && make phpcs-test && make phpstan-test

fix-cs:
	vendor/bin/phpcbf
