up:
	docker-compose up -d

logs:
	docker-compose logs -f $(service)

down:
	docker-compose down

build:
	docker-compose build $(args)

bash.exec:
	docker-compose exec php bash $(args)

bash:
	docker-compose run php bash $(args)

wp:
	docker-compose run php wp $(args)

remove.volumes:
	docker-compose down --volumes

clear.docker:
	docker ps | awk '{print $$1}' | grep -v CONTAINER | xargs docker stop

config.env:
	cp .env.example .env

setup.inicial:
	docker-compose run php inicial.sh

setup.padrao:
	docker-compose run php padrao.sh

setup: setup.inicial setup.padrao
