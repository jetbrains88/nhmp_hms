#!/bin/sh
set -e

# 1. Ensure internal Laravel directories exist
# Using -p ensures no error if they already exist
mkdir -p /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/bootstrap/cache

# 2. FIX PERMISSIONS (Targeted & Fast)
# We only touch storage and bootstrap/cache to keep boot times low
echo "Fixing permissions for storage and bootstrap..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 3. CRITICAL CHECK: Ensure autoloader exists
# If vendor is missing, the container will stay in a crash loop.
if [ ! -f "/var/www/vendor/autoload.php" ]; then
    echo "Vendor folder missing. Running composer install..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# 4. Wait for MySQL (Reliable TCP check)
echo "Waiting for MySQL at ${DB_HOST:-db}:3306..."
while ! nc -z ${DB_HOST:-db} 3306; do
  echo "MySQL is still warming up... sleeping 2s"
  sleep 2
done
echo "MySQL is up and running!"

# 5. Optimizing Laravel
# In development, it's often better to CLEAR caches so changes reflect immediately.
# In production, use 'cache' instead of 'clear'.
echo "Cleaning up Laravel optimization..."
php artisan config:clear
php artisan cache:clear || true

# 6. Run migrations (Uncomment when ready for auto-migrations)
# echo "Running migrations..."
# php artisan migrate --force

# Hand off to the main Docker command (php-fpm)
echo "Starting PHP-FPM..."
exec "$@"