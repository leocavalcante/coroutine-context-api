#!/usr/bin/env sh
echo "yasd.remote_host=$(getent hosts host.docker.internal | awk '{print $1}')" >> "$PHP_INI_DIR/conf.d/99_overrides.ini"
php "$@"
