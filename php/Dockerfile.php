FROM php:7.4-apache
ARG UID
ARG GID
RUN apt-get update; apt-get install unzip git -y
RUN docker-php-ext-install mysqli && a2enmod rewrite
RUN a2enmod rewrite headers
RUN sed -ri -e 's/^([ \t]*)(<\/VirtualHost>)/\1\tHeader set Access-Control-Allow-Headers "Content-Type"\n\1\2/g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's/^([ \t]*)(<\/VirtualHost>)/\1\tHeader set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"\n\1\2/g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's/^([ \t]*)(<\/VirtualHost>)/\1\tHeader set Access-Control-Allow-Origin "http:\/\/localhost:3000"\n\1\2/g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's/^([ \t]*)(<\/VirtualHost>)/\1\tHeader set Access-Control-Allow-Credentials "true"\n\1\2/g' /etc/apache2/sites-available/*.conf
EXPOSE 80
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ADD entrypoint-php.sh /entrypoint-php.sh
RUN chmod +x /entrypoint-php.sh
RUN groupadd -f informatica -g$GID
RUN adduser --disabled-password --uid $UID --gid $GID --gecos "" informatica || true
CMD ["/entrypoint-php.sh"]
# ... dopo l'installazione di composer ...

# 1. Copia tutto il contenuto della tua cartella locale nel container
COPY . /var/www/html/

# 2. Installa le dipendenze di Slim (vendor) durante la build
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 3. Assicurati che i permessi siano corretti per Apache
RUN chown -R www-data:www-data /var/www/html

# ... poi prosegui con ADD entrypoint-php.sh ...

