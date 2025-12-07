<?php
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\SystemSetting;

try {
    echo "Simulating Real Upload (Hashed)...\n";
    $dummyFile = UploadedFile::fake()->image('real_upload_simulation.png');

    // Use default hashing behavior (no filename arg)
    $path = $dummyFile->store('public/customization');
    echo "Stored at path: $path\n";

    $fullPath = storage_path('app/' . $path);
    echo "Absolute path: $fullPath\n";

    if (file_exists($fullPath)) {
        echo "File EXISTS.\n";
        echo "Perms: " . substr(sprintf('%o', fileperms($fullPath)), -4) . "\n";
    } else {
        echo "File MISSING.\n";
    }

    $url = Storage::url($path);
    echo "Generated URL: $url\n";

    // Update DB to point to this new hashed file
    SystemSetting::updateOrCreate(['key' => 'logo_path'], ['value' => $url]);
    echo "DB Updated to: $url\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
