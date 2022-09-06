FROM debian:buster-slim

ARG PHP_VERSION=8.1
ENV PHP_VERSION=$PHP_VERSION

# Installation des dépendances
RUN apt-get update && apt-get install -y --no-install-recommends \
    unzip \
    sudo \
    apt-transport-https \
    curl \
    ca-certificates \
    && rm -r /var/lib/apt/lists/* \
    && apt-get purge -y --auto-remove

# Installation de PHP
RUN curl -L https://packages.sury.org/php/apt.gpg -o /etc/apt/trusted.gpg.d/php.gpg \
    && echo "deb https://packages.sury.org/php/ buster main" >> /etc/apt/sources.list.d/php.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-ctype \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-pgsql \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-gd \
    && rm -r /var/lib/apt/lists/* \
    && apt-get purge -y --auto-remove

# Modification de la configuration de php-fpm
RUN cd /etc/php/${PHP_VERSION}/fpm/ \
    && sed -i 's/pid =/;pid =/g' php-fpm.conf \
    && sed -i 's/error_log =/;error_log =/g' php-fpm.conf \
    && sed -i 's/listen =/;listen =/g' pool.d/www.conf \
    && sed -i 's/upload_max_filesize = .*/upload_max_filesize = 20M/g' php.ini \
    && sed -i 's/post_max_size = .*/post_max_size = 21M/g' php.ini \
    && { \
    		echo '[global]'; \
    		echo 'error_log = /proc/self/fd/2'; \
    		echo 'log_limit = 8192'; \
    		echo; \
    		echo '[www]'; \
    		echo 'access.log = /proc/self/fd/2'; \
    		echo 'clear_env = no'; \
    		echo; \
    		echo 'catch_workers_output = yes'; \
    		echo 'decorate_workers_output = no'; \
    	} | tee pool.d/docker.conf \
    	&& { \
    		echo '[global]'; \
    		echo 'daemonize = no'; \
    		echo; \
    		echo '[www]'; \
    		echo 'listen = 9000'; \
    } | tee pool.d/zz-docker.conf

# Copie du binaire composer
COPY --from=composer:2.0.9 /usr/bin/composer /usr/bin/composer

# Installation de PHP CS Fixer
ARG PHPCSFIXER_VERSION=2.18.2
RUN curl -L https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v${PHPCSFIXER_VERSION}/php-cs-fixer.phar -o /usr/bin/php-cs-fixer \
    && chmod a+x /usr/bin/php-cs-fixer

# Création du répertoire principal
ARG PROJECT_DIR_ARG='/home/httpd'
ENV PROJECT_DIR=$PROJECT_DIR_ARG
RUN mkdir -p $PROJECT_DIR \
    && chown -R www-data:www-data $PROJECT_DIR \
    && usermod -d $PROJECT_DIR www-data
WORKDIR $PROJECT_DIR

# Entrypoint
COPY ./entrypoint.sh /bin/entrypoint.sh
RUN chmod +x /bin/entrypoint.sh
ENTRYPOINT ["/bin/entrypoint.sh"]
EXPOSE 9000
CMD ["php-fpm8.1"]
