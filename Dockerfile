# Use an official PHP image as the base image
FROM php:8.2-apache

# Install required PHP extensions and dependencies
RUN apt-get update && apt-get install -y \
    libmariadb-dev-compat \
    libmariadb-dev \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-enable pdo_mysql

# Enable apache mod_rewrite (required for Laravel)
RUN a2enmod rewrite

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip

# Set the working directory
WORKDIR /var/www/html

# Copy the Laravel project files into the container
COPY . .

# Install composer (if not already installed)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install the PHP dependencies using Composer
RUN composer install

# Expose port 80 for the web server
EXPOSE 80
