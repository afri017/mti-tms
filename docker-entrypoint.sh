#!/bin/bash
set -e

echo "🚀 Starting Laravel application..."

# Wait for database to be ready
echo "⏳ Waiting for database connection..."
until php artisan db:show 2>/dev/null; do
  echo "Database not ready yet, waiting..."
  sleep 2
done

echo "✅ Database connected!"

# Run migrations
echo "🔄 Running database migrations..."
php artisan migrate --force

# Cache optimization
echo "⚡ Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✨ Application ready!"

# Start Laravel server
exec php artisan serve --host=0.0.0.0 --port=8000
