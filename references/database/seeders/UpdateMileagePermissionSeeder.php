<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class UpdateMileagePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create update_mileage permission if not exists
        $permission = Permission::firstOrCreate(
            ['name' => 'update_mileage'],
            ['display_name' => 'Edit Mileage in Transactions', 'description' => 'Allow user to edit mileage values in transaction records']
        );

        // Add permission to administrator role
        $adminRole = Role::where('name', 'administrator')->first();
        if ($adminRole && !$adminRole->permissions()->where('name', 'update_mileage')->exists()) {
            $adminRole->permissions()->attach($permission->id);
        }

        // Add permission to editor role
        $editorRole = Role::where('name', 'editor')->first();
        if ($editorRole && !$editorRole->permissions()->where('name', 'update_mileage')->exists()) {
            $editorRole->permissions()->attach($permission->id);
        }

        // Create mileage editor role if not exists
        $mileageEditorRole = Role::firstOrCreate(
            ['name' => 'mileage_editor'],
            ['display_name' => 'Mileage Editor', 'description' => 'Can view and edit mileage in transactions']
        );

        // Assign specific permissions to mileage editor
        $mileagePermissions = Permission::whereIn('name', [
            'view_dashboard',
            'transactions.view',
            'update_mileage'
        ])->get();

        $mileageEditorRole->permissions()->sync($mileagePermissions);
    }
}