FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive \
    TZ=Etc/UTC

RUN apt-get update \
    && apt-get install -y software-properties-common lsb-release ca-certificates apt-transport-https wget gnupg \
    && add-apt-repository ppa:ondrej/php -y \
    && apt-get update \
    && apt-get install -y \
        tzdata \
        apache2 \
        libapache2-mod-php8.3 \
        php8.3 \
        php8.3-cli \
        php8.3-common \
        php8.3-mysql \       
        poppler-utils \
    && echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && /etc/init.d/apache2 restart
    
COPY misc/apache/httpd.conf /etc/apache2/apache2.conf

COPY ../ /var/www/html/
WORKDIR /var/www/html/
EXPOSE 80
CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]