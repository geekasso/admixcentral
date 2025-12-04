<?php
$logFile = 'verify_aliases.log';
$outputFile = 'verify_aliases_output.txt';

if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    file_put_contents($outputFile, $content);
    echo "Log copied to $outputFile\n";
} else {
    file_put_contents($outputFile, "Log file $logFile not found.");
    echo "Log file not found\n";
}
