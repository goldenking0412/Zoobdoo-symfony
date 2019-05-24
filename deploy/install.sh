#!/usr/bin/env bash

docker-compose exec php bash -c "composer install"
docker-compose exec php bash -c "php app/console app:version:bump"
docker-compose exec php bash -c "php app/console doctrine:database:create --if-not-exists"
docker-compose exec php bash -c "php app/console doctrine:schema:create"
docker-compose exec php bash -c "php app/console doctrine:fixtures:load"

docker-compose exec php bash -c "php app/console assets:install --symlink --env=dev"
docker-compose exec php bash -c "php app/console assetic:dump --env=dev"

sudo rm -Rf app/cache* app/logs/*
docker-compose exec php bash -c "php app/console cache:clear --env=dev"

docker-compose exec php bash -c "php app/console assets:install --symlink --env=prod"
docker-compose exec php bash -c "php app/console assetic:dump --env=prod"

sudo rm -Rf app/cache* app/logs/*
docker-compose exec php bash -c "php app/console cache:clear --env=prod"

docker-compose exec php bash -c "chown -R www-data:www-data app/cache"
docker-compose exec php bash -c "chown -R www-data:www-data app/logs"
docker-compose exec php bash -c "chown -R www-data:www-data web/uploads"
docker-compose exec php bash -c "chown -R www-data:www-data web/cache"
docker-compose exec php bash -c "php app/console security:check"
docker-compose exec php bash -c "php app/console app:version:bump"

sudo rm -Rf app/cache* app/logs/*

result=$?

if [ $result -eq 0 ]
then
  echo "Update succeed!"
else
  echo "Update failed!" >&2
fi

exit $result
