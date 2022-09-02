FROM debian:buster-slim

ARG PHP_VERSION=8.0
ENV PHP_VERSION=$PHP_VERSION

# Installation des dépendances
RUN apt-get update -qq && apt-get install -y --no-install-recommends \
    unzip \
    apt-transport-https \
    curl \
    ca-certificates \
    && rm -r /var/lib/apt/lists /var/cache/apt/archives

# Installation de PHP
RUN curl -L https://packages.sury.org/php/apt.gpg -o /etc/apt/trusted.gpg.d/php.gpg \
    && echo "deb https://packages.sury.org/php/ buster main" >> /etc/apt/sources.list.d/php.list \
    && apt-get update -qq && apt-get install -y --no-install-recommends \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-ctype \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-zip \
    && rm -r /var/lib/apt/lists /var/cache/apt/archives

# Préparation du répertoire des sources
ARG PROJECT_DIR='/var/www/html'
ENV PROJECT_DIR=$PROJECT_DIR
WORKDIR $PROJECT_DIR

# Copie du binaire composer
COPY --from=composer:2.0.9 /usr/bin/composer /usr/bin/composer
# Copie des sources
COPY . $PROJECT_DIR

# Ajout des variables d'environement
ENV APP_ENV=prod
RUN echo "APP_SECRET=$(openssl rand -hex 12)" > $PROJECT_DIR/.env.local

# Installation des vendors
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
	--no-scripts \
	--prefer-dist \
	--no-progress \
	--no-interaction \
	--classmap-authoritative \
	--no-dev \
	--no-cache

# Nettoyage du dossier var
RUN rm -rf $PROJECT_DIR/var

# Modification de la configuration de php-fpm
RUN cd /etc/php/${PHP_VERSION}/fpm/ \
    && sed -i 's/pid =/;pid =/g' php-fpm.conf \
    && sed -i 's/error_log =/;error_log =/g' php-fpm.conf \
    && sed -i 's/listen =/;listen =/g' pool.d/www.conf \
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

STOPSIGNAL SIGQUIT

# Entrypoint
COPY ./docker/deploy/entrypoint.sh /bin/entrypoint.sh
RUN chmod +x /bin/entrypoint.sh
ENTRYPOINT ["/bin/entrypoint.sh"]

EXPOSE 9000
CMD ["php-fpm8.0"]
