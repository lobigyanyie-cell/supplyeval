# Railway: PHP + Apache only (no Java). App URL: https://<host>/saas/
# Bookworm base for reproducible Apache layout; exactly one MPM must load.
FROM php:8.2-apache-bookworm

RUN docker-php-ext-install pdo_mysql mysqli \
    && a2enmod rewrite \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf

COPY docker/railway/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/railway/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /var/www/html
COPY . .

# php:apache uses mod_php → requires mpm_prefork only. Remove other MPM symlinks.
RUN set -eux; \
    rm -f /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_worker.load \
          /etc/apache2/mods-enabled/mpm_worker.conf; \
    if [ ! -e /etc/apache2/mods-enabled/mpm_prefork.load ]; then a2enmod mpm_prefork; fi; \
    apache2ctl -t

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
