FROM phpswoole/swoole:4.6-php7.4
RUN pecl install pcov \
 && docker-php-ext-enable pcov
