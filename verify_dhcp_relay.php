<?php

$cookieJar = '/tmp/cookies.txt';
if (file_exists($cookieJar))
    unlink($cookieJar);

// 1. Get CSRF Token
$ch = curl_init('http://localhost:8001/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieJar);
$html = curl_exec($ch);

if (preg_match('/name="_token" value="([^"]+)"/', $html, $matches)) {
    $token = $matches[1];
    echo "Token: " . $token . "\n";
} else {
    die("Failed to get CSRF token\n");
}

// 2. Login
$postData = [
    '_token' => $token,
    'email' => 'admin@admixcenter.com',
    'password' => 'password',
];

curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);

if (strpos($response, 'Dashboard') !== false) {
    echo "Login Successful\n";
} else {
    echo "Login Failed\n";
    exit(1);
}

// 3. Get DHCP Relay Page (Initial State)
echo "Fetching DHCP Relay Page...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/firewall/1/services/dhcp-relay');
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);

if (strpos($response, 'DHCP Relay') !== false) {
    echo "DHCP Relay Page Loaded\n";
} else {
    echo "Failed to load DHCP Relay Page\n";
    exit(1);
}

// Extract CSRF token from the page for the form submission
if (preg_match('/name="_token" value="([^"]+)"/', $response, $matches)) {
    $token = $matches[1];
}

// 4. Update DHCP Relay Settings
echo "Updating DHCP Relay Settings...\n";
$updateData = [
    '_token' => $token,
    'enable' => 'yes',
    'interface' => ['lan'],
    'server' => '1.1.1.1, 8.8.8.8',
    'agentoption' => 'yes'
];

curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/firewall/1/services/dhcp-relay');
curl_setopt($ch, CURLOPT_POST, true);
// Use http_build_query to handle array parameters correctly
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($updateData));
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Add timeout
$response = curl_exec($ch);

if (strpos($response, 'DHCP Relay settings updated') !== false) {
    echo "Update Successful\n";
} else {
    echo "Update Failed\n";
    // echo substr(strip_tags($response), 0, 500) . "\n";
}

// 5. Verify Persistence (Get Page Again)
echo "Verifying Persistence...\n";
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/firewall/1/services/dhcp-relay');
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);

if (strpos($response, '1.1.1.1, 8.8.8.8') !== false) {
    echo "Persistence Verified: Server IP found\n";
} else {
    echo "Persistence Failed: Server IP not found\n";
}

curl_close($ch);
