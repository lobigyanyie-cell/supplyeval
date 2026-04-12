#!/bin/sh
set -e
PORT="${PORT:-8080}"
exec php -S "0.0.0.0:${PORT}" -t /var/www/html/public /var/www/html/public/router.php
