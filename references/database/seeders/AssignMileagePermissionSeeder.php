<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class AssignMileagePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $updateMileagePermission = Permission::where('name', 'update_mileage')->first();
        
        if (!$updateMileagePermission) {
            echo "update_mileage permission not found!\n";
            return;
        }

        // Add to administrator role
        $adminRole = Role::where('name', 'administrator')->first();
        if ($adminRole && !$adminRole->permissions()->where('name', 'update_mileage')->exists()) {
            $adminRole->permissions()->attach($updateMileagePermission->id);
            echo "Added update_mileage permission to administrator role\n";
        }

        // Add to editor role
        $editorRole = Role::where('name', 'editor')->first();
        if ($editorRole && !$editorRole->permissions()->where('name', 'update_mileage')->exists()) {
            $editorRole->permissions()->attach($updateMileagePermission->id);
            echo "Added update_mileage permission to editor role\n";
        }

        // Create or update mileage_editor role
        $mileageEditorRole = Role::firstOrCreate(
            ['name' => 'mileage_editor'],
            ['display_name' => 'Mileage Editor', 'description' => 'Can view and edit mileage in transactions']
        );

        // Assign specific permissions to mileage editor
        $mileagePermissions = Permission::whereIn('name', [
            'view_dashboard',
            'view_transactions',
            'update_mileage'
        ])->get();

        $mileageEditorRole->permissions()->sync($mileagePermissions);
        echo "Updated mileage_editor role permissions\n";

        echo "Mileage permissions assigned successfully!\n";
    }
}