FROM php:8.1

RUN apt-get update
RUN pecl channel-update pecl.php.net && pecl install ev \
    && pecl clear-cache \
    && rm -rf /tmp/pear \
    && apt-get install -y libzip-dev git wget libicu-dev libpq-dev cron libonig-dev make --no-install-recommends \
    && apt-get -y autoremove \
    && apt-get clean autoclean \
    && rm -rf /var/lib/apt/lists/*

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN docker-php-ext-install pdo pdo_pgsql mysqli pdo_mysql zip bcmath sockets

RUN wget https://getcomposer.org/download/2.7.2/composer.phar \
    && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www

CMD ["php", "-a"]

ENTRYPOINT ["./docker/php-bot/entrypoint.sh"]