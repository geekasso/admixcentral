<?php
try {
    $content = file_get_contents('openapi.json');
    if ($content === false) {
        file_put_contents('debug_output.txt', 'Failed to read file');
    } else {
        $paths = [];
        $data = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            file_put_contents('debug_output.txt', 'JSON Decode Error: ' . json_last_error_msg());
        } else {
            if (isset($data['paths'])) {
                foreach (array_keys($data['paths']) as $path) {
                    if (strpos($path, 'system') !== false || strpos($path, 'status') !== false) {
                        $paths[] = $path;
                    }
                }
                file_put_contents('debug_output.txt', implode("\n", $paths));
            } else {
                file_put_contents('debug_output.txt', 'No paths found');
            }
        }
    }
} catch (Exception $e) {
    file_put_contents('debug_output.txt', 'Exception: ' . $e->getMessage());
}
