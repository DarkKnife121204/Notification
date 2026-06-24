#!/bin/sh

role=${CONTAINER_ROLE:-backend}

if [ "$role" = "backend" ]; then
    exec php-fpm --nodaemonize

elif [ "$role" = "queue" ]; then
    php /app/artisan queue:work --verbose --tries=3 --timeout=120 --sleep=3

else
    echo "Could not match the container role: $role"
    exit 1
fi
