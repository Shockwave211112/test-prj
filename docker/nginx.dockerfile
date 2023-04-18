FROM nginx

ADD docker/conf/nginx-laravel.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/laravel-docker
