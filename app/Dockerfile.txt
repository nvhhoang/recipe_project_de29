FROM php:8.3-fpm-bookworm
RUN docker-php-ext-install pdo pdo_mysql
WORKDIR /var/www/html
COPY src/ /var/www/html/
RUN mkdir -p /var/log/recipe && chown -R www-data:www-data /var/www/html /var/log/recipe
USER www-data
CMD ["php-fpm", "-F"]
