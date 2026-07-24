FROM php:8.2-apache 
# APENAS ORTOGRÁFICO

# Instala e ativa a extensão cURL (necessária para chamar a API Node.js)
RUN apt-get update \
    && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-configure curl \
    && docker-php-ext-install curl \
    && rm -rf /var/lib/apt/lists/*

# Copia os arquivos do frontend para o servidor Apache
COPY . /var/www/html/

EXPOSE 80
