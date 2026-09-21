# Usa a imagem oficial do PHP 8.2 com Apache
FROM php:8.2-apache

# Instala dependências do sistema e extensões necessárias para o CodeIgniter 4 e MySQL
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo pdo_mysql mysqli zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilita o módulo mod_rewrite do Apache para as rotas do CodeIgniter 4
RUN a2enmod rewrite

# Ajusta o DocumentRoot do Apache para apontar para a pasta /public do CodeIgniter
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copia os arquivos do projeto para o contêiner
WORKDIR /var/www/html
COPY . /var/www/html

# Define as permissões necessárias para as pastas de escrita do CodeIgniter
RUN chown -R www-data:www-data /var/www/html/writable /var/www/html/public

# Expõe a porta padrão de execução
EXPOSE 80
