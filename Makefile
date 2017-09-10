
install:
		composer install

test: install
		phpdbg -rr vendor/bin/phpunit --coverage-text

build: install test
		php-cs-fixer fix ./src
		php-cs-fixer fix ./lib