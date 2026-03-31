<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class UpdateRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Create new permissions
        $permissions = [
            ['name' => 'edit_transactions', 'display_name' => 'Edit Transactions'],
            ['name' => 'view_only', 'display_name' => 'View Only'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create new roles
        $editorRole = Role::firstOrCreate(
            ['name' => 'transaction_editor'],
            ['display_name' => 'Transaction Editor']
        );

        $viewerRole = Role::firstOrCreate(
            ['name' => 'viewer'],
            ['display_name' => 'Viewer']
        );

        // Assign permissions to Transaction Editor
        $editorPermissions = [
            'view_dashboard', 'view_cards', 'view_vehicles', 'view_transactions', 
            'view_events', 'view_reconciliations', 'view_daily_reports', 'view_daily_ratio',
            'edit_transactions', 'update_mileage'
        ];

        foreach ($editorPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission && !$editorRole->permissions()->where('permission_id', $permission->id)->exists()) {
                $editorRole->permissions()->attach($permission->id);
            }
        }

        // Assign permissions to Viewer
        $viewerPermissions = [
            'view_dashboard', 'view_cards', 'view_vehicles', 'view_transactions', 
            'view_events', 'view_reconciliations', 'view_daily_reports', 'view_daily_ratio',
            'view_only'
        ];

        foreach ($viewerPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission && !$viewerRole->permissions()->where('permission_id', $permission->id)->exists()) {
                $viewerRole->permissions()->attach($permission->id);
            }
        }
    }
}