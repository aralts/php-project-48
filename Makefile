install:
	composer install

update:
	composer update

validate:
	composer validate

lint:
	composer exec --verbose phpcs -- --standard=PSR12 src bin