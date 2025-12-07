<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create Demo Company
        $company = \App\Models\Company::create([
            'name' => 'Demo Company',
            'description' => 'A demo company for testing.',
        ]);

        // Create Global Admin
        \App\Models\User::create([
            'name' => 'Global Admin',
            'email' => 'admin@admixcentral.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create Company User
        \App\Models\User::create([
            'name' => 'Company User',
            'email' => 'user@demo.com',
            'password' => bcrypt('password'),
            'company_id' => $company->id,
            'role' => 'user',
        ]);


    }
}
