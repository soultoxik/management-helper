FROM php:7.4-cli

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
  && apt-get update \
  && apt-get install -y \
      git \
      curl \
      wget \
      grep \
      zip \
      unzip \
      libcurl4-openssl-dev \
      libssl-dev \
      libzip-dev \
      libonig-dev \
      libpq-dev \
      librabbitmq-dev \
  && docker-php-ext-install zip sockets bcmath \
  && docker-php-ext-install mbstring \
  && pecl install amqp && docker-php-ext-enable amqp \
  && pecl install -o redis && docker-php-ext-enable redis

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

WORKDIR /var/www/html
CMD ["composer", "install"]