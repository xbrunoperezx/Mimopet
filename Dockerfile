FROM php:8.2-apache

# Copia el código al document root
COPY . /var/www/html/

# Da permisos de lectura/escritura si lo necesitas
RUN chown -R www-data:www-data /var/www/html