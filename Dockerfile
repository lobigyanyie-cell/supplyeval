# Railway: PHP + Apache only (no Java). App URL: https://<host>/saas/
FROM php:8.2-apache

# Only one Apache MPM may be loaded; php:apache uses prefork + mod_php
RUN docker-php-ext-install pdo_mysql mysqli \
    && (a2dismod mpm_event 2>/dev/null || true) \
    && (a2dismod mpm_worker 2>/dev/null || true) \
    && a2enmod mpm_prefork \
    && a2enmod rewrite \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

COPY docker/railway/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/railway/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /var/www/html
COPY . .

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
