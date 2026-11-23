FROM php:8.5-fpm

# sendmail powers the PHPMailer "mail" mailer (used when no SMTP server is configured)
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        sendmail \
    && rm -rf /var/lib/apt/lists/*
RUN echo "sendmail_path=/usr/sbin/sendmail -t -i" >> /usr/local/etc/php/conf.d/sendmail.ini

# Xdebug (pinned) for IDE debugging. Installed but disabled unless
# PHP_XDEBUG_ENABLED=1 is set (the entrypoint removes the ini otherwise).
RUN pecl channel-update pecl.php.net && \
    pecl install xdebug-3.5.3 && \
    echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini && \
    echo "xdebug.mode = debug" >> /usr/local/etc/php/conf.d/xdebug.ini && \
    mkdir -p /var/log/xdebug

# Customization of the entrypoint (kept idempotent so it is safe on every start):
# - start sendmail (the PHPMailer "mail" mailer shells out to it)
# - expose the container itself on the loopback name (needed by some SMTP configs)
# - disable xdebug unless explicitly enabled
RUN sed -i '/#!\/bin\/sh/a if [ "$PHP_XDEBUG_ENABLED" != "1" ]; then rm -f /usr/local/etc/php/conf.d/xdebug.ini; fi' /usr/local/bin/docker-php-entrypoint && \
    sed -i '/#!\/bin\/sh/a service sendmail restart || true' /usr/local/bin/docker-php-entrypoint && \
    sed -i '/#!\/bin\/sh/a grep -qF "$(hostname).localhost" /etc/hosts 2>/dev/null || echo "$(hostname -i)\t$(hostname) $(hostname).localhost" >> /etc/hosts' /usr/local/bin/docker-php-entrypoint
