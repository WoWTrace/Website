#/bin/bash

# git
git pull

# Laravel
composer install
php artisan migrate
php artisan db:seed
php artisan l5-swagger:generate
php artisan optimize