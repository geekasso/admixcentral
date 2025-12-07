<?php
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Log;

try {
    echo "Simulating Upload...\n";
    $dummyFile = UploadedFile::fake()->image('simulated_logo.png');

    // Manually store
    $path = $dummyFile->store('public/customization');
    echo "Stored at: $path\n";

    // Update DB
    $url = Storage::url($path);
    echo "Generated URL: $url\n";

    SystemSetting::updateOrCreate(['key' => 'logo_path'], ['value' => $url]);
    echo "DB Updated.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
