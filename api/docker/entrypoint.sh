#!/bin/sh
# Prepara o container da API: .env, dependências, banco e usuário do painel.
# Tudo aqui é idempotente — roda a cada `docker compose up` sem duplicar nada.
# POSIX sh: a imagem é Alpine, não tem bash.
set -eu

cd /var/www/html

# Grava KEY=VALUE no .env, substituindo a linha existente ou acrescentando no fim.
set_env() {
    key="$1"
    value="$2"
    if grep -qE "^${key}=" .env; then
        sed -i "s|^${key}=.*|${key}=${value}|" .env
    else
        printf '%s=%s\n' "$key" "$value" >> .env
    fi
}

if [ ! -f .env ]; then
    echo "==> criando .env a partir de .env.example"
    cp .env.example .env
fi

# O compose é a fonte da verdade da configuração de ambiente.
set_env DB_CONNECTION "${DB_CONNECTION:-sqlite}"
set_env DB_DATABASE "${DB_DATABASE:-/var/www/html/storage/app/team-devs.sqlite}"
set_env SESSION_DRIVER "${SESSION_DRIVER:-file}"
set_env CACHE_STORE "${CACHE_STORE:-file}"
set_env QUEUE_CONNECTION "${QUEUE_CONNECTION:-sync}"
set_env APP_URL "${APP_URL:-http://localhost:8000}"

# `if` e não `&&`: sob `set -e`, um `&&` com teste falso derruba o script.
if [ -n "${MCP_API_TOKEN:-}" ]; then
    set_env MCP_API_TOKEN "${MCP_API_TOKEN}"
fi

# Sobrou do setup com Postgres; sem isso o .env fica com lixo confuso.
sed -i '/^DB_HOST=/d; /^DB_PORT=/d; /^DB_USERNAME=/d; /^DB_PASSWORD=/d' .env

if [ ! -f vendor/autoload.php ]; then
    echo "==> composer install"
    composer install --no-interaction --prefer-dist
fi

if ! grep -qE '^APP_KEY=base64:' .env; then
    echo "==> gerando APP_KEY"
    php artisan key:generate --force
fi

# SQLite não cria o arquivo sozinho.
db_file="${DB_DATABASE:-/var/www/html/storage/app/team-devs.sqlite}"
if [ ! -f "$db_file" ]; then
    echo "==> criando banco em $db_file"
    mkdir -p "$(dirname "$db_file")"
    touch "$db_file"
fi

echo "==> migrate"
php artisan migrate --force

# Sem usuário não há como entrar no painel do Filament (o painel exige login).
if [ -n "${ADMIN_EMAIL:-}" ]; then
    echo "==> garantindo usuário do painel"
    php artisan db:seed --class=AdminUserSeeder --force
fi

php artisan config:clear >/dev/null

exec "$@"
