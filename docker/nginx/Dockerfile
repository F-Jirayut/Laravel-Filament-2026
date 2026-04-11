FROM nginx:alpine

RUN apk update && \
    apk upgrade && \
    rm -rf /var/cache/apk/*

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

RUN mkdir -p /var/www \
    /var/cache/nginx \
    /var/run \
    /var/log/nginx && \
    chown -R nginx:nginx /var/www \
    /var/cache/nginx \
    /var/run \
    /var/log/nginx \
    /etc/nginx/conf.d

WORKDIR /var/www