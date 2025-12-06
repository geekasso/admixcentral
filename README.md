# AdmixCentral

AdmixCentral is a centralized firewall management dashboard tailored for managing multiple **pfSense** instances. It leverages the pfSense API to provide a unified interface for system administrators to manage firewalls, companies, and users from a single pane of glass.

## Features

- **Multi-Tenancy**: Manage multiple companies and their respective firewalls.
- **Unified Dashboard**: View system status, resource usage, and critical alerts across all managed firewalls.
- **Firewall Management**:
    - **Aliases**: Create, edit, and delete aliases.
    - **NAT**: Port Forward, Outbound, and 1:1 NAT mapping management.
    - **Rules**: Manage firewall rules with drag-and-drop reordering (planned).
    - **Virtual IPs**: Manage CARP, IP Alias, and Proxy ARP virtual IPs.
    - **Traffic Shaper**: Configure Limiters for bandwidth management.
- **Service Management**:
    - **DHCP Server**: Manage scopes, static mappings (with subnet suggestions), and relay.
    - **DNS**: Manage DNS Forwarder and Resolver settings.
    - **VPN**: Manage OpenVPN (Server/Client) and IPSec (Phase 1 & 2) and WireGuard configurations.
- **Diagnostics**:
    - View ARP tables, Firewall States, and System Logs.
    - Execute shell commands, ping, traceroute, and reboot/halt systems.
- **Bulk Actions**: Apply configurations (Aliases, Rules) to multiple firewalls simultaneously.
- **Secure Integration**: Interacts with pfSense contexts via the REST API or XMLRPC (legacy support).

## Tech Stack

- **Framework**: [Laravel 11.x](https://laravel.com) (PHP 8.2+)
- **Frontend**: Blade Templates, Tailwind CSS, Alpine.js
- **Database**: SQLite (default), MySQL/PostgreSQL supported
- **API Integration**: Custom service layer interacting with [jaredhendrickson13/pfsense-api](https://github.com/jaredhendrickson13/pfsense-api)

## Installation Guide

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & NPM
- SQLite (or another database server)

### Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/yourusername/admixcentral.git
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
   *Note: creating the sqlite database file might be required if it doesn't exist:*
   ```bash
   touch database/database.sqlite
   ```

4. **Run Migrations & Seed Database**
   This sets up the database schema and creates default demo users.
   ```bash
   php artisan migrate --seed
   ```

5. **Build Frontend Assets**
   ```bash
   npm run build
   ```

6. **Serve the Application**
   ```bash
   php artisan serve
   ```
   or preferably using composer to start backend and frontend (Vite) concurrently:
   ```bash
   composer run dev
   ```
   The application will be available at `http://127.0.0.1:8000` (and `http://0.0.0.0:8000` for external access).

## Default Credentials

The database seeder creates the following default users:

| Role | Email | Password |
|------|-------|----------|
| **Global Admin** | `admin@admixcentral.com` | `password` |
| **Demo User** | `user@demo.com` | `password` |

## Firewall Setup

To manage a pfSense firewall, ensure the [pfsense-api](https://github.com/jaredhendrickson13/pfsense-api) package is installed on the target pfSense machine.

1. Log in to AdmixCentral.
2. Navigate to **Firewalls > Add Firewall**.
3. Enter the pfSense URL, API credentials, and Netgate ID.
