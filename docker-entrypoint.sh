#!/bin/bash

# Fix Laravel permissions
chown -R www-data:www-data /home/sameer/public_html/news-aggregator/storage
chown -R www-data:www-data /home/sameer/public_html/news-aggregator/bootstrap/cache
chmod -R 775 /home/sameer/public_html/news-aggregator/storage
chmod -R 775 /home/sameer/public_html/news-aggregator/bootstrap/cache

# Start Supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

