<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://172.30.1.129:444/api/v2/status/interfaces");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_USERPWD, "admin:pfsense");
$output = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

$result = "HTTP Code: " . $info['http_code'] . "\n";
$result .= "Output: " . substr($output, 0, 1000) . "\n"; // Truncate output
file_put_contents('curl_output.txt', $result);
