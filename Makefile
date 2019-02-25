USER = datashaman
PROJECT = larabuild
REPO = https://github.com/$(USER)/$(PROJECT).git
TAG = $(USER)/$(PROJECT):$(COMMIT)

dummy:

test:
	phpunit

build: docker-build-tag

setup:
	php artisan passport:client --name=demo --personal

admin-token: setup
	php artisan larabuild:access-token adminToken --email=admin-user@example.com

docker-prune-stopped:
	docker ps -a -q | xargs -r docker rm

docker-prune-untagged:
	docker images | grep '^<none>' | awk '{print $$3}' | xargs -r docker rmi

docker-prune: docker-prune-stopped docker-prune-untagged

docker-build-base:
	docker build -f Dockerfile.base --tag $(USER)/$(PROJECT):base .

docker-build-build:
	docker build -f Dockerfile.build --tag $(USER)/$(PROJECT):build .

docker-build-all: docker-build-base docker-build-build
	docker build .

docker-build-local: docker-build-base
	docker build .
