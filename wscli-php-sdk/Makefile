.PHONY: build clean dist-clean

all: swagger-php-sdk comp cs-fix build coverage

wsapi_v2.json:
	wget https://isecure.fi/wsapi_v2.json -O wsapi_v2.json.download
	mv -f wsapi_v2.json.download wsapi_v2.json

swagger-codegen-cli.jar:
	wget http://central.maven.org/maven2/io/swagger/swagger-codegen-cli/2.2.3/swagger-codegen-cli-2.2.3.jar -O swagger-codegen-cli.jar

swagger-php-sdk: swagger-codegen-cli.jar wsapi_v2.json
	rm -rf swagger-php-sdk
	java -jar swagger-codegen-cli.jar generate --config swagger-conf.json -i wsapi_v2.json -l php -o swagger-php-sdk

comp:
	composer self-update
	composer install --prefer-source

build: swagger-php-sdk comp
	php -l src/*.php
	composer cs-fix
	composer build
	composer dump-autoload --optimize

test: build
	composer test

coverage: build
	composer coverage

cs-fix:
	composer cs-fix

clean:
	rm -rf composer.phar build/ .php_cs.*
	find . -name "*~" -exec rm -f {} ";" -print

dist-clean: clean
	rm -rf swagger-php-sdk/ vendor/ wsapi_v2.json swagger-codegen-cli.jar
