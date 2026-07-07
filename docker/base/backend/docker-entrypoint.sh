#!/bin/sh

role=${CONTAINER_ROLE:-backend}

if [ "$role" = "backend" ]; then
    php /app/artisan migrate --force

    if [ "$RUN_SEEDER" = "true" ]; then
        php /app/artisan db:seed --force
    fi

    exec php-fpm --nodaemonize

elif [ "$role" = "kafka-consumer" ]; then
    exec php /app/artisan kafka:consume-messages

else
    echo "Could not match the container role: $role"
    exit 1
fi
