# PHP 8.3 com Apache; a imagem oficial já vem com PDO SQLite
FROM php:8.3-apache

# Configuração de produção: erros vão para o log, não aparecem na tela
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Só a pasta public/ fica acessível pelo navegador; src/, views/ e dados/ ficam fora
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY . /var/www/html

# Pasta do banco SQLite com permissão de escrita para o Apache
RUN mkdir -p /var/www/html/dados && chown -R www-data:www-data /var/www/html/dados

EXPOSE 80
