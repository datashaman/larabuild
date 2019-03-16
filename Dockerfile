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
        vim \
    && (curl -sS https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add - > /dev/null) \
    && (curl -sS https://deb.nodesource.com/gpgkey/nodesource.gpg.key | apt-key add - > /dev/null) \
    && (curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - > /dev/null) \
    && (echo deb https://deb.nodesource.com/node_11.x bionic main > /etc/apt/sources.list.d/nodesource.list) \
    && (echo deb https://dl.yarnpkg.com/debian/ stable main > /etc/apt/sources.list.d/yarn.list) \
    && apt-get update -y \
    && apt-get install -yq --no-install-recommends \
        mysql-client \
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
        yarn \
    && apt-get autoremove -y \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/*

COPY templates/php-fpm.conf /etc/php/7.2/fpm/php-fpm.conf
RUN sed -i "s#%%BUILD_USER%%#${BUILD_USER}#g" /etc/php/7.2/fpm/php-fpm.conf

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install bower brunch gulp-cli
RUN npm install bower brunch gulp-cli -g

RUN mkdir /workspace && chown ${BUILD_USER} /workspace
WORKDIR /workspace
USER ${BUILD_USER}

# Install prestissimo composer package
RUN composer global require hirak/prestissimo

ENV HOME=/home/${BUILD_USER}
ENV PATH=${PATH}:${HOME}/.config/composer/vendor/bin:${HOME}/.local/bin:vendor/bin:node_modules/.bin

USER root
COPY . /workspace
RUN chown -R ${BUILD_USER} /workspace

USER ${BUILD_USER}

RUN composer install --no-dev \
    && rm -rf $HOME/.cache/composer/files $HOME/.composer/cache/files

RUN php artisan optimize
RUN php artisan config:cache
RUN php artisan route:cache

RUN yarn install \
    && yarn run production \
    && rm -rf node_modules \
    && yarn cache clean

RUN composer global remove hirak/prestissimo

USER root

RUN npm uninstall bower brunch gulp-cli -g

RUN rm /usr/local/bin/composer

RUN apt-get purge -y \
   git \
   nodejs \
   yarn

USER ${BUILD_USER}
EXPOSE 9000

CMD ["/usr/sbin/php-fpm7.2"]
