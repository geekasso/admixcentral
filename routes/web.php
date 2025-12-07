<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoutingController;

use App\Http\Controllers\VpnIpsecController;
use App\Http\Controllers\VpnOpenVpnController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/test-routing', function () {
    return 'Routing Works';
});

// Setup Wizard (protected by CheckSystemSetup middleware to only be accessible when no users exist)
Route::get('/setup', [App\Http\Controllers\SetupController::class, 'welcome'])->name('setup.welcome');
Route::post('/setup', [App\Http\Controllers\SetupController::class, 'store'])->name('setup.store');

Route::middleware(['auth', 'verified'])->group(function () {
    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // System: Routing
    Route::get('/firewall/{firewall}/system/routing', [RoutingController::class, 'index'])->name('firewall.system.routing');

    // Routing: Gateways
    Route::post('/firewall/{firewall}/system/routing/gateways', [RoutingController::class, 'storeGateway'])->name('firewall.system.routing.gateways.store');
    Route::patch('/firewall/{firewall}/system/routing/gateways/{id}', [RoutingController::class, 'updateGateway'])->name('firewall.system.routing.gateways.update');
    Route::delete('/firewall/{firewall}/system/routing/gateways/{id}', [RoutingController::class, 'destroyGateway'])->name('firewall.system.routing.gateways.destroy');

    // Routing: Static Routes
    Route::post('/firewall/{firewall}/system/routing/static-routes', [RoutingController::class, 'storeStaticRoute'])->name('firewall.system.routing.static-routes.store');
    Route::patch('/firewall/{firewall}/system/routing/static-routes/{id}', [RoutingController::class, 'updateStaticRoute'])->name('firewall.system.routing.static-routes.update');
    Route::delete('/firewall/{firewall}/system/routing/static-routes/{id}', [RoutingController::class, 'destroyStaticRoute'])->name('firewall.system.routing.static-routes.destroy');

    // Routing: Gateway Groups
    Route::post('/firewall/{firewall}/system/routing/gateway-groups', [RoutingController::class, 'storeGatewayGroup'])->name('firewall.system.routing.gateway-groups.store');
    Route::patch('/firewall/{firewall}/system/routing/gateway-groups/{id}', [RoutingController::class, 'updateGatewayGroup'])->name('firewall.system.routing.gateway-groups.update');
    Route::delete('/firewall/{firewall}/system/routing/gateway-groups/{id}', [RoutingController::class, 'destroyGatewayGroup'])->name('firewall.system.routing.gateway-groups.destroy');

    // Main Dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/firewall/{firewall}/check-status', [App\Http\Controllers\DashboardController::class, 'checkStatus'])->name('firewall.check-status');

    // Bulk Firewall Actions
    Route::post('/firewalls/bulk/action', [App\Http\Controllers\FirewallBulkController::class, 'handle'])->name('firewalls.bulk.action');
    Route::get('/firewalls/bulk/create/{type}', [App\Http\Controllers\FirewallBulkController::class, 'create'])->name('firewalls.bulk.create');
    Route::post('/firewalls/bulk/store/{type}', [App\Http\Controllers\FirewallBulkController::class, 'store'])->name('firewalls.bulk.store');

    // Companies (admin only)
    Route::resource('companies', App\Http\Controllers\CompanyController::class)
        ->middleware('can:admin');

    // Firewalls CRUD - Place BEFORE the specific firewall routes
    // Exclude 'show' because we use a custom dashboard instead
    Route::resource('firewalls', App\Http\Controllers\FirewallController::class)
        ->except(['show'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class);

    // Status Dashboard (Must be before generic firewall dashboard route)
    Route::get('/firewall/{firewall}/status', [App\Http\Controllers\StatusDashboardController::class, 'index'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('status.dashboard');


    // Firewall-specific dashboard (must come AFTER resource routes to avoid conflicts)
    Route::get('/firewall/{firewall}', [App\Http\Controllers\DashboardController::class, 'firewall'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.dashboard');

    // Interfaces (Assignments, VLANs) - Must be before generic interface routes
    Route::prefix('firewall/{firewall}/interfaces')->name('interfaces.')->group(function () {
        // Assignments
        Route::get('/assignments', [App\Http\Controllers\InterfacesController::class, 'assignments'])->name('assignments');
        Route::post('/assignments', [App\Http\Controllers\InterfacesController::class, 'storeAssignment'])->name('assignments.store');
        Route::delete('/assignments/{id}', [App\Http\Controllers\InterfacesController::class, 'destroyAssignment'])->name('assignments.destroy');

        // VLANs
        Route::get('/vlans', [App\Http\Controllers\InterfacesController::class, 'vlans'])->name('vlans.index');
        Route::get('/vlans/create', [App\Http\Controllers\InterfacesController::class, 'createVlan'])->name('vlans.create');
        Route::post('/vlans', [App\Http\Controllers\InterfacesController::class, 'storeVlan'])->name('vlans.store');
        Route::get('/vlans/{id}/edit', [App\Http\Controllers\InterfacesController::class, 'editVlan'])->name('vlans.edit');
        Route::patch('/vlans/{id}', [App\Http\Controllers\InterfacesController::class, 'updateVlan'])->name('vlans.update');
        Route::delete('/vlans/{id}', [App\Http\Controllers\InterfacesController::class, 'destroyVlan'])->name('vlans.destroy');
    });

    // Interfaces management
    Route::get('/firewall/{firewall}/interfaces/{interface}/edit', [App\Http\Controllers\InterfaceController::class, 'edit'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.interfaces.edit');
    Route::put('/firewall/{firewall}/interfaces/{interface}', [App\Http\Controllers\InterfaceController::class, 'update'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.interfaces.update');
    Route::get('/firewall/{firewall}/interfaces', [App\Http\Controllers\InterfaceController::class, 'index'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.interfaces.index');
    Route::get('/firewall/{firewall}/interfaces/{interface}', [App\Http\Controllers\InterfaceController::class, 'show'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.interfaces.show');

    // Firewall rules management
    Route::get('/firewall/{firewall}/rules', [App\Http\Controllers\FirewallRuleController::class, 'index'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.rules.index');
    Route::get('/firewall/{firewall}/rules/create', [App\Http\Controllers\FirewallRuleController::class, 'create'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.rules.create');
    Route::post('/firewall/{firewall}/rules', [App\Http\Controllers\FirewallRuleController::class, 'store'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.rules.store');
    Route::get('/firewall/{firewall}/rules/{tracker}/edit', [App\Http\Controllers\FirewallRuleController::class, 'edit'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.rules.edit');
    Route::put('/firewall/{firewall}/rules/{tracker}', [App\Http\Controllers\FirewallRuleController::class, 'update'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.rules.update');
    Route::delete('/firewall/{firewall}/rules/{tracker}', [App\Http\Controllers\FirewallRuleController::class, 'destroy'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.rules.destroy');
    Route::post('/firewall/{firewall}/rules/bulk-action', [App\Http\Controllers\FirewallRuleController::class, 'bulkAction'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.rules.bulk-action');
    Route::post('/firewall/{firewall}/rules/{tracker}/move', [App\Http\Controllers\FirewallRuleController::class, 'move'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.rules.move');
    Route::post('/firewall/{firewall}/apply', [App\Http\Controllers\FirewallApplyController::class, 'apply'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.apply');

    // Firewall Aliases
    Route::get('/firewall/{firewall}/aliases', [App\Http\Controllers\FirewallAliasController::class, 'index'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.aliases.index');
    Route::get('/firewall/{firewall}/aliases/create', [App\Http\Controllers\FirewallAliasController::class, 'create'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.aliases.create');
    Route::post('/firewall/{firewall}/aliases', [App\Http\Controllers\FirewallAliasController::class, 'store'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.aliases.store');
    Route::get('/firewall/{firewall}/aliases/{id}/edit', [App\Http\Controllers\FirewallAliasController::class, 'edit'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.aliases.edit');
    Route::put('/firewall/{firewall}/aliases/{id}', [App\Http\Controllers\FirewallAliasController::class, 'update'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.aliases.update');
    Route::delete('/firewall/{firewall}/aliases/{id}', [App\Http\Controllers\FirewallAliasController::class, 'destroy'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.aliases.destroy');

    // Firewall NAT
    Route::get('/firewall/{firewall}/nat/port-forward', [App\Http\Controllers\FirewallNatController::class, 'portForward'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.nat.port-forward');
    Route::get('/firewall/{firewall}/nat/port-forward/create', [App\Http\Controllers\FirewallNatController::class, 'createPortForward'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.nat.port-forward.create');
    Route::post('/firewall/{firewall}/nat/port-forward', [App\Http\Controllers\FirewallNatController::class, 'storePortForward'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.nat.port-forward.store');
    Route::put('/firewall/{firewall}/nat/port-forward/{id}', [App\Http\Controllers\FirewallNatController::class, 'updatePortForward'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.nat.port-forward.update');
    Route::delete('/firewall/{firewall}/nat/port-forward/{id}', [App\Http\Controllers\FirewallNatController::class, 'destroyPortForward'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.nat.port-forward.destroy');
    Route::get('/firewall/{firewall}/nat/outbound', [App\Http\Controllers\FirewallNatController::class, 'outbound'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.nat.outbound');
    Route::patch('/firewall/{firewall}/nat/outbound/mode', [App\Http\Controllers\FirewallNatController::class, 'updateOutboundMode'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.nat.outbound.mode');
    Route::get('/firewall/{firewall}/nat/one-to-one', [App\Http\Controllers\FirewallNatController::class, 'oneToOne'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.nat.one-to-one');
    Route::post('/firewall/{firewall}/nat/one-to-one', [App\Http\Controllers\FirewallNatController::class, 'storeOneToOne'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.nat.one-to-one.store');
    Route::put('/firewall/{firewall}/nat/one-to-one/{id}', [App\Http\Controllers\FirewallNatController::class, 'updateOneToOne'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.nat.one-to-one.update');
    Route::delete('/firewall/{firewall}/nat/one-to-one/{id}', [App\Http\Controllers\FirewallNatController::class, 'destroyOneToOne'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('firewall.nat.one-to-one.destroy');

    // Firewall Resources Group
    Route::prefix('firewall/{firewall}')->name('firewall.')->group(function () {
        // Schedules
        Route::resource('schedules', App\Http\Controllers\FirewallScheduleController::class);

        // Traffic Shaper Limiters
        Route::resource('limiters', App\Http\Controllers\FirewallLimiterController::class);

        // Virtual IPs
        Route::resource('virtual_ips', App\Http\Controllers\FirewallVirtualIpController::class);

        // Status
        Route::get('/status/interfaces', [App\Http\Controllers\StatusInterfaceController::class, 'index'])->name('status.interfaces.index');

        // Diagnostics
        Route::get('/diagnostics/ping', [App\Http\Controllers\DiagnosticsPingController::class, 'index'])->name('diagnostics.ping.index');
        Route::post('/diagnostics/ping', [App\Http\Controllers\DiagnosticsPingController::class, 'ping'])->name('diagnostics.ping.run');

        // OpenVPN
        Route::get('/vpn/openvpn/server', [App\Http\Controllers\VpnOpenVpnController::class, 'servers'])->name('vpn.openvpn.servers');
        Route::get('/vpn/openvpn/client', [App\Http\Controllers\VpnOpenVpnController::class, 'clients'])->name('vpn.openvpn.clients');
        Route::get('/vpn/openvpn/server/create', [App\Http\Controllers\VpnOpenVpnController::class, 'createServer'])->name('vpn.openvpn.server.create');
        Route::post('/vpn/openvpn/server', [App\Http\Controllers\VpnOpenVpnController::class, 'storeServer'])->name('vpn.openvpn.server.store');


    });

    // User Management
    Route::resource('users', App\Http\Controllers\UserController::class)
        ->middleware([App\Http\Middleware\CheckRole::class . ':admin']);

    // System Customization (Global Admin)
    Route::get('/system/customization', [App\Http\Controllers\SystemCustomizationController::class, 'index'])
        ->middleware([App\Http\Middleware\CheckRole::class . ':admin'])
        ->name('system.customization.index');
    Route::post('/system/customization', [App\Http\Controllers\SystemCustomizationController::class, 'update'])
        ->middleware([App\Http\Middleware\CheckRole::class . ':admin'])
        ->name('system.customization.update');

    // Services - DHCP Server
    Route::get('/firewall/{firewall}/services/dhcp/{interface?}', [App\Http\Controllers\ServicesDhcpServerController::class, 'index'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('services.dhcp.index');
    Route::patch('/firewall/{firewall}/services/dhcp/{interface}', [App\Http\Controllers\ServicesDhcpServerController::class, 'update'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('services.dhcp.update');

    // Services - DNS Resolver
    Route::get('/firewall/{firewall}/services/dns-resolver', [App\Http\Controllers\ServicesDnsResolverController::class, 'index'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('services.dns.resolver');
    Route::patch('/firewall/{firewall}/services/dns-resolver', [App\Http\Controllers\ServicesDnsResolverController::class, 'update'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('services.dns.resolver.update');
    Route::get('/firewall/{firewall}/services/dns-resolver/host-overrides', [App\Http\Controllers\ServicesDnsResolverController::class, 'hostOverrides'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('services.dns.host-overrides');
    Route::post('/firewall/{firewall}/services/dns-resolver/host-overrides', [App\Http\Controllers\ServicesDnsResolverController::class, 'storeHostOverride'])
        ->middleware(App\Http\Middleware\EnsureTenantScope::class)
        ->name('services.dns.host-overrides.store');





    // System
    Route::prefix('firewall/{firewall}/system')->name('system.')->group(function () {
        Route::get('/advanced', [App\Http\Controllers\SystemController::class, 'advanced'])->name('advanced');
        Route::post('/advanced', [App\Http\Controllers\SystemController::class, 'updateAdvanced'])->name('advanced.update');
        Route::post('/advanced/tunables', [App\Http\Controllers\SystemController::class, 'storeTunable'])->name('advanced.tunables.store');
        Route::patch('/advanced/tunables/{id}', [App\Http\Controllers\SystemController::class, 'updateTunable'])->name('advanced.tunables.update');
        Route::delete('/advanced/tunables/{id}', [App\Http\Controllers\SystemController::class, 'destroyTunable'])->name('advanced.tunables.destroy');
        Route::get('/general-setup', [App\Http\Controllers\SystemController::class, 'generalSetup'])->name('general-setup');
        Route::post('/general-setup', [App\Http\Controllers\SystemController::class, 'updateGeneralSetup'])->name('general-setup.update');
        Route::get('/high-avail-sync', [App\Http\Controllers\SystemController::class, 'highAvailSync'])->name('high-avail-sync');

        // Package Manager
        Route::get('/package-manager', [App\Http\Controllers\PackageManagerController::class, 'index'])->name('package_manager.index');
        Route::post('/package-manager/install', [App\Http\Controllers\PackageManagerController::class, 'install'])->name('package_manager.install');
        Route::post('/package-manager/uninstall', [App\Http\Controllers\PackageManagerController::class, 'uninstall'])->name('package_manager.uninstall');

        Route::get('/notifications', [App\Http\Controllers\SystemController::class, 'notifications'])->name('notifications');
        Route::post('/notifications', [App\Http\Controllers\SystemController::class, 'updateNotifications'])->name('notifications.update');

        // Routing

        Route::get('/update', [App\Http\Controllers\SystemController::class, 'update'])->name('update');

        // User Manager
        Route::get('/user-manager', [App\Http\Controllers\UserManagerController::class, 'index'])->name('user_manager.index');

        // Users
        Route::get('/user-manager/users/create', [App\Http\Controllers\UserManagerController::class, 'createUser'])->name('user_manager.users.create');
        Route::post('/user-manager/users', [App\Http\Controllers\UserManagerController::class, 'storeUser'])->name('user_manager.users.store');
        Route::get('/user-manager/users/{id}/edit', [App\Http\Controllers\UserManagerController::class, 'editUser'])->name('user_manager.users.edit');
        Route::patch('/user-manager/users/{id}', [App\Http\Controllers\UserManagerController::class, 'updateUser'])->name('user_manager.users.update');
        Route::delete('/user-manager/users/{id}', [App\Http\Controllers\UserManagerController::class, 'destroyUser'])->name('user_manager.users.destroy');

        // Groups
        Route::get('/user-manager/groups/create', [App\Http\Controllers\UserManagerController::class, 'createGroup'])->name('user_manager.groups.create');
        Route::post('/user-manager/groups', [App\Http\Controllers\UserManagerController::class, 'storeGroup'])->name('user_manager.groups.store');
        Route::get('/user-manager/groups/{id}/edit', [App\Http\Controllers\UserManagerController::class, 'editGroup'])->name('user_manager.groups.edit');
        Route::patch('/user-manager/groups/{id}', [App\Http\Controllers\UserManagerController::class, 'updateGroup'])->name('user_manager.groups.update');
        Route::delete('/user-manager/groups/{id}', [App\Http\Controllers\UserManagerController::class, 'destroyGroup'])->name('user_manager.groups.destroy');

        // Certificate Manager
        Route::get('/certificate-manager', [App\Http\Controllers\CertificateManagerController::class, 'index'])->name('certificate_manager.index');

        // CAs
        Route::get('/certificate-manager/cas/create', [App\Http\Controllers\CertificateManagerController::class, 'createCa'])->name('certificate_manager.cas.create');
        Route::post('/certificate-manager/cas', [App\Http\Controllers\CertificateManagerController::class, 'storeCa'])->name('certificate_manager.cas.store');
        Route::delete('/certificate-manager/cas/{id}', [App\Http\Controllers\CertificateManagerController::class, 'destroyCa'])->name('certificate_manager.cas.destroy');

        // Certificates
        Route::get('/certificate-manager/certificates/create', [App\Http\Controllers\CertificateManagerController::class, 'createCert'])->name('certificate_manager.certificates.create');
        Route::post('/certificate-manager/certificates', [App\Http\Controllers\CertificateManagerController::class, 'storeCert'])->name('certificate_manager.certificates.store');
        Route::delete('/certificate-manager/certificates/{id}', [App\Http\Controllers\CertificateManagerController::class, 'destroyCert'])->name('certificate_manager.certificates.destroy');
    });



    // Services
    Route::prefix('firewall/{firewall}/services')->name('services.')->group(function () {
        Route::get('/captive-portal', [App\Http\Controllers\ServicesController::class, 'captivePortal'])->name('captive-portal');
        Route::get('/auto-config-backup', [App\Http\Controllers\ServicesController::class, 'autoConfigBackup'])->name('auto-config-backup');

        // ACME (Let's Encrypt)
        Route::get('/acme', [App\Http\Controllers\ServicesAcmeController::class, 'index'])->name('acme.index');
        Route::get('/acme/account-keys', [App\Http\Controllers\ServicesAcmeController::class, 'accountKeys'])->name('acme.account-keys');
        Route::post('/acme/account-keys', [App\Http\Controllers\ServicesAcmeController::class, 'storeAccountKey'])->name('acme.account-keys.store');
        Route::delete('/acme/account-keys/{id}', [App\Http\Controllers\ServicesAcmeController::class, 'destroyAccountKey'])->name('acme.account-keys.destroy');
        Route::get('/acme/certificates', [App\Http\Controllers\ServicesAcmeController::class, 'certificates'])->name('acme.certificates');
        Route::post('/acme/certificates', [App\Http\Controllers\ServicesAcmeController::class, 'storeCertificate'])->name('acme.certificates.store');
        Route::post('/acme/certificates/issue', [App\Http\Controllers\ServicesAcmeController::class, 'issueCertificate'])->name('acme.certificates.issue');
        Route::post('/acme/certificates/renew', [App\Http\Controllers\ServicesAcmeController::class, 'renewCertificate'])->name('acme.certificates.renew');
        Route::delete('/acme/certificates/{id}', [App\Http\Controllers\ServicesAcmeController::class, 'destroyCertificate'])->name('acme.certificates.destroy');
        Route::get('/acme/settings', [App\Http\Controllers\ServicesAcmeController::class, 'settings'])->name('acme.settings');
        Route::post('/acme/settings', [App\Http\Controllers\ServicesAcmeController::class, 'updateSettings'])->name('acme.settings.update');

        // HAProxy
        Route::get('/haproxy', [App\Http\Controllers\ServicesHaproxyController::class, 'index'])->name('haproxy.index');
        Route::get('/haproxy/settings', [App\Http\Controllers\ServicesHaproxyController::class, 'settings'])->name('haproxy.settings');
        Route::post('/haproxy/settings', [App\Http\Controllers\ServicesHaproxyController::class, 'updateSettings'])->name('haproxy.settings.update');

        // HAProxy Frontends
        Route::get('/haproxy/frontends', [App\Http\Controllers\ServicesHaproxyController::class, 'frontends'])->name('haproxy.frontends.index');
        Route::get('/haproxy/frontends/create', [App\Http\Controllers\ServicesHaproxyController::class, 'createFrontend'])->name('haproxy.frontends.create');
        Route::post('/haproxy/frontends', [App\Http\Controllers\ServicesHaproxyController::class, 'storeFrontend'])->name('haproxy.frontends.store');
        Route::get('/haproxy/frontends/{id}/edit', [App\Http\Controllers\ServicesHaproxyController::class, 'editFrontend'])->name('haproxy.frontends.edit');
        Route::put('/haproxy/frontends/{id}', [App\Http\Controllers\ServicesHaproxyController::class, 'updateFrontend'])->name('haproxy.frontends.update');
        Route::delete('/haproxy/frontends/{id}', [App\Http\Controllers\ServicesHaproxyController::class, 'destroyFrontend'])->name('haproxy.frontends.destroy');

        // HAProxy Backends
        Route::get('/haproxy/backends', [App\Http\Controllers\ServicesHaproxyController::class, 'backends'])->name('haproxy.backends.index');
        Route::get('/haproxy/backends/create', [App\Http\Controllers\ServicesHaproxyController::class, 'createBackend'])->name('haproxy.backends.create');
        Route::post('/haproxy/backends', [App\Http\Controllers\ServicesHaproxyController::class, 'storeBackend'])->name('haproxy.backends.store');
        Route::get('/haproxy/backends/{id}/edit', [App\Http\Controllers\ServicesHaproxyController::class, 'editBackend'])->name('haproxy.backends.edit');
        Route::put('/haproxy/backends/{id}', [App\Http\Controllers\ServicesHaproxyController::class, 'updateBackend'])->name('haproxy.backends.update');
        Route::delete('/haproxy/backends/{id}', [App\Http\Controllers\ServicesHaproxyController::class, 'destroyBackend'])->name('haproxy.backends.destroy');

        // FreeRADIUS
        Route::get('/freeradius', [App\Http\Controllers\ServicesFreeradiusController::class, 'index'])->name('freeradius.index');

        // Settings
        Route::get('/freeradius/settings', [App\Http\Controllers\ServicesFreeradiusController::class, 'settings'])->name('freeradius.settings');
        Route::post('/freeradius/settings', [App\Http\Controllers\ServicesFreeradiusController::class, 'updateSettings'])->name('freeradius.settings.update');

        // Users
        Route::get('/freeradius/users', [App\Http\Controllers\ServicesFreeradiusController::class, 'users'])->name('freeradius.users.index');
        Route::get('/freeradius/users/create', [App\Http\Controllers\ServicesFreeradiusController::class, 'createUser'])->name('freeradius.users.create');
        Route::post('/freeradius/users', [App\Http\Controllers\ServicesFreeradiusController::class, 'storeUser'])->name('freeradius.users.store');
        Route::get('/freeradius/users/{username}/edit', [App\Http\Controllers\ServicesFreeradiusController::class, 'editUser'])->name('freeradius.users.edit');
        Route::put('/freeradius/users/{username}', [App\Http\Controllers\ServicesFreeradiusController::class, 'updateUser'])->name('freeradius.users.update');
        Route::delete('/freeradius/users/{username}', [App\Http\Controllers\ServicesFreeradiusController::class, 'destroyUser'])->name('freeradius.users.destroy');

        // Clients/NAS
        Route::get('/freeradius/clients', [App\Http\Controllers\ServicesFreeradiusController::class, 'clients'])->name('freeradius.clients.index');
        Route::get('/freeradius/clients/create', [App\Http\Controllers\ServicesFreeradiusController::class, 'createClient'])->name('freeradius.clients.create');
        Route::post('/freeradius/clients', [App\Http\Controllers\ServicesFreeradiusController::class, 'storeClient'])->name('freeradius.clients.store');
        Route::get('/freeradius/clients/{id}/edit', [App\Http\Controllers\ServicesFreeradiusController::class, 'editClient'])->name('freeradius.clients.edit');
        Route::put('/freeradius/clients/{id}', [App\Http\Controllers\ServicesFreeradiusController::class, 'updateClient'])->name('freeradius.clients.update');
        Route::delete('/freeradius/clients/{id}', [App\Http\Controllers\ServicesFreeradiusController::class, 'destroyClient'])->name('freeradius.clients.destroy');

        // Interfaces
        Route::get('/freeradius/interfaces', [App\Http\Controllers\ServicesFreeradiusController::class, 'interfaces'])->name('freeradius.interfaces.index');
        Route::get('/freeradius/interfaces/create', [App\Http\Controllers\ServicesFreeradiusController::class, 'createInterface'])->name('freeradius.interfaces.create');
        Route::post('/freeradius/interfaces', [App\Http\Controllers\ServicesFreeradiusController::class, 'storeInterface'])->name('freeradius.interfaces.store');
        Route::delete('/freeradius/interfaces/{id}', [App\Http\Controllers\ServicesFreeradiusController::class, 'destroyInterface'])->name('freeradius.interfaces.destroy');

        Route::get('/dhcp-relay', [App\Http\Controllers\ServicesController::class, 'dhcpRelay'])->name('dhcp-relay');
        Route::post('/dhcp-relay', [App\Http\Controllers\ServicesController::class, 'updateDhcpRelay'])->name('dhcp-relay.update');
        Route::get('/dhcpv6-relay', [App\Http\Controllers\ServicesController::class, 'dhcpv6Relay'])->name('dhcpv6-relay');
        Route::get('/dhcpv6-server', [App\Http\Controllers\ServicesController::class, 'dhcpv6Server'])->name('dhcpv6-server');
        Route::get('/dns-forwarder', [App\Http\Controllers\ServicesController::class, 'dnsForwarder'])->name('dns-forwarder');
        Route::get('/dynamic-dns', [App\Http\Controllers\ServicesController::class, 'dynamicDns'])->name('dynamic-dns');
        Route::get('/igmp-proxy', [App\Http\Controllers\ServicesController::class, 'igmpProxy'])->name('igmp-proxy');
        Route::get('/ntp', [App\Http\Controllers\ServicesController::class, 'ntp'])->name('ntp');
        Route::get('/pppoe-server', [App\Http\Controllers\ServicesController::class, 'pppoeServer'])->name('pppoe-server');
        Route::get('/router-advertisement', [App\Http\Controllers\ServicesController::class, 'routerAdvertisement'])->name('router-advertisement');
        Route::get('/snmp', [App\Http\Controllers\ServicesController::class, 'snmp'])->name('snmp');
        Route::get('/upnp', [App\Http\Controllers\ServicesController::class, 'upnp'])->name('upnp');
        Route::get('/wake-on-lan', [App\Http\Controllers\ServicesController::class, 'wakeOnLan'])->name('wake-on-lan');
    });

    // VPN
    Route::prefix('firewall/{firewall}/vpn')->name('vpn.')->group(function () {
        Route::get('/ipsec', [VpnIpsecController::class, 'tunnels'])->name('ipsec');
        Route::post('/ipsec/phase1', [VpnIpsecController::class, 'storePhase1'])->name('ipsec.phase1.store');
        Route::delete('/ipsec/phase1/{id}', [VpnIpsecController::class, 'destroyPhase1'])->name('ipsec.phase1.destroy');
        Route::get('/ipsec/phase2/{phase1}', [VpnIpsecController::class, 'phase2'])->name('ipsec.phase2');
        Route::post('/ipsec/phase2/{phase1}', [VpnIpsecController::class, 'storePhase2'])->name('ipsec.phase2.store');
        Route::delete('/ipsec/phase2/{phase1}/{uniqid}', [VpnIpsecController::class, 'destroyPhase2'])->name('ipsec.phase2.destroy');
        Route::get('/l2tp', [App\Http\Controllers\VpnController::class, 'l2tp'])->name('l2tp');

        // OpenVPN
        Route::get('/openvpn/server', [VpnOpenVpnController::class, 'servers'])->name('openvpn.servers');
        Route::get('/openvpn/server/create', [VpnOpenVpnController::class, 'createServer'])->name('openvpn.server.create');
        Route::post('/openvpn/server', [VpnOpenVpnController::class, 'storeServer'])->name('openvpn.server.store');
        Route::get('/openvpn/server/{id}/edit', [VpnOpenVpnController::class, 'editServer'])->name('openvpn.server.edit');
        Route::put('/openvpn/server/{id}', [VpnOpenVpnController::class, 'updateServer'])->name('openvpn.server.update');
        Route::delete('/openvpn/server/{id}', [VpnOpenVpnController::class, 'destroyServer'])->name('openvpn.server.destroy');
        Route::get('/openvpn/client', [VpnOpenVpnController::class, 'clients'])->name('openvpn.clients');

        Route::get('/wireguard', [App\Http\Controllers\VpnWireGuardController::class, 'index'])->name('wireguard.index');
    });

    Route::get('/debug-interfaces', function () {
        $firewall = \App\Models\Firewall::first();
        $api = new \App\Services\PfSenseApiService($firewall);
        dd($api->get('/interfaces'));
    });

    // Status
    Route::prefix('firewall/{firewall}/status')->name('status.')->group(function () {
        Route::get('/captive-portal', [App\Http\Controllers\StatusController::class, 'captivePortal'])->name('captive-portal');
        Route::get('/carp', [App\Http\Controllers\StatusController::class, 'carp'])->name('carp');
        Route::post('/carp', [App\Http\Controllers\StatusController::class, 'updateCarp'])->name('carp.update');
        Route::get('/dhcp-leases', [App\Http\Controllers\StatusController::class, 'dhcpLeases'])->name('dhcp-leases');
        Route::get('/dhcpv6-leases', [App\Http\Controllers\StatusController::class, 'dhcpv6Leases'])->name('dhcpv6-leases');
        Route::get('/filter-reload', [App\Http\Controllers\StatusController::class, 'filterReload'])->name('filter-reload');
        Route::get('/gateways', [App\Http\Controllers\StatusController::class, 'gateways'])->name('gateways');
        Route::get('/interfaces', [App\Http\Controllers\StatusController::class, 'interfaces'])->name('interfaces.index');
        Route::get('/ipsec', [App\Http\Controllers\StatusController::class, 'ipsec'])->name('ipsec');
        Route::get('/monitoring', [App\Http\Controllers\StatusController::class, 'monitoring'])->name('monitoring');
        Route::get('/ntp', [App\Http\Controllers\StatusController::class, 'ntp'])->name('ntp');
        Route::get('/openvpn', [App\Http\Controllers\StatusController::class, 'openvpn'])->name('openvpn');
        Route::get('/queues', [App\Http\Controllers\StatusController::class, 'queues'])->name('queues');
        Route::get('/services', [App\Http\Controllers\StatusController::class, 'services'])->name('services');
        Route::get('/system-logs', [App\Http\Controllers\StatusController::class, 'systemLogs'])->name('system-logs');
        Route::get('/traffic-graph', [App\Http\Controllers\StatusController::class, 'trafficGraph'])->name('traffic-graph');
        Route::get('/upnp', [App\Http\Controllers\StatusController::class, 'upnp'])->name('upnp');
        Route::get('/dhcp', [App\Http\Controllers\StatusController::class, 'dhcp'])->name('dhcp');
        Route::get('/system', [App\Http\Controllers\StatusController::class, 'system'])->name('system');
    });

    // Diagnostics
    Route::prefix('firewall/{firewall}/diagnostics')->name('diagnostics.')->group(function () {
        Route::get('/arp-table', [App\Http\Controllers\DiagnosticsController::class, 'arpTable'])->name('arp-table');
        Route::get('/authentication', [App\Http\Controllers\DiagnosticsController::class, 'authentication'])->name('authentication');
        Route::get('/backup-restore', [App\Http\Controllers\DiagnosticsController::class, 'backupRestore'])->name('backup-restore');
        Route::match(['get', 'post'], '/command-prompt', [App\Http\Controllers\DiagnosticsController::class, 'commandPrompt'])->name('command-prompt');
        Route::get('/dns-lookup', [App\Http\Controllers\DiagnosticsController::class, 'dnsLookup'])->name('dns-lookup');
        Route::get('/edit-file', [App\Http\Controllers\DiagnosticsController::class, 'editFile'])->name('edit-file');
        Route::get('/factory-defaults', [App\Http\Controllers\DiagnosticsController::class, 'factoryDefaults'])->name('factory-defaults');
        Route::match(['get', 'post'], '/halt-system', [App\Http\Controllers\DiagnosticsController::class, 'haltSystem'])->name('halt-system');
        Route::get('/limiter-info', [App\Http\Controllers\DiagnosticsController::class, 'limiterInfo'])->name('limiter-info');
        Route::get('/ndp-table', [App\Http\Controllers\DiagnosticsController::class, 'ndpTable'])->name('ndp-table');
        Route::get('/packet-capture', [App\Http\Controllers\DiagnosticsController::class, 'packetCapture'])->name('packet-capture');
        Route::get('/pf-info', [App\Http\Controllers\DiagnosticsController::class, 'pfInfo'])->name('pf-info');
        Route::get('/pf-top', [App\Http\Controllers\DiagnosticsController::class, 'pfTop'])->name('pf-top');
        Route::get('/ping', [App\Http\Controllers\DiagnosticsController::class, 'ping'])->name('ping');
        Route::match(['get', 'post'], '/reboot', [App\Http\Controllers\DiagnosticsController::class, 'reboot'])->name('reboot');
        Route::get('/routes', [App\Http\Controllers\DiagnosticsController::class, 'routes'])->name('routes');
        Route::get('/smart-status', [App\Http\Controllers\DiagnosticsController::class, 'smartStatus'])->name('smart-status');
        Route::get('/sockets', [App\Http\Controllers\DiagnosticsController::class, 'sockets'])->name('sockets');
        Route::get('/states', [App\Http\Controllers\DiagnosticsController::class, 'states'])->name('states');
        Route::get('/states-summary', [App\Http\Controllers\DiagnosticsController::class, 'statesSummary'])->name('states-summary');
        Route::get('/system-activity', [App\Http\Controllers\DiagnosticsController::class, 'systemActivity'])->name('system-activity');
        Route::get('/tables', [App\Http\Controllers\DiagnosticsController::class, 'tables'])->name('tables');
        Route::get('/test-port', [App\Http\Controllers\DiagnosticsController::class, 'testPort'])->name('test-port');
        Route::get('/traceroute', [App\Http\Controllers\DiagnosticsController::class, 'traceroute'])->name('traceroute');
    });


});





require __DIR__ . '/auth.php';
