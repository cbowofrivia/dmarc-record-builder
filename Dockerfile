FROM docker.io/bitnami/php-fpm:8.0

RUN apt-get -y update
RUN apt-get -y install git

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
