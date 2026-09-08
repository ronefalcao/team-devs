FROM php:8.4-cli-alpine

# Precisa bater com o `user:` do compose — o OpenSSH recusa rodar sob um uid que
# não tenha entrada no /etc/passwd ("No user exists for uid ..."), e sem ssh o
# painel não consegue dar push nos repos dos projetos.
ARG APP_UID=1000
ARG APP_GID=1000

# pdo_sqlite/sqlite3 já vêm habilitados na imagem oficial; aqui só o que falta.
# git/openssh: o painel precisa versionar os PRDs no repo de cada projeto.
RUN apk add --no-cache git openssh-client icu-libs libzip \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev \
    && docker-php-ext-install -j"$(nproc)" intl zip bcmath \
    && apk del .build-deps \
    && addgroup -g "${APP_GID}" app \
    && adduser -D -u "${APP_UID}" -G app -h /home/app app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_HOME=/home/app/.composer \
    COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 8000

ENTRYPOINT ["entrypoint"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
