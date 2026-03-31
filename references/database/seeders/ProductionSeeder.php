<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions (same as development)
        $permissions = [
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard'],
            ['name' => 'edit_dashboard', 'display_name' => 'Edit Dashboard Settings'],
            ['name' => 'cards.view', 'display_name' => 'View Cards'],
            ['name' => 'cards.export', 'display_name' => 'Export Cards'],
            ['name' => 'vehicles.view', 'display_name' => 'View Vehicles'],
            ['name' => 'vehicles.export', 'display_name' => 'Export Vehicles'],
            ['name' => 'transactions.view', 'display_name' => 'View Transactions'],
            ['name' => 'transactions.export', 'display_name' => 'Export Transactions'],
            ['name' => 'update_mileage', 'display_name' => 'Edit Mileage in Transactions'],
            ['name' => 'reconciliations.view', 'display_name' => 'View Reconciliations'],
            ['name' => 'reconciliations.export', 'display_name' => 'Export Reconciliations'],
            ['name' => 'daily_reports.view', 'display_name' => 'View Daily Reports'],
            ['name' => 'daily_reports.export', 'display_name' => 'Export Daily Reports'],
            ['name' => 'daily_ratio.view', 'display_name' => 'View Daily Ratio'],
            ['name' => 'daily_ratio.export', 'display_name' => 'Export Daily Ratio'],
            ['name' => 'events.view', 'display_name' => 'View Events'],
            ['name' => 'events.export', 'display_name' => 'Export Events'],
            ['name' => 'users.view', 'display_name' => 'View Users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users'],
            ['name' => 'users.manage', 'display_name' => 'Manage Users'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create Administrator role
        $adminRole = Role::firstOrCreate(
            ['name' => 'administrator'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full system access'
            ]
        );

        // Assign all permissions to administrator role
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        // Create administrator user
        $adminUser = User::firstOrCreate(
            ['email' => 'fauzi@hectronic.in'],
            [
                'name' => 'Ahmad Fauzi Firmansyah',
                'username' => 'afafirmansyah',
                'password' => Hash::make('FleetAccess1$'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}