FROM php:8.1.3-fpm

RUN apt-get update && apt-get install -y \
        curl \
        wget \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libonig-dev \
        libzip-dev \
        libicu-dev \
        libmcrypt-dev \
        && docker-php-ext-install -j$(nproc) iconv mbstring mysqli pdo_mysql zip \
        && docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install -j$(nproc) gd \
        && docker-php-ext-configure intl \
        && docker-php-ext-install intl \
        && docker-php-ext-enable intl

RUN apt-get install -y redis-server
RUN wget https://github.com/FriendsOfPHP/pickle/releases/latest/download/pickle.phar && chmod +x pickle.phar && mv pickle.phar /usr/bin/pickle
RUN pickle install igbinary && docker-php-ext-enable igbinary
RUN pickle install redis && docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ADD php.ini /usr/local/etc/php/conf.d/40-custom.ini

WORKDIR /var/www

CMD ["php-fpm"]
