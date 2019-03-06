#
# Built with https://github.com/datashaman/docker-laravel
#
# Packages: beanstalkd elasticsearch google-chrome mailhog mariadb memcached minio redis php7.2 node11
#

FROM ubuntu:18.04

ARG BUILD_MIRROR="http://za.archive.ubuntu.com"
ARG BUILD_USER="webapp"

ENV DEBIAN_FRONTEND=noninteractive

RUN useradd --shell /bin/bash -u 1000 -o -c "" -m ${BUILD_USER}

RUN [ -n "${BUILD_MIRROR}" ] && sed -i "s#http://za.archive.ubuntu.com#${BUILD_MIRROR}#g" /etc/apt/sources.list

RUN apt-get update -y \
    && apt-get install -yq --no-install-recommends \
        acl \
        apt-transport-https \
        awscli \
        ca-certificates \
        curl \
        git \
        gnupg \
        iputils-ping \
        less \
        lsof \
        make \
        netcat \
        net-tools \
        procps \
        rsync \
        sqlite3 \
        telnet \
        tmux \
        unzip \
        vim

# Add apt keys
RUN curl -sS https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add - > /dev/null
RUN curl -sS https://deb.nodesource.com/gpgkey/nodesource.gpg.key | apt-key add - > /dev/null
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - > /dev/null

# Add apt sources
RUN echo deb https://deb.nodesource.com/node_11.x bionic main > /etc/apt/sources.list.d/nodesource.list
RUN echo deb https://dl.yarnpkg.com/debian/ stable main > /etc/apt/sources.list.d/yarn.list
RUN echo deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main > /etc/apt/sources.list.d/google-chrome.list

# Update apt repositories
RUN apt-get update -y

# Install apt packages
RUN apt-get install -yq --no-install-recommends \
   google-chrome-stable \
   mariadb-client \
   nodejs \
   php7.2-bcmath \
   php7.2-curl \
   php7.2-fpm \
   php7.2-gd \
   php7.2-imagick \
   php7.2-intl \
   php7.2-mbstring \
   php7.2-mysql \
   php7.2-sqlite3 \
   php7.2-xml \
   php7.2-zip \
   php-memcached \
   redis-tools \
   xvfb \
   yarn

COPY templates/php-fpm.conf /etc/php/7.2/fpm/php-fpm.conf
RUN sed -i "s#%%BUILD_USER%%#${BUILD_USER}#g" /etc/php/7.2/fpm/php-fpm.conf

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install bower brunch gulp-cli
RUN npm install bower brunch gulp-cli -g

RUN apt-get autoremove -y \
   && apt-get clean -y \
   && rm -rf /var/lib/apt/lists/*

RUN mkdir /workspace && chown ${BUILD_USER} /workspace
WORKDIR /workspace
USER ${BUILD_USER}

# Install prestissimo composer package
RUN composer global require hirak/prestissimo

EXPOSE 9000

ENV HOME=/home/${BUILD_USER}
ENV PATH=${PATH}:${HOME}/.config/composer/vendor/bin:${HOME}/.local/bin:vendor/bin:node_modules/.bin

CMD ["/usr/sbin/php-fpm7.2"]
