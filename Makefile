.PHONY: clean help build bundle zip version bundle-prod bundle-inte build-front build-back
PHP = $(shell which php 2> /dev/null)
DOCKER = $(shell docker ps 2> /dev/null)
NPM = $(shell which npm 2> /dev/null)
YARN = $(shell which yarn 2> /dev/null)
VERSION := $(shell git describe --tags)

SEM_VERSION := $(shell git describe --tags | sed 's/^v//')
MODULE := $(shell basename ${PWD})
PACKAGE := "${MODULE}-${VERSION}"
PHPSTAN := "phpstan/phpstan:0.12"
PRESTASHOP := "prestashop/prestashop:1.7.7.1"

# target: default                                - Calling build by default
default: build

# target: help                                   - Get help on this file
help:
	@egrep "^#" Makefile

# target: build                                  - Clean up the repository
clean:
	git -c core.excludesfile=/dev/null clean -X -d -f

# target: bundle                                 - Bundle local sources into a ZIP file
bundle: bundle-prod

# target: zip                                    - Alias of target: bundle
zip: bundle

# target: dist                                   - A directory to save zip bundles
dist:
	mkdir -p ./dist

# target: version                                - Replace version in files
version:
	echo "...$(VERSION)..."
	sed -i "" -e "s/\(VERSION = \).*/\1\'${SEM_VERSION}\';/" ps_accounts.php
	sed -i "" -e "s/\($this->version = \).*/\1\'${SEM_VERSION}\';/" ps_accounts.php
	sed -i "" -e "s|\(<version><!\[CDATA\[\)[0-9a-z.-]\{1,\}]]></version>|\1${SEM_VERSION}]]></version>|" config.xml

# target: bundle-prod                            - Bundle a production zip
bundle-prod: dist ./vendor ./views/index.php
	rm -f .env
	cd .. && zip -r ${PACKAGE}_prod.zip ${MODULE} -x '*.git*' \
	  ${MODULE}/_dev/\* \
	  ${MODULE}/dist/\* \
	  ${MODULE}/composer.phar \
	  ${MODULE}/Makefile
	mv ../${PACKAGE}_prod.zip ./dist

# target: bundle-prod                            - Bundle an integration zip
bundle-inte: dist .env.inte ./vendor ./views/index.php
	cp .env.inte .env
	cd .. && zip -r ${PACKAGE}_inte.zip ${MODULE} -x '*.git*' \
	  ${MODULE}/_dev/\* \
	  ${MODULE}/dist/\* \
	  ${MODULE}/composer.phar \
	  ${MODULE}/Makefile
	mv ../${PACKAGE}_inte.zip ./dist
	rm -f .env

# target: build                                  - Setup PHP & Node.js locally
build: build-front build-back

# target: build-front                            - Build front for prod locally
build-front:
ifndef YARN
    $(error "YARN is unavailable on your system, try `npm i -g yarn`")
endif
	yarn --cwd ./_dev --frozen-lockfile 
	yarn --cwd ./_dev build

# target: build-back                             - Build production dependencies
build-back: composer.phar
	./composer.phar install --no-dev

composer.phar:
ifndef PHP
    $(error "PHP is unavailable on your system")
endif
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
	php composer-setup.php
	php -r "unlink('composer-setup.php');"

# target: tests                                  - Launch the tests/lints suite front and back
tests: test-back test-front

# target: test-back                              - Launch the tests back
test-back: vendor/bin/php-cs-fixer
ifndef DOCKER
    $(error "DOCKER is unavailable on your system")
endif
	vendor/bin/php-cs-fixer fix --dry-run --diff --using-cache=no --diff-format udiff
	docker pull ${PHPSTAN}
	docker pull ${PRESTASHOP}
	docker run --rm -d -v ps-volume:/var/www/html --entrypoint /bin/sleep --name test-phpstan ${PRESTASHOP} 2s
	docker run --rm --volumes-from test-phpstan \
	  -v ${PWD}:/web/module \
	  -e _PS_ROOT_DIR_=/var/www/html \
	  --workdir=/web/module ${PHPSTAN} analyse \
	  --configuration=/web/module/tests/phpstan/phpstan-PS-1.7.neon

vendor/bin/php-cs-fixer:
	./composer.phar install

# target: test-front                             - Launch the tests front (does not work linter is not configured)
test-front:
	npm --prefix=./_dev run lint

# target: fix-lint                               - Launch php cs fixer and npm run lint
fix-lint: vendor/bin/php-cs-fixer
	vendor/bin/php-cs-fixer fix --using-cache=no
	npm --prefix=./_dev run lint --fix

vendor/bin/php-cs-fixer:
	./composer.phar install