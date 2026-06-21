#!/bin/bash
# deploy.sh

# Pull latest code
git pull origin main

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Frontend dependencies and build assets
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Restart queues (if you are using them)
php artisan queue:restart