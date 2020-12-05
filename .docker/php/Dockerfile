FROM php:7.4-fpm

RUN apt-get update && \
    apt-get install -y \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zlib1g-dev

RUN docker-php-ext-install mbstring iconv intl pdo_mysql zip

RUN php -r 'readfile("https://getcomposer.org/installer");' > /tmp/composer-setup.php \
	&& php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
	&& rm -f /tmp/composer-setup.php

WORKDIR /srv/app
