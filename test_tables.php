<?php

function log_msg($msg)
{
    file_put_contents('test_tables_output.txt', $msg . "\n", FILE_APPEND);
}

file_put_contents('test_tables_output.txt', "Starting test_tables.php...\n");

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Firewall;
use App\Services\PfSenseApiService;

$firewall = Firewall::first();
if (!$firewall) {
    log_msg("No firewall found.");
    exit(1);
}

log_msg("Firewall found: " . $firewall->id);

$api = new PfSenseApiService($firewall);

try {
    log_msg("Fetching tables...");
    $tables = $api->getDiagnosticsTables();
    // log_msg("Tables fetched: " . print_r($tables, true));

    log_msg("Fetching table LAN__NETWORK...");
    $tableContent = $api->getDiagnosticsTable('LAN__NETWORK');
    log_msg("Table Content fetched: " . print_r($tableContent, true));
} catch (Exception $e) {
    log_msg("Error: " . $e->getMessage());
}
