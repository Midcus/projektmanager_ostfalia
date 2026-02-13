#!/bin/bash
set -e

ENV_FILE="/var/www/html/.env"

# Load only DB vars needed for mysqladmin ping (do NOT export APP_KEY early)
if [ -f "$ENV_FILE" ]; then
  export $(grep -E '^(DB_HOST|DB_PORT|DB_USERNAME|DB_PASSWORD)=' "$ENV_FILE" | xargs || true)
fi

echo "Checking APP_KEY..."
if [ -f "$ENV_FILE" ] && ! grep -q "^APP_KEY=base64:" "$ENV_FILE"; then
  echo "Generating APP_KEY..."
  php /var/www/html/artisan key:generate --force
else
  echo "APP_KEY already set."
fi

# IMPORTANT: ensure APP_KEY in environment is not empty / not overriding file
unset APP_KEY
if [ -f "$ENV_FILE" ]; then
  export APP_KEY="$(grep -E '^APP_KEY=' "$ENV_FILE" | head -n1 | cut -d= -f2-)"
fi

# Clear caches just in case
php /var/www/html/artisan config:clear || true
php /var/www/html/artisan cache:clear || true

echo "Installing Composer dependencies..."
composer install --no-dev || exit 1

echo "Waiting for MySQL..."
while ! mysqladmin ping -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" --ssl=0 --silent; do
  sleep 1
done
echo "MySQL is ready!"

echo "Fixing permissions for storage directory..."
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage
chmod g+s /var/www/html/storage/logs
find /var/www/html/storage/logs -type f -exec chown www-data:www-data {} \; -exec chmod 664 {} \;

echo "Creating storage link..."
rm -rf /var/www/html/public/storage
php /var/www/html/artisan storage:link

echo "Generating Swagger..."
php /var/www/html/artisan l5-swagger:generate

echo "Starting cron and Apache..."
cron && apache2-foreground
