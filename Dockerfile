FROM php:apache

RUN docker-php-ext-install pdo

ENTRYPOINT [ "apache2ctl","-D", "FOREGROUND" ]