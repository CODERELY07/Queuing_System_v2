#!/bin/sh

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations automatically on startup
php artisan migrate --force

# Start Supervisor (Nginx + PHP-FPM + Reverb)
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
