FROM php:8.4-fpm-alpine3.23 AS backend

ENV PROJECT_PATH=/app
ENV XDG_CONFIG_HOME=/tmp/tinker

WORKDIR ${PROJECT_PATH}

RUN set -ex && \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        postgresql-dev && \
    apk add --no-cache \
        icu \
        libzip \
        libpq \
        dos2unix \
        curl && \
    docker-php-ext-install \
        intl \
        pcntl \
        opcache \
        pdo_pgsql \
        zip && \
    apk del .build-deps && \
    docker-php-source delete && \
    rm -rf /var/cache/apk/* /tmp/pear

COPY php.ini /usr/local/etc/php/php.ini

COPY --chmod=777 docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN dos2unix /usr/local/bin/docker-entrypoint.sh && \
    mkdir -p /tmp/tinker && \
    chmod 777 -R /tmp/tinker

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

USER www-data:www-data

CMD ["/usr/local/bin/docker-entrypoint.sh"]
