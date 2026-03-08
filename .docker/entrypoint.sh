#!/bin/bash
set -e

APP_DIR="/var/www/html"
ENV_FILE="$APP_DIR/.env"

cd "$APP_DIR"

# Load only DB vars (for mysqladmin ping)
if [ -f "$ENV_FILE" ]; then
  export $(grep -E '^(DB_HOST|DB_PORT|DB_USERNAME|DB_PASSWORD)=' "$ENV_FILE" | xargs || true)
fi

# 1) Ensure APP_KEY exists (generate once)
echo "Checking APP_KEY..."
if [ -f "$ENV_FILE" ] && ! grep -q "^APP_KEY=base64:" "$ENV_FILE"; then
  echo "Generating APP_KEY..."
  php artisan key:generate --force
else
  echo "APP_KEY already set."
fi

# Ensure APP_KEY not overridden by empty env
unset APP_KEY
if [ -f "$ENV_FILE" ]; then
  export APP_KEY="$(grep -E '^APP_KEY=' "$ENV_FILE" | head -n1 | cut -d= -f2-)"
fi

# 2) Install composer deps ONLY if vendor missing
if [ ! -f "$APP_DIR/vendor/autoload.php" ]; then
  echo "vendor/ missing → Installing Composer dependencies..."
  composer install --no-dev --optimize-autoloader --no-interaction
else
  echo "vendor/ exists → Skipping composer install."
fi

# 3) Wait for MySQL
echo "Waiting for MySQL..."
: "${DB_HOST:=db}"
: "${DB_PORT:=3306}"

while ! mysqladmin ping -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" --ssl=0 --silent; do
  sleep 1
done
echo "MySQL is ready!"

# 4) Permissions (safe to run each time)
echo "Fixing permissions..."
chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" || true
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" || true

# 5) storage:link ONLY if missing
if [ ! -L "$APP_DIR/public/storage" ]; then
  echo "Creating storage link..."
  php artisan storage:link || true
else
  echo "Storage link exists → Skipping."
fi

# 6) Clear caches (OK for dev/showcase; keep as tolerant)
php artisan config:clear || true
php artisan cache:clear || true

# 7) Swagger generate: ONLY if you explicitly enable it
# (recommended: default off for showcase to speed up boot)
if [ "${GENERATE_SWAGGER:-false}" = "true" ]; then
  echo "Generating Swagger..."
  php artisan l5-swagger:generate || true
else
  echo "Swagger generation disabled → Skipping."
fi

echo "Starting cron and Apache..."
cron
exec apache2-foreground
