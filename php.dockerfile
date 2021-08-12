FROM php:8.0-fpm

RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev libzip-dev sendmail    
RUN echo "sendmail_path=/usr/sbin/sendmail -t -i" >> /usr/local/etc/php/conf.d/sendmail.ini

RUN docker-php-ext-install pdo pdo_mysql && \
pecl install xdebug \
    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode = debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.start_with_request = ye" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN sed -i '/#!\/bin\/sh/aservice sendmail restart' /usr/local/bin/docker-php-entrypoint

RUN sed -i '/#!\/bin\/sh/aecho "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts' /usr/local/bin/docker-php-entrypoint

    
    
    #&& echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini
    #&& echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini