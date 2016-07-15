
FROM php:7.0-zts

RUN apt-get update && apt-get install -y git libmcrypt-dev libxml2-dev zlib1g-dev libssl-dev mysql-client dnsmasq

RUN docker-php-ext-install mcrypt mbstring pdo pdo_mysql soap zip && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer

RUN yes | pecl install xdebug pthreads && \
    echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini && \
    docker-php-ext-enable pthreads

RUN curl http://get.sensiolabs.org/php-cs-fixer.phar -o /usr/bin/php-cs-fixer && \
    chmod a+x /usr/bin/php-cs-fixer

COPY etc/dnsmasq.conf /etc/dnsmasq.conf
COPY usr/local/etc/php/conf.d/common.ini /usr/local/etc/php/conf.d/common.ini

EXPOSE 80
EXPOSE 443

WORKDIR /var/www
