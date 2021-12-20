#/bin/bash

# git
git pull

# Laravel
composer install
php artisan migrate
php artisan db:seed
php artisan optimize:clear
#php artisan optimize

# Listfile
#php artisan listfile:download
#php artisan listfile:cache
