FROM php:8.2-apache

# Habilitar el módulo de reescritura de Apache para las rutas de Laravel
RUN a2enmod rewrite

# Instalar dependencias esenciales del sistema para SQLite, Compresión y Git
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libsqlite3-dev \
    git \
    nano \
    && docker-php-ext-install pdo pdo_sqlite zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configurar el directorio raíz de Apache para apuntar a la carpeta /public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Descargar e Instalar Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir el directorio de trabajo
WORKDIR /var/www/html

# Copiar todos los archivos del proyecto al contenedor
COPY . /var/www/html

# Instalar las librerías de PHP desde composer.lock optimizadas
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Asegurar que los permisos son los correctos para que Apache pueda guardar logs y leer la base de datos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
