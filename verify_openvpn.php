<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Firewall;
use App\Services\PfSenseApiService;
use Illuminate\Support\Facades\Log;

// Get the first firewall
$firewall = Firewall::first();

if (!$firewall) {
    echo "No firewall found.\n";
    exit(1);
}

echo "Using firewall: {$firewall->name} ({$firewall->url})\n";

$api = new PfSenseApiService($firewall);

try {
    // 1. List OpenVPN Servers
    echo "\n1. Listing OpenVPN Servers...\n";
    $servers = $api->getOpenVpnServers();
    echo "Found " . count($servers['data'] ?? []) . " servers.\n";
    // print_r($servers);

    // Fetch CAs and Certs
    echo "\nFetching CAs and Certs...\n";
    $cas = $api->getCertificateAuthorities();
    $certs = $api->getCertificates();

    $caref = $cas['data'][0]['refid'] ?? '';

    if (empty($caref)) {
        echo "No CA found. Uploading local CA...\n";

        $certsEnv = parse_ini_file(__DIR__ . '/certs.env');
        if (!$certsEnv) {
            throw new Exception("Failed to read certs.env");
        }

        $caData = [
            'descr' => 'Test CA',
            'trust' => true,
            'crt' => base64_decode($certsEnv['CA_CRT']),
            'prv' => base64_decode($certsEnv['CA_KEY']),
            'randomserial' => true,
        ];
        $ca = $api->createCertificateAuthority($caData);
        $caref = $ca['data']['refid'] ?? '';
        echo "Created CA ref: $caref\n";
    } else {
        echo "Using CA ref: $caref\n";
    }

    // We need a server cert signed by this CA
    // Check if we have a cert signed by this CA (simplification: just check if we have any cert, if not create one)
    $certref = $certs['data'][0]['refid'] ?? '';

    if (empty($certref)) {
        echo "No Certificate found. Uploading local Cert...\n";

        if (!isset($certsEnv)) {
            $certsEnv = parse_ini_file(__DIR__ . '/certs.env');
        }

        $certData = [
            'descr' => 'Test Server Cert',
            'caref' => $caref,
            'crt' => base64_decode($certsEnv['SERVER_CRT']),
            'prv' => base64_decode($certsEnv['SERVER_KEY']),
            'type' => 'server',
        ];
        $cert = $api->createCertificate($certData);
        $certref = $cert['data']['refid'] ?? '';
        echo "Created Cert ref: $certref\n";
    } else {
        echo "Using Cert ref: $certref\n";
    }

    // 2. Create OpenVPN Server
    echo "\n2. Creating Test OpenVPN Server...\n";
    $serverData = [
        'mode' => 'server_tls',
        'protocol' => 'UDP4',
        'dev_mode' => 'tun',
        'interface' => 'wan',
        'local_port' => '1195', // Use a different port
        'tunnel_network' => '10.0.9.0/24',
        'description' => 'Test OpenVPN Server',
        'tls' => '',
        'dh_length' => '2048',
        'ecdh_curve' => 'none',
        'data_ciphers' => ['AES-256-GCM'],
        'data_ciphers_fallback' => 'AES-256-GCM',
        'digest' => 'SHA256',
        'certref' => $certref,
        'caref' => $caref,
    ];

    // We might need a CA and Cert. Let's list them first if creation fails.
    // For now, let's try to create and see if it fails validation.

    // To make this robust, we should probably fetch a valid CA and Cert if required.
    // But let's try basic creation first.

    try {
        $createdServer = $api->createOpenVpnServer($serverData);
        echo "Server created.\n";
        print_r($createdServer);
    } catch (\Exception $e) {
        echo "Creation failed (might already exist): " . $e->getMessage() . "\n";
    }

    // 3. List Again to Verify
    echo "\n3. Verifying Server in List...\n";
    $servers = $api->getOpenVpnServers();
    $found = false;
    foreach ($servers['data'] as $s) {
        if (($s['description'] ?? '') === 'Test OpenVPN Server') {
            $found = true;
            print_r($s);
            // Use 'id' if available, otherwise 'vpnid'
            $serverId = isset($s['id']) ? $s['id'] : ($s['vpnid'] ?? null);
            echo "Found created server with ID: $serverId\n";
            break;
        }
    }

    if (!$found) {
        echo "FAILED: Created server not found in list.\n";
    }

    // 4. Delete Server
    if ($found && isset($serverId)) {
        echo "\n4. Deleting Server...\n";
        $api->deleteOpenVpnServer($serverId);
        echo "Server deleted.\n";
    }

    // 5. List OpenVPN Clients
    echo "\n5. Listing OpenVPN Clients...\n";
    $clients = $api->getOpenVpnClients();
    echo "Found " . count($clients['data'] ?? []) . " clients.\n";
    // print_r($clients);

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (method_exists($e, 'getResponse')) {
        echo "Response: " . $e->getResponse()->getBody() . "\n";
    }
}
