#!/bin/bash
set -e

echo "🚀 Starting Laravel application..."

# Generate APP_KEY if not exists
if [ -z "$APP_KEY" ]; then
  echo "⚠️  APP_KEY not set, generating..."
  php artisan key:generate --force --show
fi

# Wait a bit for database to be ready
echo "⏳ Waiting for database to be ready..."
sleep 5

# Try to connect to database
echo "🔍 Testing database connection..."
if php artisan db:show 2>/dev/null; then
  echo "✅ Database connected successfully!"
else
  echo "⚠️  Database check failed, but continuing anyway..."
  echo "DB_HOST: $DB_HOST"
  echo "DB_PORT: $DB_PORT"
  echo "DB_DATABASE: $DB_DATABASE"
fi

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
