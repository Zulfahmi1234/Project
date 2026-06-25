FROM webdevops/php-nginx:8.3-alpine

# Set working directory secara eksplisit
WORKDIR /app

# Copy seluruh file backend
COPY . .

# Konfigurasi Nginx untuk menunjuk ke folder public Laravel
ENV WEB_DOCUMENT_ROOT=/app/public
ENV PHP_DISPLAY_ERRORS=0

# Konfigurasi Default Laravel
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

# Install composer dependencies
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Beri hak akses Nginx ke folder penting Laravel (Image ini menggunakan user 'application')
RUN chown -R application:application /app/storage /app/bootstrap/cache
