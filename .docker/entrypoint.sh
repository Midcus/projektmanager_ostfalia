#!/bin/bash

# Load environment variables from .env file, ignoring commented lines
if [ -f /var/www/html/.env ]; then
    export $(grep -v '^#' /var/www/html/.env | xargs)
fi

# Install Composer dependencies without dev packages
echo "Installing Composer dependencies..."
composer install --no-dev || exit 1

# Wait for MySQL to be ready
echo "Waiting for MySQL..."
while ! mysqladmin ping -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
    sleep 1
done
echo "MySQL is ready!"

# Set ownership and permissions for storage directory
echo "Fixing permissions for storage directory..."
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

# Set setgid bit for logs directory to ensure new files inherit www-data group
echo "Setting setgid for logs..."
chmod g+s /var/www/html/storage/logs

# Fix ownership and permissions for existing log files
echo "Fixing log files..."
find /var/www/html/storage/logs -type f -exec chown www-data:www-data {} \; -exec chmod 664 {} \;

# Create symbolic link for storage
echo "Creating storage link..."
rm -rf /var/www/html/public/storage
php /var/www/html/artisan storage:link || exit 1

# Generate Swagger API documentation
echo "Generating Swagger..."
php /var/www/html/artisan l5-swagger:generate || exit 1

# Start cron service and Apache server
echo "Starting cron and Apache..."
cron && apache2-foreground