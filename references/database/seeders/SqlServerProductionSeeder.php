<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\TerminalConfiguration;

class SqlServerProductionSeeder extends Seeder
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
            $this->command->info('SQL Server production seeding completed successfully!');
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
            DB::table('PERMISSIONS')->updateOrInsert(
                ['name' => $permission['name']],
                array_merge($permission, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
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
            DB::table('ROLES')->updateOrInsert(
                ['name' => $role['name']],
                array_merge($role, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        $this->command->info('Roles created/updated');
    }

    private function assignPermissionsToRoles(): void
    {
        // Get role and permission IDs
        $adminRole = DB::table('ROLES')->where('name', 'administrator')->first();
        $editorRole = DB::table('ROLES')->where('name', 'editor')->first();
        $viewerRole = DB::table('ROLES')->where('name', 'viewer')->first();
        $mileageEditorRole = DB::table('ROLES')->where('name', 'mileage_editor')->first();

        $allPermissions = DB::table('PERMISSIONS')->get();

        // Administrator - all permissions
        if ($adminRole) {
            foreach ($allPermissions as $permission) {
                DB::table('ROLE_PERMISSIONS')->updateOrInsert(
                    ['role_id' => $adminRole->id, 'permission_id' => $permission->id],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // Editor permissions
        if ($editorRole) {
            $editorPermissionNames = [
                'view_dashboard', 'view_cards', 'export_cards', 'view_vehicles', 'export_vehicles',
                'view_transactions', 'export_transactions', 'edit_transactions', 'update_mileage',
                'view_reconciliations', 'export_reconciliations', 'view_daily_reports', 'export_daily_reports',
                'view_daily_ratio', 'export_daily_ratio', 'view_events', 'export_events', 'view_users'
            ];
            
            $editorPermissions = $allPermissions->whereIn('name', $editorPermissionNames);
            foreach ($editorPermissions as $permission) {
                DB::table('ROLE_PERMISSIONS')->updateOrInsert(
                    ['role_id' => $editorRole->id, 'permission_id' => $permission->id],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // Viewer permissions
        if ($viewerRole) {
            $viewerPermissionNames = [
                'view_dashboard', 'view_cards', 'view_vehicles', 'view_transactions',
                'view_reconciliations', 'view_daily_reports', 'view_daily_ratio', 'view_events'
            ];
            
            $viewerPermissions = $allPermissions->whereIn('name', $viewerPermissionNames);
            foreach ($viewerPermissions as $permission) {
                DB::table('ROLE_PERMISSIONS')->updateOrInsert(
                    ['role_id' => $viewerRole->id, 'permission_id' => $permission->id],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // Mileage Editor permissions
        if ($mileageEditorRole) {
            $mileagePermissionNames = ['view_dashboard', 'view_transactions', 'update_mileage'];
            
            $mileagePermissions = $allPermissions->whereIn('name', $mileagePermissionNames);
            foreach ($mileagePermissions as $permission) {
                DB::table('ROLE_PERMISSIONS')->updateOrInsert(
                    ['role_id' => $mileageEditorRole->id, 'permission_id' => $permission->id],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        $this->command->info('Permissions assigned to roles');
    }

    private function createDefaultUsers(): void
    {
        $adminRole = DB::table('ROLES')->where('name', 'administrator')->first();
        
        if (!$adminRole) {
            $this->command->error('Administrator role not found');
            return;
        }

        // Find user table
        $userTable = $this->findUserTable();
        if (!$userTable) {
            $this->command->error('User table not found');
            return;
        }

        // Create main admin user
        DB::table($userTable)->updateOrInsert(
            ['email' => 'fauzi@hectronic.in'],
            [
                'name' => 'Ahmad Fauzi Firmansyah',
                'username' => 'afafirmansyah',
                'password' => Hash::make('FleetAccess1$'),
                'role_id' => $adminRole->id,
                'is_active' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Update existing users without roles
        DB::table($userTable)
            ->whereNull('role_id')
            ->update([
                'role_id' => $adminRole->id,
                'is_active' => 1,
                'updated_at' => now()
            ]);

        $this->command->info('Default users created/updated');
    }

    private function createTerminalConfigurations(): void
    {
        $configurations = [
            ['operation_type' => 'decantation', 'terminal_ids' => '[3]'],
            ['operation_type' => 'toploading', 'terminal_ids' => '[1,2]'],
            ['operation_type' => 'fuel_dispensing', 'terminal_ids' => '[4,5,6]'],
        ];

        foreach ($configurations as $config) {
            DB::table('TERMINAL_CONFIGURATIONS')->updateOrInsert(
                ['operation_type' => $config['operation_type']],
                array_merge($config, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }

        $this->command->info('Terminal configurations created');
    }

    private function findUserTable(): ?string
    {
        $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
        $existingTables = collect($tables)->pluck('TABLE_NAME')->map(fn($name) => strtoupper($name))->toArray();
        
        $possibleUserTables = ['WEBUSERS', 'USERS', 'webusers', 'users'];
        
        foreach ($possibleUserTables as $tableName) {
            if (in_array(strtoupper($tableName), $existingTables)) {
                return $tableName;
            }
        }
        
        return null;
    }
}