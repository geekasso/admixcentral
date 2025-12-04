<?php

namespace App\Services;

use App\Models\Firewall;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PfSenseApiService
{
    protected $firewall;
    protected $baseUrl;
    protected $username;
    protected $password;

    public function __construct(Firewall $firewall)
    {
        $this->firewall = $firewall;
        $this->baseUrl = rtrim($firewall->url, '/') . '/api/v2';
        $this->username = $firewall->api_key;
        $this->password = $firewall->api_secret;
    }

    /**
     * Make a GET request to the pfSense API
     */
    public function get(string $endpoint, array $params = [])
    {
        return $this->request('GET', $endpoint, $params);
    }

    /**
     * Make a POST request to the pfSense API
     */
    public function post(string $endpoint, array $data = [])
    {
        return $this->request('POST', $endpoint, $data);
    }

    /**
     * Make a PUT request to the pfSense API
     */
    public function put(string $endpoint, array $data = [])
    {
        return $this->request('PUT', $endpoint, $data);
    }

    /**
     * Make a PATCH request to the pfSense API
     */
    public function patch(string $endpoint, array $data = [])
    {
        return $this->request('PATCH', $endpoint, $data);
    }

    /**
     * Make a DELETE request to the pfSense API
     */
    public function delete(string $endpoint, array $data = [])
    {
        return $this->request('DELETE', $endpoint, $data);
    }

    /**
     * Make a request to the pfSense API
     */
    protected function request(string $method, string $endpoint, array $data = [])
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        $client = Http::withOptions(['verify' => false])
            ->withBasicAuth($this->username, $this->password)
            ->acceptJson()
            ->timeout(30);

        if ($method === 'DELETE' && !empty($data)) {
            $response = $client->send('DELETE', $url, ['json' => $data]);
        } elseif ($method === 'GET') {
            $queryString = http_build_query($data);
            $fullUrl = $queryString ? $url . '?' . $queryString : $url;
            $response = $client->get($fullUrl);
        } else {
            $response = $client->asJson()->$method($url, $data);
        }

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception("API request failed: " . $response->body(), $response->status());
    }

    /**
     * Get system status - v2 API doesn't have /system/status endpoint
     * Return basic connection test instead
     */
    /**
     * Get system status
     */
    public function getSystemStatus()
    {
        return $this->get('/status/system');
    }

    public function getSystemVersion()
    {
        return $this->get('/system/version');
    }

    public function getSystemHostname()
    {
        return $this->get('/system/hostname');
    }

    public function updateSystemHostname(array $data)
    {
        return $this->patch('/system/hostname', $data);
    }

    public function getSystemTimezone()
    {
        return $this->get('/system/timezone');
    }

    public function updateSystemTimezone(array $data)
    {
        return $this->patch('/system/timezone', $data);
    }

    public function getSystemDns()
    {
        return $this->get('/system/dns');
    }

    public function updateSystemDns(array $data)
    {
        return $this->patch('/system/dns', $data);
    }

    // System Advanced - Admin Access
    public function getSystemWebGui()
    {
        return $this->get('/system/webgui/settings');
    }

    public function updateSystemWebGui(array $data)
    {
        return $this->patch('/system/webgui/settings', $data);
    }

    public function getSystemSsh()
    {
        return $this->get('/services/ssh');
    }

    public function updateSystemSsh(array $data)
    {
        return $this->patch('/services/ssh', $data);
    }

    public function getSystemConsole()
    {
        return $this->get('/system/console');
    }

    public function updateSystemConsole(array $data)
    {
        return $this->patch('/system/console', $data);
    }

    // System Advanced - Firewall & NAT
    public function getSystemFirewallAdvanced()
    {
        return $this->get('/firewall/advanced_settings');
    }

    public function updateSystemFirewallAdvanced(array $data)
    {
        return $this->patch('/firewall/advanced_settings', $data);
    }

    // System Advanced - Notifications
    public function getSystemNotifications()
    {
        return $this->get('/system/notifications/email_settings');
    }

    public function updateSystemNotifications(array $data)
    {
        return $this->patch('/system/notifications/email_settings', $data);
    }

    // System Advanced - Tunables
    public function getSystemTunables()
    {
        return $this->get('/system/tunables');
    }

    public function createSystemTunable(array $data)
    {
        return $this->post('/system/tunable', $data);
    }

    public function updateSystemTunable(array $data)
    {
        return $this->patch('/system/tunable', $data);
    }

    public function deleteSystemTunable(string $id)
    {
        return $this->delete('/system/tunable', ['id' => $id]);
    }

    // Routing - Gateways
    public function getRoutingGateways()
    {
        return $this->get('/routing/gateways');
    }

    public function createRoutingGateway(array $data)
    {
        return $this->post('/routing/gateway', $data);
    }

    public function updateRoutingGateway(array $data)
    {
        return $this->patch('/routing/gateway', $data);
    }

    public function deleteRoutingGateway(string $id)
    {
        return $this->delete('/routing/gateway', ['id' => $id]);
    }

    // Routing - Static Routes
    public function getRoutingStaticRoutes()
    {
        return $this->get('/routing/static_routes');
    }

    public function createRoutingStaticRoute(array $data)
    {
        return $this->post('/routing/static_route', $data);
    }

    public function updateRoutingStaticRoute(array $data)
    {
        return $this->patch('/routing/static_route', $data);
    }

    public function deleteRoutingStaticRoute(string $id)
    {
        return $this->delete('/routing/static_route', ['id' => $id]);
    }

    // Routing - Gateway Groups
    public function getRoutingGatewayGroups()
    {
        return $this->get('/routing/gateway/groups');
    }

    public function createRoutingGatewayGroup(array $data)
    {
        return $this->post('/routing/gateway/group', $data);
    }

    public function updateRoutingGatewayGroup(array $data)
    {
        return $this->patch('/routing/gateway/group', $data);
    }

    public function deleteRoutingGatewayGroup(string $id)
    {
        return $this->delete('/routing/gateway/group', ['id' => $id]);
    }

    // System - Package Manager
    public function getSystemPackages()
    {
        return $this->get('/system/packages');
    }

    public function getSystemAvailablePackages()
    {
        return $this->get('/system/package/available');
    }

    public function installSystemPackage(string $name)
    {
        return $this->post('/system/package', ['name' => $name]);
    }

    public function uninstallSystemPackage(string $name)
    {
        return $this->delete('/system/package', ['name' => $name]);
    }

    // System - User Manager - Users
    public function getSystemUsers()
    {
        return $this->get('/users');
    }

    public function createSystemUser(array $data)
    {
        return $this->post('/user', $data);
    }

    public function updateSystemUser(array $data)
    {
        return $this->patch('/user', $data);
    }

    public function deleteSystemUser(string $id)
    {
        return $this->delete('/user', ['id' => $id]);
    }

    // System - User Manager - Groups
    public function getSystemGroups()
    {
        return $this->get('/user/groups');
    }

    public function createSystemGroup(array $data)
    {
        return $this->post('/user/group', $data);
    }

    public function updateSystemGroup(array $data)
    {
        return $this->patch('/user/group', $data);
    }

    public function deleteSystemGroup(string $id)
    {
        return $this->delete('/user/group', ['id' => $id]);
    }

    // System - User Manager - Auth Servers
    public function getSystemAuthServers()
    {
        return $this->get('/user/auth_servers');
    }

    public function createSystemAuthServer(array $data)
    {
        return $this->post('/user/auth_server', $data);
    }

    public function updateSystemAuthServer(array $data)
    {
        return $this->patch('/user/auth_server', $data);
    }

    public function deleteSystemAuthServer(string $id)
    {
        return $this->delete('/user/auth_server', ['id' => $id]);
    }


    /**
     * Get gateways status
     */
    public function getGateways()
    {
        return $this->get('/status/gateways');
    }

    /**
     * Get interfaces
     */
    public function getInterfaces()
    {
        return $this->get('/interfaces');
    }

    /**
     * Get interfaces status
     */
    /**
     * Get interfaces status
     */
    public function getInterfacesStatus()
    {
        return $this->get('/status/interfaces');
    }

    /**
     * Get available interfaces (ports)
     */
    public function getAvailableInterfaces()
    {
        return $this->get('/interface/available_interfaces');
    }

    /**
     * Create Interface (Assign)
     */
    public function createInterface(array $data)
    {
        return $this->post('/interface', $data);
    }

    /**
     * Delete Interface (Unassign)
     */
    public function deleteInterface(string $id)
    {
        return $this->delete('/interface', ['id' => $id]);
    }



    /**
     * Get firewall rules
     */
    public function getFirewallRules()
    {
        return $this->get('/firewall/rules');
    }

    public function createFirewallRule(array $data)
    {
        // Ensure interface is an array
        if (isset($data['interface']) && !is_array($data['interface'])) {
            $data['interface'] = [$data['interface']];
        }

        Log::info('Creating firewall rule payload:', $data);

        return $this->post('/firewall/rule', $data);
    }

    public function updateFirewallRule(int $id, array $data)
    {
        $data['id'] = $id;
        return $this->patch("/firewall/rule", $data);
    }

    public function deleteFirewallRule(int $id)
    {
        return $this->delete("/firewall/rule?id={$id}", ['id' => $id]);
    }

    public function updateInterface(string $id, array $data)
    {
        // The API requires PATCH for updates and expects 'id' in the body
        $data['id'] = $id;
        return $this->patch("/interface", $data);
    }

    public function applyChanges()
    {
        return $this->post('/firewall/apply');
    }

    /**
     * Get services status
     */
    public function getServices()
    {
        return $this->get('/services');
    }

    /**
     * Get NAT Port Forwards
     */
    public function getNatPortForwards()
    {
        return $this->get('/firewall/nat/port_forwards');
    }

    /**
     * Create NAT Port Forward
     */
    public function createNatPortForward(array $data)
    {
        return $this->post('/firewall/nat/port_forward', $data);
    }

    /**
     * Update NAT Port Forward
     */
    public function updateNatPortForward(int $id, array $data)
    {
        $data['id'] = $id;
        return $this->patch("/firewall/nat/port_forward", $data);
    }

    /**
     * Delete NAT Port Forward
     */
    public function deleteNatPortForward(int $id)
    {
        return $this->delete("/firewall/nat/port_forward?id={$id}", ['id' => $id]);
    }

    /**
     * Get NAT Outbound Rules
     */
    public function getNatOutboundRules()
    {
        return $this->get('/firewall/nat/outbound/mappings');
    }

    /**
     * Get NAT Outbound Mode
     */
    public function getNatOutboundMode()
    {
        return $this->get('/firewall/nat/outbound/mode');
    }

    /**
     * Update NAT Outbound Mode
     */
    public function updateNatOutboundMode(string $mode)
    {
        return $this->patch('/firewall/nat/outbound/mode', ['mode' => $mode]);
    }

    /**
     * Create NAT Outbound Rule
     */
    public function createNatOutboundRule(array $data)
    {
        return $this->post('/firewall/nat/outbound/mapping', $data);
    }

    /**
     * Update NAT Outbound Rule
     */
    public function updateNatOutboundRule(int $id, array $data)
    {
        $data['id'] = $id;
        return $this->patch("/firewall/nat/outbound/mapping", $data);
    }

    /**
     * Delete NAT Outbound Rule
     */
    public function deleteNatOutboundRule(int $id)
    {
        return $this->delete("/firewall/nat/outbound/mapping?id={$id}");
    }

    /**
     * Get NAT 1:1 Rules
     */
    public function getNatOneToOneRules()
    {
        return $this->get('/firewall/nat/one_to_one/mappings');
    }

    /**
     * Create NAT 1:1 Rule
     */
    public function createNatOneToOneRule(array $data)
    {
        return $this->post('/firewall/nat/one_to_one/mapping', $data);
    }

    /**
     * Update NAT 1:1 Rule
     */
    public function updateNatOneToOneRule(int $id, array $data)
    {
        $data['id'] = $id;
        return $this->patch("/firewall/nat/one_to_one/mapping", $data);
    }

    /**
     * Delete NAT 1:1 Rule
     */
    public function deleteNatOneToOneRule(int $id)
    {
        return $this->delete("/firewall/nat/one_to_one/mapping?id={$id}");
    }

    /**
     * Get Firewall Schedules
     */
    public function getSchedules()
    {
        return $this->get('/firewall/schedules');
    }

    /**
     * Create Firewall Schedule
     */
    public function createSchedule(array $data)
    {
        return $this->post('/firewall/schedule', $data);
    }

    /**
     * Update Firewall Schedule
     */
    public function updateSchedule(int $id, array $data)
    {
        $data['id'] = $id;
        return $this->patch("/firewall/schedule?id={$id}", $data);
    }

    /**
     * Delete Firewall Schedule
     */
    public function deleteSchedule(int $id)
    {
        return $this->delete("/firewall/schedule", ['id' => $id]);
    }

    /**
     * Get Traffic Shaper Limiters
     */
    public function getLimiters()
    {
        return $this->get('/firewall/traffic_shaper/limiters');
    }

    /**
     * Create Traffic Shaper Limiter
     */
    public function createLimiter(array $data)
    {
        return $this->post('/firewall/traffic_shaper/limiter', $data);
    }

    /**
     * Update Traffic Shaper Limiter
     */
    public function updateLimiter(int $id, array $data)
    {
        $data['id'] = $id;
        return $this->patch("/firewall/traffic_shaper/limiter", $data);
    }

    /**
     * Delete Traffic Shaper Limiter
     */
    public function deleteLimiter(int $id)
    {
        return $this->delete("/firewall/traffic_shaper/limiter", ['id' => $id]);
    }

    /**
     * Get Virtual IPs
     */
    public function getVirtualIps()
    {
        return $this->get('/firewall/virtual_ips');
    }

    /**
     * Create Virtual IP
     */
    public function createVirtualIp(array $data)
    {
        return $this->post('/firewall/virtual_ip', $data);
    }

    /**
     * Update Virtual IP
     */
    public function updateVirtualIp(int $id, array $data)
    {
        return $this->patch("/firewall/virtual_ip?id={$id}", $data);
    }

    /**
     * Delete Virtual IP
     */
    public function deleteVirtualIp(int $id)
    {
        return $this->delete("/firewall/virtual_ip", ['id' => $id]);
    }
    /**
     * Get VLANs
     */
    public function getVlans()
    {
        return $this->get('/interface/vlans');
    }

    /**
     * Create VLAN
     */
    public function createVlan(array $data)
    {
        return $this->post('/interface/vlan', $data);
    }

    /**
     * Update VLAN
     */
    public function updateVlan(int $id, array $data)
    {
        return $this->patch("/interface/vlan?id={$id}", $data);
    }

    /**
     * Delete VLAN
     */
    public function deleteVlan(int $id)
    {
        return $this->delete("/interface/vlan", ['id' => $id]);
    }

    /**
     * Get OpenVPN Servers
     */
    public function getOpenVpnServers()
    {
        return $this->get('/vpn/openvpn/servers');
    }

    /**
     * Get OpenVPN Clients
     */
    public function getOpenVpnClients()
    {
        return $this->get('/vpn/openvpn/clients');
    }

    /**
     * Create OpenVPN Server
     */
    public function createOpenVpnServer(array $data)
    {
        return $this->post('/vpn/openvpn/server', $data);
    }

    /**
     * Create OpenVPN Client
     */
    public function createOpenVpnClient(array $data)
    {
        return $this->post('/vpn/openvpn/client', $data);
    }

    /**
     * Delete OpenVPN Server
     */
    public function deleteOpenVpnServer(int $id)
    {
        return $this->delete("/vpn/openvpn/server", ['id' => $id]);
    }

    /**
     * Delete OpenVPN Client
     */
    public function deleteOpenVpnClient(int $id)
    {
        return $this->delete("/vpn/openvpn/client", ['id' => $id]);
    }

    /**
     * Get Certificate Authorities
     */
    public function getCertificateAuthorities()
    {
        return $this->get('/system/certificate_authorities');
    }

    /**
     * Get Certificates
     */
    public function getCertificates()
    {
        return $this->get('/system/certificates');
    }

    /**
     * Create Certificate Authority (Import)
     */
    public function createCertificateAuthority(array $data)
    {
        return $this->post('/system/certificate_authority', $data);
    }

    /**
     * Generate Certificate Authority (Internal)
     */
    public function generateCertificateAuthority(array $data)
    {
        return $this->post('/system/certificate_authority/generate', $data);
    }

    /**
     * Create Certificate (Import)
     */
    public function createCertificate(array $data)
    {
        return $this->post('/system/certificate', $data);
    }

    /**
     * Get IPsec Phase 1s
     */
    public function getIpsecPhase1s()
    {
        return $this->get('/vpn/ipsec/phase1s');
    }

    /**
     * Get IPsec Phase 2s
     */
    public function getIpsecPhase2s()
    {
        return $this->get('/vpn/ipsec/phase2s');
    }

    /**
     * Create IPsec Phase 1
     */
    public function createIpsecPhase1(array $data)
    {
        return $this->post('/vpn/ipsec/phase1', $data);
    }

    /**
     * Delete IPsec Phase 1
     */
    public function deleteIpsecPhase1(int $id)
    {
        return $this->delete("/vpn/ipsec/phase1", ['ikeid' => $id]);
    }

    /**
     * Create IPsec Phase 2
     */
    public function createIpsecPhase2(array $data)
    {
        return $this->post('/vpn/ipsec/phase2', $data);
    }

    /**
     * Delete IPsec Phase 2
     */
    public function deleteIpsecPhase2(int $id)
    {
        return $this->delete("/vpn/ipsec/phase2", ['uniqid' => $id]);
    }

    /**
     * Get DHCP Relay
     */
    public function getDhcpRelay()
    {
        return $this->get('/services/dhcp_relay');
    }

    /**
     * Update DHCP Relay
     */
    public function updateDhcpRelay(array $data)
    {
        return $this->patch('/services/dhcp_relay', $data);
    }



    /**
     * Generate Certificate (Internal)
     */
    public function generateCertificate(array $data)
    {
        return $this->post('/system/certificate/generate', $data);
    }

    /**
     * Delete Certificate Authority
     */
    public function deleteCertificateAuthority(string $id)
    {
        return $this->delete("/system/certificate_authority", ['id' => $id]);
    }

    /**
     * Delete Certificate
     */
    public function deleteCertificate(string $id)
    {
        return $this->delete("/system/certificate", ['id' => $id]);
    }

    /**
     * Execute a shell command
     */
    public function commandPrompt(string $command)
    {
        return $this->post('/diagnostics/command_prompt', ['command' => $command]);
    }

    /**
     * Get DNS Resolver Configuration
     */
    public function getDnsResolver()
    {
        return $this->get('/services/dns_resolver/settings');
    }

    /**
     * Update DNS Resolver Configuration
     */
    public function updateDnsResolver(array $data)
    {
        return $this->patch('/services/dns_resolver/settings', $data);
    }

    /**
     * Get DNS Resolver Host Overrides
     */
    public function getDnsResolverHostOverrides()
    {
        return $this->get('/services/dns_resolver/host_overrides');
    }

    /**
     * Create DNS Resolver Host Override
     */
    public function createDnsResolverHostOverride(array $data)
    {
        return $this->post('/services/dns_resolver/host_override', $data);
    }

    /**
     * Get DHCP Server Configuration
     */
    public function getDhcpServer(string $interface)
    {
        return $this->get('/services/dhcp_server', ['id' => $interface]);
    }

    /**
     * Update DHCP Server Configuration
     */
    public function updateDhcpServer(array $data)
    {
        return $this->patch('/services/dhcp_server', $data);
    }
}
