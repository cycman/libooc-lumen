FROM php:7.1-fpm

RUN mv /etc/apt/sources.list /etc/apt/sources.list.bak && \
        echo "deb http://mirrors.ustc.edu.cn/debian stable main contrib non-free" >/etc/apt/sources.list

RUN apt-get update && apt-get install -y \
libfreetype6-dev \
libjpeg62-turbo-dev \
libpng-dev \
&& docker-php-ext-install -j$(nproc) iconv \
&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
&& docker-php-ext-install -j$(nproc) gd

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug && \
         docker-php-ext-install \
         mysqli  \
         pdo \
         pdo_mysql && \
        docker-php-ext-enable  mysqli  pdo  pdo_mysql gd
