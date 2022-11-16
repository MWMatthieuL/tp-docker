FROM debian:11
#Version of debian

#Create variable with
ENV LANG="en_US.UTF-8" \
    LC_ALL="en_US.UTF-8" \
    LANGUAGE="en_US.UTF-8" \
    TERM="xterm" \
    DEBIAN_FRONTEND="noninteractive" \
    PHP_VERSION=8.1

#Working directory
WORKDIR /app

#Docker run of different apt
RUN apt-get update -q && \
    apt-get install -qy software-properties-common locales locales-all && \
    export LC_ALL=en_US.UTF-8 && \
    export LANG=en_US.UTF-8 && \
    apt-get update -q && \
    apt-get install --no-install-recommends -qy \
    wget \
    gnupg \
    build-essential \
    apt-transport-https \
    cron \
    curl \
    nano \
    nginx \
    git \
    supervisor \
    unzip \
    openssh-client && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# PHP
RUN wget -q https://packages.sury.org/php/apt.gpg -O- | apt-key add - && \
    echo "deb https://packages.sury.org/php/ bullseye main" | tee /etc/apt/sources.list.d/php.list && \
    apt-get update -q && \
    apt-get install -qy \
    php-pear \
    php${PHP_VERSION} \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-dom \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-intl && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
# Node & yarn
COPY --from=node:16.14.0-stretch-slim /usr/local/bin/node /usr/local/bin/node
COPY --from=node:16.14.0-stretch-slim /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node:16.14.0-stretch-slim /opt/ /opt
RUN yarn_dir=$(find /opt -type f -name yarn) && ln -s $yarn_dir /usr/local/bin/yarn
RUN yarn config set python /usr/bin/python3

# Blackfire
COPY --from=blackfire/blackfire:2 /usr/local/bin/blackfire /usr/local/bin/blackfire

# Download and install sonar qube scanner
RUN wget https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-3.2.0.1227-linux.zip
RUN unzip sonar-scanner-cli-3.2.0.1227-linux.zip -d /opt/sonar && \
    rm -rf sonar-scanner-cli-3.2.0.1227-linux.zip && \
    mv /opt/sonar/sonar-scanner-3.2.0.1227-linux/* /opt/sonar && \
    rm -rf /opt/sonar/sonar-scanner-3.2.0.1227-linux && \
    ln -s /opt/sonar/bin/sonar-scanner /usr/local/bin/sonar-scanner && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Mysql utils
RUN apt-get update && \
    apt-get install -qy default-mysql-client && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install phpcsfixer
RUN wget https://cs.symfony.com/download/php-cs-fixer-v3.phar -O /tmp/php-cs-fixer && \
    chmod a+x /tmp/php-cs-fixer && \
    mv /tmp/php-cs-fixer /usr/local/bin/php-cs-fixer && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Configuration files
COPY docker/dev/app/php.ini /etc/php/${PHP_VERSION}/cli/conf.d/50-setting.ini
COPY docker/dev/app/php.ini /etc/php/${PHP_VERSION}/fpm/conf.d/50-setting.ini
COPY docker/dev/app/pool.conf /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf
COPY docker/dev/app/nginx.conf /etc/nginx/nginx.conf
COPY docker/dev/app/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/dev/app/symfony.conf /etc/nginx/symfony.conf

# SSL Certificates
COPY docker/dev/app/mw.local.crt /etc/nginx/ssl/mw.local.crt
COPY docker/dev/app/mw.local.key /etc/nginx/ssl/mw.local.key
COPY docker/dev/app/dhparam.pem /etc/nginx/ssl/dhparam.pem

RUN mkdir -p /app/var/log /app/var/cache /run/php

# Disable host checking
RUN echo "StrictHostKeyChecking no" >> /etc/ssh/ssh_config

#Command
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
