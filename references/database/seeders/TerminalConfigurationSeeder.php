<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TerminalConfiguration;

class TerminalConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $configurations = [
            ['operation_type' => 'decantation', 'terminal_ids' => [3]],
            ['operation_type' => 'toploading', 'terminal_ids' => [1, 2]],
            ['operation_type' => 'fuel_dispensing', 'terminal_ids' => [4, 5, 6]],
        ];

        foreach ($configurations as $config) {
            TerminalConfiguration::firstOrCreate(
                ['operation_type' => $config['operation_type']],
                ['terminal_ids' => $config['terminal_ids']]
            );
        }
    }
}