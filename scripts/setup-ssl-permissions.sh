#!/bin/bash
set -e

# basic check for root
if [ "${EUID:-$(id -u)}" -ne 0 ]; then
  echo "Please run this script as root (use sudo)." >&2
  exit 1
fi

APP_USER="www-data"
NGINX_SITE_NAME="admixcentral"

echo "Configuring sudoers for Admix Central SSL management..."

# Create the sudoers file with restricted permissions
# Allows www-data to run specific commands needed for SSL installation/renewal without a password
cat >/etc/sudoers.d/admixcentral <<EOF
$APP_USER ALL=(ALL) NOPASSWD: /usr/bin/certbot, /usr/sbin/nginx, /usr/bin/systemctl reload nginx, /usr/bin/tee /etc/nginx/sites-available/$NGINX_SITE_NAME
EOF

# Secure the file (sudoers files must be 0440)
chmod 0440 /etc/sudoers.d/admixcentral

echo "✅ Permissions applied."
echo "The web server user ($APP_USER) can now manage SSL certificates."
