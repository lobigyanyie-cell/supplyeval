# Railway: PHP CLI built-in server (no Apache → no MPM issues). App: https://<host>/saas/
FROM php:8.2-cli-bookworm

RUN docker-php-ext-install pdo_mysql mysqli

WORKDIR /var/www/html
COPY . .

COPY docker/railway/router.php public/router.php
COPY docker/railway/start-php.sh /start-php.sh
RUN chmod +x /start-php.sh

EXPOSE 8080

ENTRYPOINT ["/start-php.sh"]
