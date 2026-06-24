FROM nginx:1.27-alpine

ARG WWWUSER=1000
ARG WWWGROUP=1000

COPY nginx.conf /etc/nginx/nginx.conf
COPY conf.d/default.conf /etc/nginx/conf.d/default.conf

RUN addgroup -g ${WWWGROUP} -S www-data || true && \
    adduser -u ${WWWUSER} -D -S -G www-data www-data || true
