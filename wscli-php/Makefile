.PHONY: build clean dist-clean release comp-release

run: comp cs-fix build # coverage
	php src/wscli.php --version

release: build comp-release
	( cd ../wscli-php-sdk; rm -rf vendor )
	( cd vendor/isecurefi/wscli-php-sdk; rm -f *.jar *.xml *.dist )
	( find . -name "invalid_link" -exec rm {} ";" )
	box build -v -c box.json
	rm -rf vendor; mv vendor.dev vendor
	./wscli.phar --version
	( ./wscli.phar || true )

comp-release:
	mkdir -p vendor
	rm -rf vendor.dev
	mv vendor vendor.dev
	composer install --prefer-source --no-dev
	composer dump-autoload --optimize

comp:
	composer self-update
	composer install --prefer-source
	composer update
	./vendor/bin/phpcs --config-set ignore_warnings_on_exit 1
	./vendor/bin/phpcs --config-set colors 1

build: clean comp
	php -l src/wscli.php
	composer cs-fix
	composer build

test: build
	composer test

#coverage: build
#	composer coverage

cs-fix:
	composer cs-fix

clean:
	rm -rf build/ .php_cs.* wscli.phar wscli
	find . -name "*~" -exec rm -f {} ";" -print

dist-clean: clean
	rm -rf vendor/ *.log
