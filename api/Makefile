ifeq ($(OS),Windows_NT)
VENDOR-BIN=.\\vendor\\bin\\
else
VENDOR-BIN=./vendor/bin/
endif


test:
	$(VENDOR-BIN)phpunit

database-create: #Create database
	php bin/console doctrine:database:create --if-not-exists

migrations: database-create #Generate migrations
	php bin/console doctrine:migrations:diff --no-interaction --allow-empty-diff

migrate: database-create #Migrate database
	php bin/console doctrine:migrations:migrate --no-interaction --all-or-nothing

migrate-prev: #Migrate database
	php bin/console doctrine:migrations:prev
