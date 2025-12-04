<?php

$handle = fopen("openapi.json", "r");
if ($handle) {
    $buffer = fread($handle, 1024); // Read first 1KB
    echo "First 1KB:\n" . $buffer . "\n";
    fclose($handle);
} else {
    echo "Failed to open file.\n";
}
