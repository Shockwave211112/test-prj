#!/bin/sh

php artisan cache:clear
php artisan schedule:run
php artisan migrate:fresh --seed

