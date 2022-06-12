FROM php:7.4.4-fpm

RUN apt-get update \
    && apt-get install -y \
    git \
    wget \
    python \
    zlib1g-dev \
    libzip-dev

RUN docker-php-ext-install zip pdo_mysql opcache
RUN docker-php-source delete

# Install xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
ARG XDEBUG_INI=/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.default_enable = on" >> ${XDEBUG_INI} \
    && echo "xdebug.remote_enable = on" >> ${XDEBUG_INI} \
    && echo "xdebug.remote_autostart = off" >> ${XDEBUG_INI}

# Add configs
ENV TZ=Asia/Hong_Kong
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
COPY /config/php.ini /usr/local/etc/php/php.ini

# Install Composer
RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod 755 /usr/local/bin/composer && chmod +x /usr/local/bin/composer

# Install Symfony command
RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

# Install php-cs-fixer
RUN curl -L https://cs.symfony.com/download/php-cs-fixer-v2.phar -o php-cs-fixer \
    && chmod a+x php-cs-fixer \
    && mv php-cs-fixer /usr/local/bin/php-cs-fixer

# Define rights
RUN chown -R www-data:www-data /var/www

# General
USER www-data
WORKDIR /var/www/workspace
