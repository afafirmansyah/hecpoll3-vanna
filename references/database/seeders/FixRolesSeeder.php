<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class FixRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Delete old roles
        Role::where('name', 'transaction_editor')->delete();
        Role::where('name', 'viewer')->delete();

        // Update/Create the 3 main roles
        $adminRole = Role::updateOrCreate(
            ['name' => 'administrator'],
            ['display_name' => 'Administrator']
        );

        $editorRole = Role::updateOrCreate(
            ['name' => 'editor'],
            ['display_name' => 'Editor']
        );

        $viewerRole = Role::updateOrCreate(
            ['name' => 'viewer'],
            ['display_name' => 'Viewer']
        );

        // Assign permissions to Editor
        $editorPermissions = [
            'view_dashboard', 'view_cards', 'view_vehicles', 'view_transactions', 
            'view_events', 'view_reconciliations', 'view_daily_reports', 'view_daily_ratio',
            'edit_transactions', 'update_mileage'
        ];

        $editorRole->permissions()->detach();
        foreach ($editorPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $editorRole->permissions()->attach($permission->id);
            }
        }

        // Assign permissions to Viewer
        $viewerPermissions = [
            'view_dashboard', 'view_cards', 'view_vehicles', 'view_transactions', 
            'view_events', 'view_reconciliations', 'view_daily_reports', 'view_daily_ratio'
        ];

        $viewerRole->permissions()->detach();
        foreach ($viewerPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $viewerRole->permissions()->attach($permission->id);
            }
        }
    }
}