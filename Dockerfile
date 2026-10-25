FROM php:8.3-apache

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && docker-php-ext-install mysqli \
    && a2enmod rewrite headers \
    && sed -ri 's!/var/www/html!/var/www/app/public!g' /etc/apache2/sites-available/000-default.conf \
    && printf '<Directory /var/www/app/public>\n  AllowOverride None\n  Require all granted\n  FallbackResource /index.php\n</Directory>\nServerName localhost\n' \
       > /etc/apache2/conf-enabled/app.conf

WORKDIR /var/www/app
COPY . .

# Railway injects PORT; Apache must listen on it.
# Railway's builder can restore the base image's removed mpm_event link
# ("More than one MPM loaded"), so keep only mpm_prefork at start.
ENV PORT=8080
CMD rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* \
    && sed -ri "s/Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf \
    && sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf \
    && exec apache2-foreground
