#!/bin/sh

cd /test-prj
php artisan migrate:fresh --seed
