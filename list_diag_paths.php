<?php
$json = file_get_contents('/home/baga/Code/admixcenter/openapi.json');
$data = json_decode($json, true);

if (isset($data['paths'])) {
    foreach ($data['paths'] as $path => $details) {
        if (strpos($path, '/diagnostics') !== false) {
            echo "Path: $path\n";
        }
    }
}
