up:
	docker-compose up -d

logs:
	docker-compose logs -f $(args)

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

reload:
	docker-compose restart wordpress
