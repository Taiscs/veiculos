# Usa a imagem oficial do PHP 8.2 com Apache
FROM php:8.2-apache

# Instala dependências do sistema e extensões do PHP necessárias para o CodeIgniter
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo pdo_mysql mysqli zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copia o Composer oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilita o módulo mod_rewrite do Apache
RUN a2enmod rewrite

# HABILITA O .HTACCESS (AllowOverride All)
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Aponta o DocumentRoot do Apache para a pasta /public do CodeIgniter
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html
COPY . /var/www/html

# Instala as dependências do Composer e gera a pasta vendor
RUN composer install --no-dev --optimize-autoloader

# Ajusta permissões das pastas de escrita do CodeIgniter
RUN chown -R www-data:www-data /var/www/html/writable /var/www/html/public

EXPOSE 80
