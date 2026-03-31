<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
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
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'administrator'],
            ['display_name' => 'Administrator', 'description' => 'Full system access']
        );

        $editorRole = Role::firstOrCreate(
            ['name' => 'editor'],
            ['display_name' => 'Editor', 'description' => 'View and limited edit access']
        );

        $viewerRole = Role::firstOrCreate(
            ['name' => 'viewer'],
            ['display_name' => 'Viewer', 'description' => 'Read-only access']
        );

        // Assign permissions to roles
        $adminRole->permissions()->sync(Permission::all());
        
        $editorRole->permissions()->sync(Permission::whereIn('name', [
            'view_dashboard',
            'cards.view', 'cards.export',
            'vehicles.view', 'vehicles.export',
            'transactions.view', 'transactions.export', 'update_mileage',
            'reconciliations.view', 'reconciliations.export',
            'daily_reports.view', 'daily_reports.export',
            'daily_ratio.view', 'daily_ratio.export',
            'events.view', 'events.export',
            'users.view'
        ])->get());

        $viewerRole->permissions()->sync(Permission::whereIn('name', [
            'view_dashboard',
            'cards.view',
            'vehicles.view',
            'transactions.view',
            'reconciliations.view',
            'daily_reports.view',
            'daily_ratio.view',
            'events.view'
        ])->get());
        
        // Create mileage editor role
        $mileageEditorRole = Role::firstOrCreate(
            ['name' => 'mileage_editor'],
            ['display_name' => 'Mileage Editor', 'description' => 'Can view and edit mileage in transactions']
        );
        
        $mileageEditorRole->permissions()->sync(Permission::whereIn('name', [
            'view_dashboard',
            'transactions.view',
            'update_mileage'
        ])->get());

        // Create default admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@bism.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        
        // Create sample mileage editor user
        User::firstOrCreate(
            ['email' => 'mileage@bism.com'],
            [
                'name' => 'Mileage Editor',
                'username' => 'mileage_editor',
                'password' => Hash::make('mileage123'),
                'role_id' => $mileageEditorRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}