# Utiliser une image PHP avec Apache
FROM php:8.2-apache

# Installer MySQL et ses extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copier les fichiers du projet
COPY . /var/www/html/

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/html

# Exposer le port 80
EXPOSE 80

# Démarrer Apache
CMD ["apache2-foreground"]
