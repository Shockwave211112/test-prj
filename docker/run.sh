#!/bin/sh

php artisan cache:clear
php artisan schedule:run
php artisan migrate:fresh --seed
php artisan serve --host=0.0.0.0 --port=9000
