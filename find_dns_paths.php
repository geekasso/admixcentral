<?php
$json = file_get_contents('/home/baga/Code/admixcentral/openapi.json');
$data = json_decode($json, true);

if (isset($data['paths'])) {
    foreach ($data['paths'] as $path => $details) {
        if (strpos($path, 'dns') !== false) {
            echo "Path: $path\n";
        }
    }
}
