#!/bin/bash

PHP_UID_DEFAULT=$(id -u www-data)

if [[ -z "$(ls -n $PROJECT_DIR | grep $PHP_UID_DEFAULT)" ]]; then

    : ${PHP_UID:=$(id -u www-data)}
    : ${PHP_GID:=$(id -g www-data)}

    export PHP_UID
    export PHP_GID

    if [ "$PHP_UID" != "0" ] && [ "$PHP_UID" != "$(id -u www-data)" ]; then
      usermod  -u $PHP_UID www-data
      groupmod -g $PHP_GID www-data
    fi
fi

if [ "$*" != "php-fpm${PHP_VERSION}" ]; then
    HOME=$PROJECT_DIR sudo -uwww-data "$@"
else
  exec "$@"
fi
