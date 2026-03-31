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
        // Use SafeProductionSeeder for production deployments
        // It includes all necessary seeders in a safe manner
        $this->call([
            SafeProductionSeeder::class,
        ]);
    }
}
