#!/bin/sh

echo 'Creating .env file'
cp .env.example .env

echo 'Setting up dependencies'
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs

echo 'Starting laravel sail'
./vendor/bin/sail up -d

echo 'Starting application migrations'
sleep 10
./vendor/bin/sail artisan migrate

echo 'Building config'
./vendor/bin/sail artisan key:generate
