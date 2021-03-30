FROM php:7.4-fpm

ARG UID
ARG NGINX_PHP_USER
ARG CLIENT_HOST
#ENV DOCKER_PATH=/usr/local/etc/php-fpm.d/zz-docker.conf
ENV XDEBUG_PATH=/usr/local/etc/php/conf.d/xdebug.ini

COPY xdebug.ini $XDEBUG_PATH
#COPY zz-docker.conf $DOCKER_PATH
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
  && apt-get update \
  && apt-get install -y \
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
      libpq-dev \
  && pecl install redis memcached raphf propro \
  && docker-php-ext-enable redis memcached raphf propro \
  && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo pdo_pgsql \
  && pecl install xdebug-2.9.8 && docker-php-ext-enable xdebug \
  && docker-php-ext-install sockets bcmath \
#  && addgroup --gid $UID --system $NGINX_PHP_USER \
#  && adduser --uid $UID --system --disabled-login --disabled-password --gid $UID $NGINX_PHP_USER \
#  && sed -i -r "s/%REPLACE_USERNAME%/$NGINX_PHP_USER/g" $DOCKER_PATH \
  && sed -i -r "s/%REPLACE_HOST%/$CLIENT_HOST/g" $XDEBUG_PATH \
  && rm -rf /tmp/pear

  RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

  CMD ["php-fpm"]