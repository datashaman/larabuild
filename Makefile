DOCKER_COMPOSE_TESTING = docker-compose -p larabuild_testing -f docker-compose.testing.yml
TAG = larabuild_app

dummy:

test:
	phpunit

build: docker-build-tag

admin-token:
	php artisan larabuild:access-token --email=admin-user@example.com

docker-prune: docker-prune-stopped docker-prune-untagged

docker-build-build: docker-build-local
	docker build -f Dockerfile.build --tag $(TAG):build .

docker-build-local:
	docker build --tag $(TAG) .

db-rebuild:
	mysqladmin -u$(DB_USERNAME) -p$(DB_PASSWORD) -h$(DB_HOST) drop $(DB_DATABASE)
	mysqladmin -u$(DB_USERNAME) -p$(DB_PASSWORD) -h$(DB_HOST) create $(DB_DATABASE)
	php artisan migrate --seed

dc-down:
	docker-compose down

dc-logs:
	docker-compose logs -f

dc-nuke:
	docker-compose down -v --remove-orphans

dc-ps:
	docker-compose ps

dc-test-bash:
	$(DOCKER_COMPOSE_TESTING) build
	$(DOCKER_COMPOSE_TESTING) exec --rm app bash

dc-test-down:
	$(DOCKER_COMPOSE_TESTING) down

dc-test-logs:
	$(DOCKER_COMPOSE_TESTING) logs -f

dc-test-nuke:
	$(DOCKER_COMPOSE_TESTING) down -v --remove-orphans

dc-test-ps:
	$(DOCKER_COMPOSE_TESTING) ps

dc-test-run:
	$(DOCKER_COMPOSE_TESTING) exec app php artisan migrate
	$(DOCKER_COMPOSE_TESTING) exec app phpunit

dc-test-up:
	$(DOCKER_COMPOSE_TESTING) up --build -d

dc-up:
	docker-compose up -d

nuke: dc-nuke dc-test-nuke

yarn-production:
	yarn run production
	dos2unix public/js/app.js
