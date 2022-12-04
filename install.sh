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

echo 'Running migrations'
./vendor/bin/sail artisan migrate

./vendor/bin/sail artisan key:generate
