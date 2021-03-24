FROM php:7.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    wget \
    grep \
        libmemcached-dev \
        zlib1g-dev \
    libcurl4-openssl-dev \
    libbrotli-dev \
    libevent-dev \
    libicu-dev \
    libidn11-dev \
    libidn2-0-dev \
    libssl-dev \
        libpq-dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
        && pecl install redis memcached raphf propro \
        && docker-php-ext-enable redis memcached raphf propro

#RUN pecl install pecl_http \
#    && echo "extension=http.so" > /usr/local/etc/php/conf.d/pecl-http.ini

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
       && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /tmp/pear

RUN pecl install xdebug-2.9.8 && docker-php-ext-enable xdebug

ARG UID
ARG NGINX_PHP_USER

RUN addgroup --gid $UID --system $NGINX_PHP_USER \
    && adduser --uid $UID --system --disabled-login --disabled-password \
    --gid $UID \
    $NGINX_PHP_USER

ARG PATH_LISTEN_SETTINGS=/usr/local/etc/php-fpm.d/zz-docker.conf

RUN sed -i 's@listen = 9000@listen = /sock/docker.sock@' $PATH_LISTEN_SETTINGS \
    && echo 'listen.owner = '$NGINX_PHP_USER >> $PATH_LISTEN_SETTINGS

# xdebug
ARG CLIENT_HOST
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
RUN sed -i 's@xdebug.client_host=.*@xdebug.client_host='$CLIENT_HOST'@' /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www/html
CMD ["php-fpm"]
