FROM php:8.2-apache

# Mod Rewrite do Apache
RUN a2enmod rewrite

# Dependências do sistema e extensões do PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd intl pdo pdo_mysql mysqli

# Configura o VirtualHost para apontar a DocumentRoot para /var/www/html/public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# Habilita AllowOverride All para o .htaccess funcionar
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos do projeto
COPY . .

# Instala as dependências do PHP com suporte ao autoloader otimizado
RUN composer install --no-dev --optimize-autoloader

# Ajusta permissões das pastas graváveis do CodeIgniter
RUN chown -R www-data:www-data /var/www/html/writable /var/www/html/public

EXPOSE 80
RUN echo "=== VERIFICANDO DIRETORIO DE SECRETS ==="

CMD ["apache2-foreground"]
