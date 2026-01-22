#!/usr/bin/env bash
set -e

### CONFIG ###
REPO_URL="https://github.com/geekasso/admixcentral.git"
APP_DIR="/var/www/admixcentral"
APP_USER="www-data"
PHP_FPM_SOCK="/run/php/php8.3-fpm.sock"
NGINX_SITE_NAME="admixcentral"
################

if [ "$EUID" -ne 0 ]; then
  echo "Run this script as root (use sudo)." >&2
  exit 1
fi

echo "[1/12] Stop/disable Apache if present..."
systemctl stop apache2 >/dev/null 2>&1 || true
systemctl disable apache2 >/dev/null 2>&1 || true

echo "[2/12] Apt update + base packages..."
apt update
apt install -y \
  php php-cli php-common php-fpm \
  php-mbstring php-xml php-curl php-zip \
  php-sqlite3 php-mysql php-pgsql \
  php-bcmath php-intl php-gd \
  unzip git curl sqlite3 \
  build-essential software-properties-common \
  nginx supervisor

echo "[3/12] Composer..."
if ! command -v composer >/dev/null 2>&1; then
  apt install -y composer
fi

echo "[4/12] Node.js 20.x..."
if ! command -v node >/dev/null 2>&1; then
  curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
  apt install -y nodejs
fi

echo "[5/12] Prepare /var/www and clone repo..."
mkdir -p /var/www
chown "$APP_USER":"$APP_USER" /var/www

if [ -d "$APP_DIR/.git" ]; then
  echo "Repo already exists in $APP_DIR, pulling latest..."
  cd "$APP_DIR"
  sudo -u "$APP_USER" git pull
else
  sudo -u "$APP_USER" git clone "$REPO_URL" "$APP_DIR"
  cd "$APP_DIR"
fi

echo "[6/12] .env, APP_NAME, SQLite config..."
cd "$APP_DIR"

# Ensure .env
if [ ! -f .env ]; then
  sudo -u "$APP_USER" cp .env.example .env
fi

# APP_NAME
if grep -q "^APP_NAME=" .env; then
  sed -i 's/^APP_NAME=.*/APP_NAME="AdmixCentral"/' .env
else
  echo 'APP_NAME="AdmixCentral"' >> .env
fi

# DB config -> SQLite
if grep -q "^DB_CONNECTION=" .env; then
  sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env
else
  echo "DB_CONNECTION=sqlite" >> .env
fi

if grep -q "^DB_DATABASE=" .env; then
  sed -i 's|^DB_DATABASE=.*|DB_DATABASE=database/database.sqlite|' .env
else
  echo "DB_DATABASE=database/database.sqlite" >> .env
fi

mkdir -p database
if [ ! -f database/database.sqlite ]; then
  sudo -u "$APP_USER" touch database/database.sqlite
fi

echo "[7/12] Composer install + migrations..."
sudo -u "$APP_USER" composer install --no-interaction --prefer-dist
sudo -u "$APP_USER" php artisan key:generate
sudo -u "$APP_USER" php artisan migrate --force --seed

echo "[8/12] npm install + build as www-data..."
# clean any old root-owned npm cache under /var/www
rm -rf /var/www/.npm

sudo -u "$APP_USER" env HOME="$APP_DIR" npm cache clean --force
sudo -u "$APP_USER" env HOME="$APP_DIR" npm install
sudo -u "$APP_USER" env HOME="$APP_DIR" npm run build

echo "[9/12] storage:link, optimize, permissions..."
sudo -u "$APP_USER" php artisan storage:link || true
sudo -u "$APP_USER" php artisan config:clear
sudo -u "$APP_USER" php artisan optimize

# Final permissions
chown -R "$APP_USER":"$APP_USER" "$APP_DIR"
find "$APP_DIR" -type d -exec chmod 755 {} \;
find "$APP_DIR" -type f -exec chmod 644 {} \;
chmod +x "$APP_DIR/artisan"
chmod -R ug+rwx "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"

echo "[10/12] nginx site config..."

cat >/etc/nginx/sites-available/"$NGINX_SITE_NAME" <<EOF
server {
    listen 80;
    server_name _;

    root $APP_DIR/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        include fastcgi_params;
        fastcgi_pass unix:$PHP_FPM_SOCK;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        fastcgi_param PATH_INFO \$fastcgi_path_info;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

ln -sf /etc/nginx/sites-available/"$NGINX_SITE_NAME" /etc/nginx/sites-enabled/"$NGINX_SITE_NAME"
rm -f /etc/nginx/sites-enabled/default || true

echo "[11/12] Configure Supervisor Workers..."
# Create configuration file
cat >/etc/supervisor/conf.d/admix-worker.conf <<EOF
[program:admix-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $APP_DIR/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=$APP_USER
numprocs=20
redirect_stderr=true
stdout_logfile=$APP_DIR/storage/logs/worker.log
stopwaitsecs=3600
EOF

# Ensure log directory exists and is writable
mkdir -p "$APP_DIR/storage/logs"
chown -R "$APP_USER":"$APP_USER" "$APP_DIR/storage"
chmod -R 775 "$APP_DIR/storage"

# Update supervisor
supervisorctl reread
supervisorctl update
supervisorctl start all


echo "[12/12] Test and restart nginx + php-fpm..."
nginx -t
systemctl enable --now php8.3-fpm nginx supervisor
systemctl restart php8.3-fpm
systemctl restart nginx

echo
echo "============================================================"
echo " AdmixCentral install complete."
echo " Directory : $APP_DIR"
echo " APP_NAME  : AdmixCentral"
echo " DB        : SQLite (database/database.sqlite)"
echo " Workers   : 20 processes running via Supervisor"
echo " URL       : http://<server-ip>/"
echo "============================================================"
