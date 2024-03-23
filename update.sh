#!/bin/bash

echo "Updating courierassistant.com"

git pull
npm install
npm run build
composer install
php artisan migrate
php artisan cache:clear
php artisan config:clear

echo "Updating courierassistant.com done"