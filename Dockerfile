FROM phpswoole/swoole:4.7-php7.4-alpine
RUN apk add boost-dev ${PHPIZE_DEPS} \
 && mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
 && wget -c https://github.com/swoole/yasd/archive/refs/tags/v0.3.9.tar.gz -O - | tar -xz \
 && docker-php-source extract \
 && mv yasd-0.3.9 /usr/src/php/ext/yasd \
 && docker-php-ext-install yasd \
 && pecl install pcov \
 && docker-php-ext-enable yasd pcov \
 && { \
    echo "yasd.debug_mode=remote"; \
    echo "yasd.remote_host=host.docker.internal"; \
    echo "yasd.remote_port=9000"; \
 } | tee "$PHP_INI_DIR/conf.d/99_overrides.ini"
ENTRYPOINT [ "php" ]
