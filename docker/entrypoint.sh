#!/bin/sh
set -e

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ ! -L public/storage ]; then
  php artisan storage:link || true
fi

if [ "${GENERATE_APP_KEY:-false}" = "true" ] && [ -z "${APP_KEY:-}" ]; then
  php artisan key:generate --force
fi

if [ "${WAIT_FOR_DB:-true}" = "true" ] && [ "${DB_CONNECTION:-mysql}" = "mysql" ]; then
  echo "Waiting for MySQL..."
  ATTEMPTS=0
  MAX_ATTEMPTS="${DB_MAX_TRIES:-30}"

  until php -r '
    try {
      $dsn = "mysql:host=" . getenv("DB_HOST") . ";port=" . getenv("DB_PORT") . ";dbname=" . getenv("DB_DATABASE");
      new PDO($dsn, getenv("DB_USERNAME"), getenv("DB_PASSWORD"), [PDO::ATTR_TIMEOUT => 2]);
      exit(0);
    } catch (Throwable $e) {
      exit(1);
    }'; do
    ATTEMPTS=$((ATTEMPTS + 1))
    if [ "$ATTEMPTS" -ge "$MAX_ATTEMPTS" ]; then
      echo "MySQL is not reachable after ${MAX_ATTEMPTS} attempts."
      exit 1
    fi
    sleep 2
  done
fi

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  php artisan migrate --force
fi

php artisan package:discover --ansi || true
php artisan optimize:clear

exec "$@"
