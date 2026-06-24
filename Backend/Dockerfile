FROM richarvey/nginx-php-fpm:3.1.6

# Set working directory secara eksplisit
WORKDIR /var/www/html

COPY . .

# Konfigurasi Image Nginx/PHP
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Konfigurasi Default Laravel
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Install composer dependencies saat BUILD image (Best Practice Docker)
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Beri hak akses Nginx ke folder penting Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

CMD ["/start.sh"]