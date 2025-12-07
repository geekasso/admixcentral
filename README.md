# AdmixCentral

AdmixCentral is a centralized firewall management dashboard tailored for managing multiple **pfSense** instances. It leverages the pfSense API to provide a unified interface for system administrators to manage firewalls, companies, and users from a single pane of glass.

## Features

- **Multi-Tenancy**: Manage multiple companies and their respective firewalls.
- **Unified Dashboard**: View system status, resource usage (CPU, RAM, Swap), and critical alerts across all managed firewalls.
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
   This step is critical for CSS and JS to load correctly.
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
   The application will be available at `http://127.0.0.1:8000`.

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
3. Enter the **pfSense URL** and **API Credentials** (Username/Password).
4. Click **Connect**. AdmixCentral will automatically verify the connection and retrieve system details.
   *(Note: Netgate ID is no longer required for adding a firewall)*
