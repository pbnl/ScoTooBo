FROM php:7.1-fpm
RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data

ENV APP_ENV=prod
ENV SYMFONY_ENV=prod

# install system basics
RUN apt-get update -qq && apt-get install -y -qq \
        git libmcrypt-dev libldap2-dev libicu-dev g++\
        libfreetype6-dev curl\
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev git libxml2-dev \
        zlib1g-dev nano\
        wget libpq-dev rsync

# install some php extensions
RUN docker-php-ext-install -j$(nproc) mbstring iconv mcrypt bcmath intl xml zip opcache pdo_pgsql\
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable opcache \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install ldap \
    && docker-php-ext-install mysqli pdo pdo_mysql

# install composer
RUN curl -s https://getcomposer.org/composer.phar > /usr/local/bin/composer \
    && chmod a+x /usr/local/bin/composer

WORKDIR /var/www/app

# copy the source code
COPY composer.* ./
COPY app app
COPY bin bin
COPY src src
COPY web web
COPY web /web-original
COPY docker/php-fpm/update_and_start_service.sh /update_and_start_service.sh
RUN chown www-data:www-data /update_and_start_service.sh
RUN chmod +x /update_and_start_service.sh

# set up correct permissions to run the next composer commands 
RUN mkdir var
RUN mkdir var/logs
RUN mkdir var/cache
RUN chown -R www-data:www-data /var/www

# generate the autoloaders and run composer scripts
USER www-data
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
#RUN export SYMFONY_ENV=prod && export APP_ENV=prod && composer run-script post-install-cmd --no-interaction --no-dev
CMD ["/update_and_start_service.sh"]