FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive \
    TZ=Etc/UTC

# 

RUN apt-get update \
    && apt install software-properties-common -y \
    && add-apt-repository ppa:ondrej/php \
    && apt-get update \
    && apt-get install -y tzdata apache2 libapache2-mod-php php8.3 \
    && echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite \ 
    && /etc/init.d/apache2 restart


COPY misc/apache/httpd.conf /etc/apache2/apache2.conf

# COPY backend/ /var/www/html/
WORKDIR /var/www/html/
EXPOSE 80
CMD ["apachectl", "-D", "FOREGROUND"]
