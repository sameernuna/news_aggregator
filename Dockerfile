FROM ubuntu:24.04

# Set working directory
WORKDIR /home/sameer/public_html/

# Set non-interactive mode for apt
ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies and PHP 8.3 from Ondřej PPA
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      software-properties-common \
      curl \
      gnupg && \
    add-apt-repository ppa:ondrej/php && \
    apt-get update && \
    apt-get dist-upgrade -y && \
    apt-get install -y --no-install-recommends \
      apache2 \
      php8.3 \
      php8.3-cli \
      libapache2-mod-php8.3 \
      php8.3-gd \
      php8.3-ldap \
      php8.3-mbstring \
      php8.3-mysql \
      php8.3-pgsql \
      php8.3-sqlite3 \
      php8.3-xml \
      php8.3-xsl \
      php8.3-zip \
      php8.3-soap \
      php8.3-curl \
      php-pear \
      php8.3-dev \
      gcc \
      make \
      vim \
      openssh-server \
      supervisor && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# (Optional) Install APCu for Prometheus metrics (uncomment if needed)
# RUN pecl install apcu && \
#     echo "extension=apcu.so" > /etc/php/8.3/mods-available/apcu.ini && \
#     ln -s /etc/php/8.3/mods-available/apcu.ini /etc/php/8.3/cli/conf.d/20-apcu.ini && \
#     ln -s /etc/php/8.3/mods-available/apcu.ini /etc/php/8.3/apache2/conf.d/20-apcu.ini

# Enable Apache modules
RUN a2enmod rewrite

# Create necessary directories
RUN mkdir -p /var/run/apache2 /var/run/sshd /var/log/supervisor

# Copy Apache and Supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

# Expose HTTP and SSH ports
EXPOSE 80 22

# ENTRYPOINT is now handled in docker-compose.yml
# Leave CMD as fallback (optional)
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

