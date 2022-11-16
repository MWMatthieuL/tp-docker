COMPOSE=docker-compose -f docker-compose.yml -f docker-compose-dev.yml
COMPOSE_MAC=$(COMPOSE) -f docker-compose-sync.yml
EXEC=$(COMPOSE) exec app
CONSOLE=$(EXEC) bin/console
ENVIRONMENT=$(shell bash ./scripts/get-env.sh)
SHELL := /bin/bash
MUTAGEN_NAME=$(shell bash ./scripts/get-mutagen-name.sh)

.PHONY: start up perm db cc ssh vendor assets assets-watch stop rm
.PHONY: maintenance-on maintenance-off

start: up perm ssh-key vendor assets db cc perm robots-disallow

up:
	docker kill $$(docker ps -q) || true
ifeq ($(ENVIRONMENT),Mac)
	$(COMPOSE_MAC) build --force-rm
	$(COMPOSE_MAC) up -d
	bash ./scripts/start-macos.sh
else
	$(COMPOSE) build --force-rm
	$(COMPOSE) up -d
endif

stop:
ifeq ($(ENVIRONMENT),Mac)
	$(COMPOSE_MAC) stop
	$(COMPOSE_MAC) kill
	mutagen sync pause $(MUTAGEN_NAME)
else
	$(COMPOSE) stop
	$(COMPOSE) kill
endif

rm:
	make stop
ifeq ($(ENVIRONMENT),Mac)
	$(COMPOSE_MAC) rm
	mutagen sync terminate $(MUTAGEN_NAME)
else
	$(COMPOSE) rm
endif

vendor: wait-for-db
	$(EXEC) composer install -n
	$(EXEC) yarn install --pure-lockfile
	make perm

ssh:
	$(EXEC) bash

ssh-key:
	$(EXEC) ssh-add /root/.ssh/id_rsa

run:
	$(EXEC) $(c)

sf:
	$(EXEC) bin/console $(c)

# Databases
db: wait-for-db
	$(EXEC) bin/console doctrine:database:drop --if-exists --force
	$(EXEC) bin/console doctrine:database:create --if-not-exists
	$(EXEC) bin/console doctrine:schema:update --force
	$(EXEC) bin/console doctrine:fixtures:load -n

db-migrate:
	$(EXEC) bin/console doctrine:migration:migrate

# Assets
assets:
	$(EXEC) bin/console assets:install
	$(EXEC) yarn run encore dev

assets-watch:
	$(EXEC) bin/console assets:install
	$(EXEC) yarn watch

# Permission
perm:
ifeq ($(ENVIRONMENT),Linux)
	sudo chown -R www-data:$(USER) .
	sudo chmod -R g+rwx .
else
	$(EXEC) chown -R www-data:root .
	$(EXEC) chown -R www-data:root public/
endif

robots-disallow:
	$(EXEC) cp robots/robots-disallow.txt public/robots.txt

# Cache
cc:
	$(EXEC) bin/console c:cl --no-warmup
	$(EXEC) bin/console c:warmup

# translations
trans:
	$(EXEC) bin/console lexik:translations:import

# Fixer
php-cs-fixer:
	sh -c "COMPOSE_INTERACTIVE_NO_CLI=1 $(EXEC) php-cs-fixer fix --config=.php-cs-fixer.php"
	sh -c "COMPOSE_INTERACTIVE_NO_CLI=1 $(EXEC) php-cs-fixer fix -v --dry-run  --config=.php-cs-fixer.php"

# Tests
tf: wait-for-db
	@echo "Not implemented"

tfdev: wait-for-db
	@echo "Not implemented"

tu: wait-for-db
	$(EXEC) vendor/bin/phpunit --stop-on-failure

# Update prod
update-prod:
	# Set maintenance to on
	make maintenance-on
	# Fetch updates
	git pull origin master
	# Clear cache
	bin/console c:cl --no-warmup
	bin/console c:warmup
	# Install dependencies
	composer install --classmap-authoritative --no-interaction --no-scripts --no-ansi --no-dev
	# Build JS + CSS files
	bin/console assets:install
	yarn build
	# Update Database
	bin/console doctrine:migration:migrate
	cp robots/robots-allow.txt public/robots.txt
	# Set maintenance to off
	make maintenance-off

sonar:
	$(EXEC) sonar-scanner
	@echo "Consult 'http://localhost:9000/dashboard' for sonar-scanner analysis"

maintenance-on:
ifeq ($(shell test -e public/index.php.old && echo -n yes),yes)
	@echo "Maintenance is already enabled."
else
	mv public/index.php public/index.php.old
	cp public/maintenance.html public/index.php
	@echo "Maintenance is enabled."
endif

maintenance-off:
ifeq ($(shell test -e public/index.php.old && echo -n yes),yes)
	mv public/index.php.old public/index.php
endif

# Wait commands
wait-for-db:
	$(EXEC) php -r "set_time_limit(60);for(;;){if(@fsockopen(\"db\",3306)){break;}echo \"Waiting for DB\n\";sleep(1);}"
