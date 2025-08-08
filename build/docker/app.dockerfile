FROM node:20 AS node-build

# Set working directory
WORKDIR /app

# Copy only frontend package files
COPY ./html/package*.json ./

# Install dependencies
RUN npm install

# Copy the rest of the frontend files
COPY ./html .

# Build frontend assets using Vite
RUN npm run build


# ----------------------------------------------------
# Stage 2: PHP build stage (Laravel app)
# ----------------------------------------------------
FROM php:8.4-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Copy custom php ini
COPY ./build/etc/php/conf.d/custom-config.ini /usr/local/etc/php/conf.d/custom-config.ini

# Install Composer (copy from official image)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www/html

# Copy Laravel backend files
COPY ./html .

# Copy compiled frontend assets from node build
COPY --from=node-build /app/public/build ./public/build

# Set correct permissions
RUN chown -R $user:$user /var/www/html

# Switch to non-root user
USER $user

# Install PHP dependencies with Composer
RUN composer install --no-dev --optimize-autoloader --prefer-dist