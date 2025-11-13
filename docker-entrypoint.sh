#!/bin/bash
set -e

echo "🚀 Starting Laravel application..."

# Generate APP_KEY if not exists
if [ -z "$APP_KEY" ]; then
  echo "⚠️  APP_KEY not set, generating..."
  php artisan key:generate --force --show
fi

# Wait for database to be ready (with timeout)
echo "⏳ Waiting for database connection..."
MAX_TRIES=30
COUNT=0
until php artisan db:show 2>/dev/null || [ $COUNT -eq $MAX_TRIES ]; do
  echo "Database not ready yet, waiting... ($COUNT/$MAX_TRIES)"
  sleep 2
  COUNT=$((COUNT+1))
done

if [ $COUNT -eq $MAX_TRIES ]; then
  echo "❌ Database connection timeout! Check your DB credentials."
  echo "DB_HOST: $DB_HOST"
  echo "DB_PORT: $DB_PORT"
  echo "DB_DATABASE: $DB_DATABASE"
  exit 1
fi

echo "✅ Database connected!"

# Run migrations
echo "🔄 Running database migrations..."
php artisan migrate --force || {
  echo "⚠️  Migration failed, but continuing..."
}

# Clear all caches first
echo "🧹 Clearing caches..."
php artisan cache:clear || true
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Cache optimization
echo "⚡ Optimizing Laravel..."
php artisan config:cache || {
  echo "⚠️  Config cache failed, skipping..."
}
php artisan route:cache || {
  echo "⚠️  Route cache failed, skipping..."
}
php artisan view:cache || {
  echo "⚠️  View cache failed, skipping..."
}

# Set storage permissions at runtime
echo "🔐 Setting storage permissions..."
chmod -R 777 storage bootstrap/cache || true

echo "✨ Application ready!"
echo "Environment: $APP_ENV"
echo "Debug mode: $APP_DEBUG"

# Start Laravel server
exec php artisan serve --host=0.0.0.0 --port=8000
