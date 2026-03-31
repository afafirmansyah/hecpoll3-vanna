<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class CleanupPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Update update_mileage permission display name
        Permission::where('name', 'update_mileage')->update([
            'display_name' => 'Edit Mileage in Transactions',
            'description' => 'Allow user to edit mileage values in transaction records'
        ]);

        // Remove duplicate permissions with dot notation
        $duplicatePermissions = [
            'cards.view', 'cards.export',
            'vehicles.view', 'vehicles.export', 
            'transactions.view', 'transactions.export',
            'reconciliations.view', 'reconciliations.export',
            'daily_reports.view', 'daily_reports.export',
            'daily_ratio.view', 'daily_ratio.export',
            'events.view', 'events.export',
            'users.view', 'users.edit', 'users.manage'
        ];

        foreach ($duplicatePermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                // Remove from role_permissions first
                $permission->roles()->detach();
                // Delete the permission
                $permission->delete();
            }
        }

        echo "Permissions cleaned up successfully!\n";
    }
}