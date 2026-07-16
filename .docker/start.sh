#!/bin/sh

# Ensure storage symlink for public disk
cd /app && php artisan storage:link || true

# Run Laravel optimizations
cd /app && php artisan optimize && php artisan view:cache || true

# Set proper permissions
chmod -R 755 /app
chmod -R 777 /app/storage
chmod -R 777 /app/bootstrap/cache

# Start supervisord (manages FrankenPHP, queue workers, and scheduler)
/usr/bin/supervisord -n -c /etc/supervisord.conf
