FROM php:8.3-cli-alpine

RUN apk add --no-cache \
    git unzip curl icu-dev libxml2-dev zlib-dev oniguruma-dev bash \
 && docker-php-ext-install intl pdo pdo_mysql opcache

# 🧠 Zwiększamy limit pamięci PHP do 512M
RUN echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/memlimit.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Etap build: zrób projekt Symfony do tymczasowego użycia
WORKDIR /tmp/symfony
RUN composer create-project symfony/skeleton . \
 && composer require webapp api

# Docelowy katalog (volume mount point)
WORKDIR /app

CMD ["/bin/sh", "-c", "\
  if [ ! -f /app/composer.json ]; then \
    echo '📦 Instaluję Symfony...'; \
    cp -R /tmp/symfony/. /app; \
    composer install --no-interaction --prefer-dist; \
  fi && \
  echo '🚀 Uruchamiam Symfony na http://localhost:8080'; \
  php -S 0.0.0.0:8000 -t public \
"]
