#!/bin/bash

# Start services
docker-compose up -d --force-recreate

# Initialize project
docker-compose exec phpfpm composer self-update --no-interaction
docker-compose exec phpfpm composer install --no-interaction #--no-dev --optimize-autoloader
docker-compose exec phpfpm chmod 755 ./bin/console
docker-compose exec phpfpm ./bin/console doctrine:migrations:migrate --allow-no-migration --no-interaction
docker-compose exec phpfpm ./bin/console cache:warmup
docker-compose exec phpfpm rm -rf var/cache/
