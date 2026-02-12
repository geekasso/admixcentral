#!/usr/bin/env bash
set -Eeuo pipefail

LOG_FILE="/root/admixcentral_install_part1.log"
exec > >(tee -a "$LOG_FILE") 2>&1
trap 'echo; echo "[X] FAILED at line $LINENO. See '"$LOG_FILE"'"; exit 1' ERR

log(){ echo -e "\n[+] $*\n"; }
die(){ echo -e "\n[X] $*\n"; exit 1; }

[[ "${EUID}" -eq 0 ]] || die "Run as root: sudo bash $0"

# ---------------- CONFIG (override via env) ----------------
PHP_VER="${PHP_VER:-8.3}"
NODE_MAJOR="${NODE_MAJOR:-20}"

DB_NAME="${DB_NAME:-admixcentral}"
DB_USER_HINT="${DB_USER_HINT:-admixcentral}"   # only printed as example
# -----------------------------------------------------------

wait_for_apt_locks() {
  log "Waiting for apt/dpkg locks to clear (unattended-upgrades, etc.)"
  local i=0
  while fuser /var/lib/dpkg/lock-frontend >/dev/null 2>&1 \
     || fuser /var/lib/apt/lists/lock >/dev/null 2>&1 \
     || fuser /var/cache/apt/archives/lock >/dev/null 2>&1; do
    i=$((i+1))
    [[ $i -le 300 ]] || die "apt locks did not clear after ~10 minutes"
    sleep 2
  done
}

apt_install() {
  wait_for_apt_locks
  DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends "$@"
}

install_composer() {
  log "Installing Composer"
  if command -v composer >/dev/null 2>&1; then
    composer --version || true
    return 0
  fi
  php -r "copy('https://getcomposer.org/installer','/tmp/composer-setup.php');"
  php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
  rm -f /tmp/composer-setup.php
  composer --version
}

install_node() {
  log "Installing Node.js ${NODE_MAJOR}.x"
  if command -v node >/dev/null 2>&1 && node -v | grep -qE "^v${NODE_MAJOR}\."; then
    node -v
    npm -v || true
    return 0
  fi
  curl -fsSL "https://deb.nodesource.com/setup_${NODE_MAJOR}.x" | bash -
  apt_install nodejs
  node -v
  npm -v || true
}

wait_for_mysql() {
  log "Waiting for MySQL to be ready"
  systemctl enable --now mysql
  local i=0
  until mysqladmin ping --silent >/dev/null 2>&1; do
    i=$((i+1))
    if [[ $i -gt 120 ]]; then
      systemctl status mysql --no-pager || true
      journalctl -u mysql -n 200 --no-pager || true
      die "MySQL startup failed"
    fi
    sleep 2
  done
}

create_database_only() {
  log "Creating MySQL database ONLY (no users): ${DB_NAME}"
  sudo mysql --protocol=socket -e \
    "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
}

main() {
  log "Updating apt"
  wait_for_apt_locks
  apt-get update -y

  log "Installing base packages"
  apt_install ca-certificates curl gnupg git unzip lsb-release apt-transport-https minisign

  log "Installing Nginx + MySQL + Supervisor"
  apt_install nginx mysql-server supervisor

  log "Installing PHP ${PHP_VER} + extensions"
  apt_install \
    php${PHP_VER}-cli php${PHP_VER}-fpm \
    php${PHP_VER}-mysql php${PHP_VER}-mbstring php${PHP_VER}-xml php${PHP_VER}-curl \
    php${PHP_VER}-zip php${PHP_VER}-gd php${PHP_VER}-bcmath php${PHP_VER}-intl

  install_composer
  install_node

  log "Enabling services"
  systemctl enable --now nginx
  systemctl enable --now php${PHP_VER}-fpm

  wait_for_mysql
  create_database_only

  log "PART 1 COMPLETE (stopped BEFORE git clone)"
  echo
  echo "======================================================================"
  echo "MANUAL STEPS REQUIRED BEFORE PART 2"
  echo "----------------------------------------------------------------------"
  echo "Ubuntu does NOT require a root password. Do NOT set one."
  echo
  echo "1) Create a MySQL user with privileges on database: ${DB_NAME}"
  echo
  echo "   sudo mysql"
  echo
  echo "   CREATE USER '${DB_USER_HINT}'@'localhost' IDENTIFIED BY 'CHANGE_ME_STRONG_PASSWORD';"
  echo "   GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER_HINT}'@'localhost';"
  echo "   FLUSH PRIVILEGES;"
  echo "   EXIT;"
  echo
  echo "2) Verify the credentials work (this MUST succeed):"
  echo "   mysql -u${DB_USER_HINT} -p -h 127.0.0.1 ${DB_NAME} -e \"SELECT 1;\""
  echo
  echo "3) Run Part 2:"
  echo "   sudo bash install_admixcentral_part2_app.sh"
  echo
  echo "Logs:"
  echo "  Part 1 log: ${LOG_FILE}"
  echo "======================================================================"
}

main "$@"
