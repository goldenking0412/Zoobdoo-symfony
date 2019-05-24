FROM php:7.0-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libmcrypt-dev \
    libpng-dev \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    mc \
    git \
    curl \
    libmagickwand-dev \
    mysql-client \
    cron \
    xvfb \
    libfontconfig \
    wkhtmltopdf \
    xauth

RUN apt-get install -y supervisor

RUN pecl install imagick
RUN docker-php-ext-enable imagick

RUN docker-php-ext-install \
    intl \
    mcrypt \
    opcache \
    pdo_mysql \
    soap \
    iconv \
    mysqli \
    zip \
    bcmath \
    sockets

RUN pecl install -o -f redis-4.0.0

RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable redis

# Xdebug for dev environment
ARG APP_ENV=dev
RUN printf "\033[0;36m===> \033[0;33m\$APP_ENV=$APP_ENV\033[0m\n"

RUN if [ "${APP_ENV}" = "dev" ];\
    then pecl channel-update pecl.php.net &&  pecl install xdebug-2.5.5 && docker-php-ext-enable xdebug;\
    else echo "";\
  fi

RUN rm -rf /var/lib/apt/lists/*
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY docker-entrypoint.sh /usr/local/bin/

RUN chmod +x /usr/local/bin/docker-entrypoint.sh
RUN mkdir -p /var/run/supervisord

WORKDIR /var/www

ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf" ]

#CMD ["php-fpm"]