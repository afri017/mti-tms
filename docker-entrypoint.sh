#!/bin/bash
set -e

echo "🚀 Starting Laravel application..."

# Create .env file from environment variables
echo "📝 Creating .env file from environment variables..."
cat > .env <<EOF
APP_NAME="${APP_NAME:-Laravel}"
APP_ENV="${APP_ENV:-production}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

LOG_CHANNEL="${LOG_CHANNEL:-stack}"
LOG_LEVEL="${LOG_LEVEL:-error}"

DB_CONNECTION="${DB_CONNECTION:-mysql}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-laravel}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD}"

SESSION_DRIVER="${SESSION_DRIVER:-database}"
SESSION_LIFETIME="${SESSION_LIFETIME:-120}"
CACHE_STORE="${CACHE_STORE:-database}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-database}"

ASSET_URL="${ASSET_URL}"
EOF

# Generate APP_KEY if not exists
if [ -z "$APP_KEY" ]; then
  echo "⚠️  APP_KEY not set, generating..."
  php artisan key:generate --force
  export APP_KEY=$(grep ^APP_KEY= .env | cut -d '=' -f2)
  echo "✅ APP_KEY generated: $APP_KEY"
else
  echo "✅ APP_KEY already set, adding to .env..."
  sed -i "1iAPP_KEY=${APP_KEY}" .env
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
