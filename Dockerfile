FROM phpswoole/swoole:4.6-php7.4-alpine
RUN apk add boost-dev \
 && mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
 && wget -c https://github.com/swoole/yasd/archive/refs/tags/v0.3.7.tar.gz -O - | tar -xz \
 && docker-php-source extract \
 && mv yasd-0.3.7 /usr/src/php/ext/yasd \
 && docker-php-ext-install yasd \
 && { \
    echo "yasd.debug_mode=remote"; \
    echo "yasd.remote_port=9000"; \
 } | tee "$PHP_INI_DIR/conf.d/99_overrides.ini"

ADD docker-entrypoint.sh /docker-entrypoint.sh
ENTRYPOINT [ "sh", "-c", "/docker-entrypoint.sh" ]
