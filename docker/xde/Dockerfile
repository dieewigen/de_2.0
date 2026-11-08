FROM php:8.4-apache

# Install system dependencies, MySQL extensions, and cron
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    cron \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mysqli

# Enable Apache modules
RUN a2enmod rewrite

# Copy cron configuration
COPY crontab /etc/cron.d/php-cron
RUN chmod 0644 /etc/cron.d/php-cron \
    && crontab /etc/cron.d/php-cron \
    && touch /var/log/cron_reg.log \
    && touch /var/log/cron_kt_wt.log

# Copy and set entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]