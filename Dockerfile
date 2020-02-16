FROM php:7.4-fpm

# ARGUMENTS DURING BUILDING
ARG CUSTOM_USER_ID
ARG CUSTOM_USER_GROUP

ENV USER_ID=$CUSTOM_USER_ID \
    USER_GROUP=$CUSTOM_USER_GROUP \
    DEBIAN_FRONTEND=noninteractive \
    ENVIRONMENT="dev" \
    PROJECT_PATH=/var/www/html \
    PHP_INI=/usr/local/etc/php/php.ini-development \
    TERM=xterm
RUN printf "USER_ID=$CUSTOM_USER_ID\n"
RUN printf "USER_GROUP=$CUSTOM_USER_GROUP\n"
WORKDIR /var/www/html

COPY docker/scripts /scripts

RUN apt update -q && apt upgrade -yqq && apt install -yqq --no-install-recommends \
        git \
        git-core \
        gnupg \
        libfcgi0ldbl \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libpq-dev \
        libzip-dev \
        libssl-dev \
        unzip \
        vim \
        wget \
        zip \
        zlib1g-dev \
    && pecl install apcu-5.1.18 xdebug-2.9.1 \
    && docker-php-ext-enable apcu xdebug \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        intl \
        mysqli \
        pdo_mysql \
        zip \
    && curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony/bin/symfony /usr/local/bin/symfony \
    && apt autoremove -y \
    && cp ${PHP_INI_DIR}/php.ini-development ${PHP_INI_DIR}/php.ini


ENTRYPOINT ["/scripts/entrypoint.sh"]

CMD ["php-fpm", "--nodaemonize"]

