up:
	docker-compose up -d

logs:
	docker-compose logs -f $(service)

down:
	docker-compose down

build:
	docker-compose build $(args)

bash.exec:
	docker-compose exec wordpress bash $(args)

bash:
	docker-compose run wordpress bash $(args)

wp:
	docker-compose run wordpress wp $(args)

remove.volumes:
	docker-compose down --volumes

clear.docker:
	docker ps | awk '{print $$1}' | grep -v CONTAINER | xargs docker stop

config.env:
	cp .env.example .env

setup.inicial:
	docker-compose run wordpress inicial.sh

setup.padrao:
	docker-compose run wordpress padrao.sh

setup: setup.inicial setup.padrao
