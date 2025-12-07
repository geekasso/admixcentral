<?php
echo "Deep Debugging Logo Round 2...\n";

$path = __DIR__ . '/storage/app/public/customization/test_logo.png';
if (file_exists($path)) {
    echo "File EXISTS.\n";
    echo "Size: " . filesize($path) . " bytes\n";
    echo "Perms: " . substr(sprintf('%o', fileperms($path)), -4) . "\n";
    echo "Owner: " . fileowner($path) . "\n";
    echo "Mime: " . mime_content_type($path) . "\n";
} else {
    echo "File MISSING at $path\n";
}
