<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\TerminalConfiguration;

class SafeProductionSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            $this->createPermissions();
            $this->createRoles();
            $this->assignPermissionsToRoles();
            $this->createDefaultUsers();
            $this->createTerminalConfigurations();
            
            DB::commit();
            $this->command->info('Production seeding completed successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('Seeding failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createPermissions(): void
    {
        $permissions = [
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard', 'description' => 'Access to main dashboard'],
            ['name' => 'view_cards', 'display_name' => 'View Cards', 'description' => 'View card information'],
            ['name' => 'export_cards', 'display_name' => 'Export Cards', 'description' => 'Export card data'],
            ['name' => 'view_vehicles', 'display_name' => 'View Vehicles', 'description' => 'View vehicle information'],
            ['name' => 'export_vehicles', 'display_name' => 'Export Vehicles', 'description' => 'Export vehicle data'],
            ['name' => 'view_transactions', 'display_name' => 'View Transactions', 'description' => 'View transaction records'],
            ['name' => 'export_transactions', 'display_name' => 'Export Transactions', 'description' => 'Export transaction data'],
            ['name' => 'edit_transactions', 'display_name' => 'Edit Transactions', 'description' => 'Modify transaction records'],
            ['name' => 'update_mileage', 'display_name' => 'Update Mileage', 'description' => 'Edit mileage in transactions'],
            ['name' => 'view_reconciliations', 'display_name' => 'View Reconciliations', 'description' => 'View reconciliation reports'],
            ['name' => 'export_reconciliations', 'display_name' => 'Export Reconciliations', 'description' => 'Export reconciliation data'],
            ['name' => 'view_daily_reports', 'display_name' => 'View Daily Reports', 'description' => 'View daily reports'],
            ['name' => 'export_daily_reports', 'display_name' => 'Export Daily Reports', 'description' => 'Export daily report data'],
            ['name' => 'view_daily_ratio', 'display_name' => 'View Daily Ratio', 'description' => 'View daily ratio reports'],
            ['name' => 'export_daily_ratio', 'display_name' => 'Export Daily Ratio', 'description' => 'Export daily ratio data'],
            ['name' => 'view_events', 'display_name' => 'View Events', 'description' => 'View system events'],
            ['name' => 'export_events', 'display_name' => 'Export Events', 'description' => 'Export event data'],
            ['name' => 'view_users', 'display_name' => 'View Users', 'description' => 'View user accounts'],
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'description' => 'Create, edit, delete users'],
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles', 'description' => 'Manage user roles and permissions'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        $this->command->info('Permissions created/updated');
    }

    private function createRoles(): void
    {
        $roles = [
            [
                'name' => 'administrator',
                'display_name' => 'Administrator',
                'description' => 'Full system access with all permissions'
            ],
            [
                'name' => 'editor',
                'display_name' => 'Editor',
                'description' => 'Can view and edit most data'
            ],
            [
                'name' => 'viewer',
                'display_name' => 'Viewer',
                'description' => 'Read-only access to system data'
            ],
            [
                'name' => 'mileage_editor',
                'display_name' => 'Mileage Editor',
                'description' => 'Can view transactions and edit mileage only'
            ]
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }

        $this->command->info('Roles created/updated');
    }

    private function assignPermissionsToRoles(): void
    {
        // Administrator - all permissions
        $adminRole = Role::where('name', 'administrator')->first();
        if ($adminRole) {
            $allPermissions = Permission::all();
            $adminRole->permissions()->sync($allPermissions->pluck('id'));
        }

        // Editor - most permissions except user management
        $editorRole = Role::where('name', 'editor')->first();
        if ($editorRole) {
            $editorPermissions = Permission::whereIn('name', [
                'view_dashboard', 'view_cards', 'export_cards', 'view_vehicles', 'export_vehicles',
                'view_transactions', 'export_transactions', 'edit_transactions', 'update_mileage',
                'view_reconciliations', 'export_reconciliations', 'view_daily_reports', 'export_daily_reports',
                'view_daily_ratio', 'export_daily_ratio', 'view_events', 'export_events', 'view_users'
            ])->pluck('id');
            $editorRole->permissions()->sync($editorPermissions);
        }

        // Viewer - read-only permissions
        $viewerRole = Role::where('name', 'viewer')->first();
        if ($viewerRole) {
            $viewerPermissions = Permission::whereIn('name', [
                'view_dashboard', 'view_cards', 'view_vehicles', 'view_transactions',
                'view_reconciliations', 'view_daily_reports', 'view_daily_ratio', 'view_events'
            ])->pluck('id');
            $viewerRole->permissions()->sync($viewerPermissions);
        }

        // Mileage Editor - limited permissions
        $mileageEditorRole = Role::where('name', 'mileage_editor')->first();
        if ($mileageEditorRole) {
            $mileagePermissions = Permission::whereIn('name', [
                'view_dashboard', 'view_transactions', 'update_mileage'
            ])->pluck('id');
            $mileageEditorRole->permissions()->sync($mileagePermissions);
        }

        $this->command->info('Permissions assigned to roles');
    }

    private function createDefaultUsers(): void
    {
        $adminRole = Role::where('name', 'administrator')->first();
        
        // Create main admin user
        User::firstOrCreate(
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

        // Update existing users without roles
        $usersWithoutRoles = User::whereNull('role_id')->get();
        foreach ($usersWithoutRoles as $user) {
            $user->update([
                'role_id' => $adminRole->id,
                'is_active' => true
            ]);
        }

        $this->command->info('Default users created/updated');
    }

    private function createTerminalConfigurations(): void
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

        $this->command->info('Terminal configurations created');
    }
}