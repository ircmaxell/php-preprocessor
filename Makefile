
install:
		composer install

build: install
		vendor/bin/phpunit
		php-cs-fixer fix ./src
		php-cs-fixer fix ./lib