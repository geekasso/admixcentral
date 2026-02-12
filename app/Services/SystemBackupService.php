<?php

namespace App\Services;

use App\Models\Company;
use App\Models\DeviceConnection;
use App\Models\Firewall;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class SystemBackupService
{
    protected $encryptionMethod = 'AES-256-CBC';

    /**
     * Create a system backup.
     *
     * @param string $password
     * @return string Filename of the created backup
     * @throws \Exception
     */
    public function createBackup(string $password): string
    {
        $data = $this->gatherSystemData();
        $jsonContent = json_encode($data);

        if ($jsonContent === false) {
            throw new \Exception("Failed to encode system data to JSON.");
        }

        $encryptedContent = $this->encryptData($jsonContent, $password);

        $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.json.enc';
        Storage::put('backups/' . $filename, $encryptedContent);

        return $filename;
    }

    /**
     * Restore system from a backup file path.
     *
     * @param string $path Absolute path to the backup file
     * @param string $password
     * @throws \Exception
     */
    public function restoreFromPath(string $path, string $password): void
    {
        if (!file_exists($path)) {
            throw new \Exception("Backup file not found at path: $path");
        }

        $encryptedContent = file_get_contents($path);
        $this->restoreFromContent($encryptedContent, $password);
    }

    /**
     * Restore system from raw content.
     *
     * @param string $encryptedContent
     * @param string $password
     * @throws \Exception
     */
    public function restoreFromContent(string $encryptedContent, string $password): void
    {
        $jsonContent = $this->decryptData($encryptedContent, $password);
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Failed to decode JSON data: " . json_last_error_msg());
        }

        $this->restoreSystemData($data);
    }

    protected function gatherSystemData(): array
    {
        return [
            'version' => '1.0', // Schema version
            'timestamp' => now()->toIso8601String(),
            'companies' => Company::all()->makeVisible(['api_token'])->toArray(),
            'users' => User::all()->makeVisible(['password', 'remember_token'])->toArray(), // Include password hashes
            'firewalls' => Firewall::all()->makeVisible(['api_key', 'api_secret', 'api_token'])->toArray(),
            'system_settings' => SystemSetting::all()->toArray(),
            'device_connections' => DeviceConnection::all()->toArray(),
        ];
    }

    protected function restoreSystemData(array $data)
    {
        DB::transaction(function () use ($data) {
            // Disable foreign key checks to avoid constraint violations during truncate
            Schema::disableForeignKeyConstraints();

            // 1. Restore Companies
            if (isset($data['companies'])) {
                Company::truncate();
                foreach ($data['companies'] as $record) {
                    Company::create($record);
                }
            }

            // 2. Restore Users
            if (isset($data['users'])) {
                User::truncate();
                foreach ($data['users'] as $record) {
                    // Start manually inserting to preserve IDs and password hashes
                    // Eloquent create() might re-hash passwords if there's a mutator, but typically explicit fill is safe.
                    // However, for Users, we want to be careful.
                    // Let's use forceCreate to avoid mass assignment protection issues if any.
                    User::forceCreate($record);
                }
            }

            // 3. Restore Firewalls
            if (isset($data['firewalls'])) {
                Firewall::truncate();
                foreach ($data['firewalls'] as $record) {
                    // Important: The 'encrypted' casted columns (api_key, etc) were decrypted on export.
                    // When we use generic create or forceCreate with the raw plain values, 
                    // the model's 'encrypted' cast SHOULD automatically encrypt them with the NEW app key.
                    // We must ensure 'api_key', 'api_secret' are passed as plain text here, which they are from json_decode.
                    Firewall::forceCreate($record);
                }
            }

            // 4. Restore System Settings
            if (isset($data['system_settings'])) {
                SystemSetting::truncate();
                foreach ($data['system_settings'] as $record) {
                    SystemSetting::forceCreate($record);
                }
            }

            // 5. Restore Device Connections
            if (isset($data['device_connections'])) {
                DeviceConnection::truncate();
                foreach ($data['device_connections'] as $record) {
                    DeviceConnection::forceCreate($record);
                }
            }

            Schema::enableForeignKeyConstraints();
        });
    }

    protected function encryptData(string $data, string $password): string
    {
        $salt = openssl_random_pseudo_bytes(16);
        $key = hash_pbkdf2("sha256", $password, $salt, 10000, 32, true);
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, $this->encryptionMethod, $key, 0, $iv);

        // Combine salt, iv, and encrypted data
        return base64_encode($salt . $iv . $encrypted);
    }

    protected function decryptData(string $data, string $password): string
    {
        $data = base64_decode($data);
        $salt = substr($data, 0, 16);
        $iv = substr($data, 16, 16);
        $encrypted = substr($data, 32);

        $key = hash_pbkdf2("sha256", $password, $salt, 10000, 32, true);
        $decrypted = openssl_decrypt($encrypted, $this->encryptionMethod, $key, 0, $iv);

        if ($decrypted === false) {
            throw new \Exception("Decryption failed. Incorrect password or corrupted file.");
        }

        return $decrypted;
    }
}
