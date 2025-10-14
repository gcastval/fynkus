ifeq ($(OS),Windows_NT)
VENDOR-BIN=.\\vendor\\bin\\
else
VENDOR-BIN=./vendor/bin/
endif


test:
	@docker-compose -f compose-test-db.yaml up -d --build --remove-orphans
	@bash -c "\
		until docker exec -it test-db psql -U root -d fynkus_test -c 'SELECT 1; '; do \
			echo 'Waiting DB...'; \
			sleep 5; \
		done \
	"
	php bin/console doctrine:database:create --if-not-exists --env=test
	php bin/console doctrine:migrations:migrate --no-interaction --all-or-nothing --env=test

	@$(VENDOR-BIN)phpunit
	@docker-compose -f compose-test-db.yaml down

database-create: #Create database
	php bin/console doctrine:database:create --if-not-exists

migrations: database-create #Generate migrations
	php bin/console doctrine:migrations:diff --no-interaction --allow-empty-diff

migrate: database-create #Migrate database
	php bin/console doctrine:migrations:migrate --no-interaction --all-or-nothing

migrate-prev: #Migrate database
	php bin/console doctrine:migrations:prev
