# AdmixCentral

AdmixCentral is a centralized firewall management dashboard tailored for managing multiple **pfSense** instances. It leverages the pfSense API to provide a unified interface for system administrators to manage firewalls, companies, and users from a single pane of glass.

## Features

- **Multi-Tenancy**: Manage multiple companies and their respective firewalls securely with isolated scopes.
- **Unified Dashboard**: View system status, resource usage (CPU, RAM, Swap), and critical alerts across all managed firewalls.
- **System Customization**:
    - **Branding**: Upload custom Logos and Favicons.
    - **Theming**: Toggle between Dark and Light modes for the entire application.
- **Firewall Management**:
    - **Aliases**: Create, edit, and delete aliases with bulk update capabilities.
    - **NAT**: Full support for Port Forward, Outbound, and 1:1 NAT mapping management.
    - **Rules**: Manage firewall rules with drag-and-drop reordering.
    - **Virtual IPs**: Manage CARP, IP Alias, and Proxy ARP virtual IPs.
    - **Traffic Shaper**: Configure Limiters for bandwidth management.
- **Service Management**:
    - **DHCP**: Manage scopes, static mappings (with subnet suggestions), and relay.
    - **DNS**: Manage DNS Forwarder and Resolver settings.
    - **VPN**: 
        - **OpenVPN**: Server and Client management.
        - **IPSec**: Full Phase 1 and Phase 2 tunnel management (IKEv1/v2, Modern Encryption).
        - **WireGuard**: Tunnel and Peer management.
    - **Additional**: ACME (Let's Encrypt), HAProxy, FreeRADIUS.
- **Diagnostics**:
    - **Interactive Tables**: View and filter system tables (pf tables).
    - **Status Pages**: ARP Table, Firewall States, System Logs, Gateways, Interfaces.
    - **Tools**: Execute shell commands, Ping, Traceroute, Reboot, and Halt systems.
- **Bulk Actions**: 
    - Apply configurations (Aliases, NAT Rules, Firewall Rules, IPSec Tunnels) to multiple firewalls simultaneously.
    - Perform bulk system actions like Reboot or Update.
- **Secure Integration**: Interacts with pfSense via the REST API or XMLRPC.

## Tech Stack

- **Framework**: [Laravel 11.x](https://laravel.com) (PHP 8.2+)
- **Frontend**: Blade Templates, Tailwind CSS (Custom `pf-*` utility classes), Alpine.js
- **Database**: SQLite (default), MySQL/PostgreSQL supported
- **API Integration**: Custom service layer interacting with [jaredhendrickson13/pfsense-api](https://github.com/jaredhendrickson13/pfsense-api)

---

## Development & Testing Setup

Use these instructions for setting up a local development environment.

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & NPM
- SQLite (or another database server)

### Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/admxlz/admixcentral.git
   cd admixcentral
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Note: creating the sqlite database file works best if you create it first:*
   ```bash
   touch database/database.sqlite
   ```

4. **Run Migrations**
   This sets up the database schema.
   ```bash
   php artisan migrate
   ```

5. **Build Frontend Assets**
   This step is critical for CSS and JS to load correctly.
   ```bash
   npm run build
   ```

6. **Serve the Application (Development Mode)**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```
   or preferably using composer to start backend and frontend (Vite) concurrently:
   ```bash
   composer run dev
   ```
   The application will be available at `http://127.0.0.1:8000`.
   **Note:** On first access, you will be redirected to the Setup Wizard to create your admin account.

---

## Production Deployment (Nginx + PHP-FPM + SSL)

For a production environment, it is recommended to use Nginx with PHP-FPM and SSL enabled.

### 1. Requirements
Ensure your server has the following installed:
- Nginx
- PHP 8.2 or higher + FPM (`php8.2-fpm`)
- MySql / MariaDB (Recommended for production over SQLite)
- Certbot (for SSL)

### 2. File Ownership & Permissions
Set the correct permissions for the web server user (usually `www-data`):

```bash
cd /var/www/admixcentral

# Create storage link
php artisan storage:link

# Set ownership
chown -R www-data:www-data .

# Set permissions for storage directory
chmod -R 775 storage bootstrap/cache
```

### 3. Nginx Configuration
Create a new configuration file at `/etc/nginx/sites-available/admixcentral`:

```nginx
server {
    listen 80;
    server_name dashboard.yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name dashboard.yourdomain.com;
    root /var/www/admixcentral/public;

    # IMPORTANT: Ensure the storage link exists and points to the correct location
    # Run: php artisan storage:link

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.html index.htm index.php;

    charset utf-8;

    # SSL Configuration (Let's Encrypt placeholders)
    # ssl_certificate /etc/letsencrypt/live/dashboard.yourdomain.com/fullchain.pem;
    # ssl_certificate_key /etc/letsencrypt/live/dashboard.yourdomain.com/privkey.pem;
    # include /etc/letsencrypt/options-ssl-nginx.conf;
    # ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:
```bash
ln -s /etc/nginx/sites-available/admixcentral /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

### 4. Enable SSL with Certbot
The easiest way to secure your application is using Certbot (Let's Encrypt):

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain and install certificate
sudo certbot --nginx -d dashboard.yourdomain.com
```
Certbot will automatically update your Nginx configuration with the correct SSL paths.

---

## Initial Setup

Upon first installation, AdmixCentral requires you to create a **Global Admin** account via the secure Setup Wizard.

1. Access the application in your browser (e.g., `http://dashboard.yourdomain.com`).
2. You will be automatically redirected to the **Setup Wizard**.
3. Create your admin account details.
4. You will then be logged in and redirected to the Dashboard.

## Firewall Setup

To manage a pfSense firewall, ensure the [pfsense-api](https://github.com/jaredhendrickson13/pfsense-api) package is installed on the target pfSense machine.

1. Log in to AdmixCentral.
2. Navigate to **Firewalls > Add Firewall**.
3. Enter the **pfSense URL** and **API Credentials** (Username/Password).
4. Click **Connect**. AdmixCentral will automatically verify the connection and retrieve system details.

---

## Troubleshooting

### "403 Forbidden" on Uploaded Images (Logo/Favicon)
If you encounter a 403 error for uploaded images, it usually means the webserver cannot find the file in the `public/storage` directory, or it is looking in the wrong place.

**Common Causes:**
1.  **Missing Symlink**: The `public/storage` symbolic link is missing.
    *   **Fix**: Run `php artisan storage:link`.
2.  **Wrong Disk**: The application was saving files to `storage/app/private` (default) instead of `storage/app/public`.
    *   **Fix**: Update `SystemCustomizationController.php` to use `store('path', 'public')` (This is fixed in the latest codebase).
3.  **Permissions**: The webserver user (e.g., `www-data` or `nginx`) does not have permission to read the storage folder.
    *   **Fix**: `chmod -R 775 storage/app/public` and ensure ownership is correct.

**Verification:**
Check if the file actually exists where the symlink points:
```bash
ls -la storage/app/public/customization/
```
If the file is missing there but you have a URL, the upload process likely failed or saved to the wrong disk.

## Disclaimers

> [!CAUTION]
> This package is not affiliated or supported by Netgate or the pfSense team.
