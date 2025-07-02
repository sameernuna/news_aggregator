FROM php:8.3-apache

# Set working directory
WORKDIR /home/innoscripta/public_html/

# Set non-interactive mode for apt
ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    vim \
    gcc \
    make \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libldap2-dev \
    openssh-server \
    supervisor && \
    docker-php-ext-install \
      pdo \
      pdo_mysql \
      pdo_pgsql \
      mysqli \
      mbstring \
      gd \
      xml \
      zip \
      ldap \
      soap && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite module
RUN a2enmod rewrite

# Create necessary directories
RUN mkdir -p /var/run/apache2 /var/run/sshd /var/log/supervisor

# Copy Apache and Supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# Copy entrypoint script
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

# Expose ports
EXPOSE 80 22

# ENTRYPOINT handled in docker-compose.yml, fallback CMD:
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
