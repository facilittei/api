FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    build-essential \
    libwebp-dev \
    libpng-dev \
    libjpeg-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    nano \
    unzip \
    git \
    curl \
    libzip-dev
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_mysql zip exif pcntl bcmath
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd

RUN pecl install redis && docker-php-ext-enable redis
RUN pecl install apcu && docker-php-ext-enable apcu

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
COPY ./infra/php/local.ini /usr/local/etc/php/conf.d/local.ini

RUN rm -rf vendor || true \
    && mkdir vendor

COPY src/composer.json .

RUN composer install --no-dev

COPY src .

RUN cp .env.example .env \
    && php artisan key:generate

ARG APP_ENV
ENV APP_ENV=$APP_ENV

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www
COPY --chown=www:www src /var/www/html
USER www

ARG VERSION
ARG APP_DEBUG
ARG APP_URL
ARG APP_CLIENT_URL
ARG APP_WEBSITE_URL
ARG APP_ASSETS_URL
ARG DB_CONNECTION
ARG DB_HOST
ARG DB_PORT
ARG DB_DATABASE
ARG DB_USERNAME
ARG DB_PASSWORD
ARG CACHE_DRIVER
ARG QUEUE_CONNECTION
ARG QUEUE_DRIVER
ARG SQS_PREFIX
ARG SQS_QUEUE
ARG REDIS_CLIENT
ARG REDIS_HOST
ARG REDIS_PORT
ARG MAIL_MAILER
ARG MAIL_HOST
ARG MAIL_PORT
ARG MAIL_USERNAME
ARG MAIL_PASSWORD
ARG MAIL_ENCRYPTION
ARG MAIL_FROM_ADDRESS
ARG MAIL_FROM_NAME
ARG AWS_ACCESS_KEY_ID
ARG AWS_SECRET_ACCESS_KEY
ARG AWS_DEFAULT_REGION
ARG AWS_BUCKET
ARG LOG_LEVEL
ARG LOG_SLACK_WEBHOOK_URL
ARG JUNO_API_URL
ARG JUNO_API_AUTHORIZATION_BASIC
ARG JUNO_API_VERSION
ARG JUNO_API_RESOURCE_TOKEN
ARG PROMETHEUS_METRICS_ROUTE_ENABLED
ARG PROMETHEUS_METRICS_ROUTE_PATH
ARG PROMETHEUS_METRICS_ROUTE_MIDDLEWARE
ARG PROMETHEUS_COLLECT_FULL_SQL_QUERY
ARG PROMETHEUS_STORAGE_ADAPTER
ARG PROMETHEUS_REDIS_HOST
ARG PROMETHEUS_REDIS_PORT
ARG PROMETHEUS_REDIS_TIMEOUT
ARG PROMETHEUS_REDIS_READ_TIMEOUT
ARG PROMETHEUS_REDIS_PERSISTENT_CONNECTIONS
ARG PROMETHEUS_REDIS_PREFIX
ARG PAPERTRAIL_URL
ARG PAPERTRAIL_PORT
ARG SENTRY_LARAVEL_DSN
ARG STRIPE_KEY
ARG STRIPE_SECRET

ENV VERSION=$VERSION
ENV APP_DEBUG=$APP_DEBUG
ENV APP_URL=$APP_URL
ENV APP_CLIENT_URL=$APP_CLIENT_URL
ENV APP_WEBSITE_URL=$APP_WEBSITE_URL
ENV APP_ASSETS_URL=$APP_ASSETS_URL
ENV DB_CONNECTION=$DB_CONNECTION
ENV DB_HOST=$DB_HOST
ENV DB_PORT=$DB_PORT
ENV DB_DATABASE=$DB_DATABASE
ENV DB_USERNAME=$DB_USERNAME
ENV DB_PASSWORD=$DB_PASSWORD
ENV CACHE_DRIVER=$CACHE_DRIVER
ENV QUEUE_CONNECTION=$QUEUE_CONNECTION
ENV QUEUE_DRIVER=$QUEUE_DRIVER
ENV SQS_PREFIX=$SQS_PREFIX
ENV SQS_QUEUE=$SQS_QUEUE
ENV REDIS_CLIENT=$REDIS_CLIENT
ENV REDIS_HOST=$REDIS_HOST
ENV REDIS_PORT=$REDIS_PORT
ENV MAIL_MAILER=$MAIL_MAILER
ENV MAIL_HOST=$MAIL_HOST
ENV MAIL_PORT=$MAIL_PORT
ENV MAIL_USERNAME=$MAIL_USERNAME
ENV MAIL_PASSWORD=$MAIL_PASSWORD
ENV MAIL_ENCRYPTION=$MAIL_ENCRYPTION
ENV MAIL_FROM_ADDRESS=$MAIL_FROM_ADDRESS
ENV MAIL_FROM_NAME=$MAIL_FROM_NAME
ENV AWS_ACCESS_KEY_ID=$AWS_ACCESS_KEY_ID
ENV AWS_SECRET_ACCESS_KEY=$AWS_SECRET_ACCESS_KEY
ENV AWS_DEFAULT_REGION=$AWS_DEFAULT_REGION
ENV AWS_BUCKET=$AWS_BUCKET
ENV LOG_LEVEL=$LOG_LEVEL
ENV LOG_SLACK_WEBHOOK_URL=$LOG_SLACK_WEBHOOK_URL
ENV JUNO_API_URL=$JUNO_API_URL
ENV JUNO_API_AUTHORIZATION_BASIC=$JUNO_API_AUTHORIZATION_BASIC
ENV JUNO_API_VERSION=$JUNO_API_VERSION
ENV JUNO_API_RESOURCE_TOKEN=$JUNO_API_RESOURCE_TOKEN
ENV PROMETHEUS_METRICS_ROUTE_ENABLED=$PROMETHEUS_METRICS_ROUTE_ENABLED
ENV PROMETHEUS_METRICS_ROUTE_PATH=$PROMETHEUS_METRICS_ROUTE_PATH
ENV PROMETHEUS_METRICS_ROUTE_MIDDLEWARE=$PROMETHEUS_METRICS_ROUTE_MIDDLEWARE
ENV PROMETHEUS_COLLECT_FULL_SQL_QUERY=$PROMETHEUS_COLLECT_FULL_SQL_QUERY
ENV PROMETHEUS_STORAGE_ADAPTER=$PROMETHEUS_STORAGE_ADAPTER
ENV PROMETHEUS_REDIS_HOST=$PROMETHEUS_REDIS_HOST
ENV PROMETHEUS_REDIS_PORT=$PROMETHEUS_REDIS_PORT
ENV PROMETHEUS_REDIS_TIMEOUT=$PROMETHEUS_REDIS_TIMEOUT
ENV PROMETHEUS_REDIS_READ_TIMEOUT=$PROMETHEUS_REDIS_READ_TIMEOUT
ENV PROMETHEUS_REDIS_PERSISTENT_CONNECTIONS=$PROMETHEUS_REDIS_PERSISTENT_CONNECTIONS
ENV PROMETHEUS_REDIS_PREFIX=$PROMETHEUS_REDIS_PREFIX
ENV PAPERTRAIL_URL=$PAPERTRAIL_URL
ENV PAPERTRAIL_PORT=$PAPERTRAIL_PORT
ENV SENTRY_LARAVEL_DSN=$SENTRY_LARAVEL_DSN
ENV STRIPE_KEY=$STRIPE_KEY
ENV STRIPE_SECRET=$STRIPE_SECRET

EXPOSE 9000
CMD ["php-fpm"]
