<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class SetDefaultPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'administrator')->first();
        $editorRole = Role::where('name', 'editor')->first();
        $viewerRole = Role::where('name', 'viewer')->first();

        // Administrator gets ALL permissions
        if ($adminRole) {
            $allPermissions = Permission::all()->pluck('id');
            $adminRole->permissions()->sync($allPermissions);
        }

        // Editor permissions
        if ($editorRole) {
            $editorPermissions = Permission::whereIn('name', [
                'view_dashboard', 'view_cards', 'export_cards', 'view_vehicles', 'export_vehicles',
                'view_transactions', 'export_transactions', 'update_mileage', 'view_events', 'export_events',
                'view_reconciliations', 'export_reconciliations', 'view_daily_reports', 'export_daily_reports',
                'view_daily_ratio', 'export_daily_ratio', 'edit_transactions'
            ])->pluck('id');
            $editorRole->permissions()->sync($editorPermissions);
        }

        // Viewer permissions  
        if ($viewerRole) {
            $viewerPermissions = Permission::whereIn('name', [
                'view_dashboard', 'view_cards', 'view_vehicles', 'view_transactions', 
                'view_events', 'view_reconciliations', 'view_daily_reports', 'view_daily_ratio', 'view_only'
            ])->pluck('id');
            $viewerRole->permissions()->sync($viewerPermissions);
        }
    }
}