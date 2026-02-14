#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/testing
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwx storage bootstrap/cache || true

if [ "${DB_CONNECTION}" = "mysql" ]; then
  echo "Aguardando MySQL..."
  i=0
  until php -r '
$host = getenv("DB_HOST") ?: "mysql";
$port = getenv("DB_PORT") ?: "3306";
$db = getenv("DB_DATABASE") ?: "simulados";
$user = getenv("DB_USERNAME") ?: "simulados";
$pass = getenv("DB_PASSWORD") ?: "simulados";
try {
    new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass, [PDO::ATTR_TIMEOUT => 3]);
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}
'; do
    i=$((i + 1))
    if [ "$i" -ge 60 ]; then
      echo "MySQL nao ficou disponivel a tempo."
      exit 1
    fi
    sleep 2
  done
  echo "MySQL pronto."
fi

if [ "${RUN_MIGRATIONS}" = "true" ] && [ -f artisan ]; then
  php artisan optimize:clear || true
  php artisan migrate --force
fi

apache2ctl -t
exec apache2-foreground
