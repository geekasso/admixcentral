#!/bin/bash

# 1. Create the Reverb Supervisor Config
echo "Configuring Reverb Supervisor..."
cat > /tmp/admix-reverb.conf <<EOF
[program:admix-reverb]
command=php /var/www/admixcentral/artisan reverb:start
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/admixcentral/storage/logs/reverb.log
EOF

# Move to correct location (requires sudo)
sudo mv /tmp/admix-reverb.conf /etc/supervisor/conf.d/admix-reverb.conf

# 2. Update and Start Supervisor
echo "Updating Supervisor..."
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start admix-reverb

# 3. Clear Caches
echo "Clearing Application Caches..."
cd /var/www/admixcentral
sudo php artisan view:clear
sudo php artisan config:clear
sudo php artisan route:clear

echo "----------------------------------------------------------------"
echo "✅ Reverb Server Started!"
echo "----------------------------------------------------------------"
echo "NOTE: Because we modified JavaScript (echo.js), please run:"
echo "      npm run build"
echo "      (or ensure 'npm run dev' is running)"
echo "----------------------------------------------------------------"
