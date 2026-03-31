<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check what tables exist first
        $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
        $existingTables = collect($tables)->pluck('TABLE_NAME')->map(fn($name) => strtoupper($name))->toArray();
        
        $this->command->info('Existing tables: ' . implode(', ', $existingTables));
        
        // Create ROLES table if not exists
        if (!in_array('ROLES', $existingTables)) {
            Schema::create('ROLES', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('display_name');
                $table->text('description')->nullable();
                $table->timestamps();
            });
            $this->command->info('Created ROLES table');
        }

        // Create PERMISSIONS table if not exists
        if (!in_array('PERMISSIONS', $existingTables)) {
            Schema::create('PERMISSIONS', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('display_name');
                $table->text('description')->nullable();
                $table->timestamps();
            });
            $this->command->info('Created PERMISSIONS table');
        }

        // Create ROLE_PERMISSIONS table if not exists
        if (!in_array('ROLE_PERMISSIONS', $existingTables)) {
            Schema::create('ROLE_PERMISSIONS', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained('ROLES')->onDelete('cascade');
                $table->foreignId('permission_id')->constrained('PERMISSIONS')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['role_id', 'permission_id']);
            });
            $this->command->info('Created ROLE_PERMISSIONS table');
        }

        // Find the users table (could be WEBUSERS, users, etc.)
        $userTable = null;
        $possibleUserTables = ['WEBUSERS', 'USERS', 'webusers', 'users'];
        
        foreach ($possibleUserTables as $tableName) {
            if (in_array(strtoupper($tableName), $existingTables)) {
                $userTable = $tableName;
                break;
            }
        }
        
        if ($userTable) {
            $this->command->info("Found user table: $userTable");
            
            // Get existing columns
            $columns = DB::select("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?", [$userTable]);
            $existingColumns = collect($columns)->pluck('COLUMN_NAME')->map(fn($name) => strtolower($name))->toArray();
            
            // Add columns to user table if they don't exist
            Schema::table($userTable, function (Blueprint $table) use ($existingColumns) {
                if (!in_array('username', $existingColumns)) {
                    $table->string('username')->nullable()->after('name');
                }
                if (!in_array('role_id', $existingColumns)) {
                    $table->foreignId('role_id')->nullable()->constrained('ROLES')->onDelete('set null');
                }
                if (!in_array('is_active', $existingColumns)) {
                    $table->boolean('is_active')->default(true);
                }
                if (!in_array('last_login_at', $existingColumns)) {
                    $table->timestamp('last_login_at')->nullable();
                }
            });
            $this->command->info("Updated $userTable table");
        } else {
            $this->command->warn('No user table found. Please create WEBUSERS table manually.');
        }

        // Create TERMINAL_CONFIGURATIONS table if not exists
        if (!in_array('TERMINAL_CONFIGURATIONS', $existingTables)) {
            Schema::create('TERMINAL_CONFIGURATIONS', function (Blueprint $table) {
                $table->id();
                $table->string('operation_type');
                $table->text('terminal_ids'); // Use text instead of json for SQL Server compatibility
                $table->timestamps();
            });
            $this->command->info('Created TERMINAL_CONFIGURATIONS table');
        }

        // Create USER_ACCESS table if not exists
        if (!in_array('USER_ACCESS', $existingTables) && $userTable) {
            Schema::create('USER_ACCESS', function (Blueprint $table) use ($userTable) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->text('station_ids')->nullable(); // Use text instead of json
                $table->text('terminal_ids')->nullable(); // Use text instead of json
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on($userTable)->onDelete('cascade');
            });
            $this->command->info('Created USER_ACCESS table');
        }
    }

    public function down(): void
    {
        // Find the users table
        $tables = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
        $existingTables = collect($tables)->pluck('TABLE_NAME')->map(fn($name) => strtoupper($name))->toArray();
        
        $userTable = null;
        $possibleUserTables = ['WEBUSERS', 'USERS', 'webusers', 'users'];
        
        foreach ($possibleUserTables as $tableName) {
            if (in_array(strtoupper($tableName), $existingTables)) {
                $userTable = $tableName;
                break;
            }
        }

        if ($userTable) {
            Schema::table($userTable, function (Blueprint $table) {
                $table->dropForeign(['role_id']);
                $table->dropColumn(['role_id', 'is_active', 'username', 'last_login_at']);
            });
        }

        Schema::dropIfExists('USER_ACCESS');
        Schema::dropIfExists('TERMINAL_CONFIGURATIONS');
        Schema::dropIfExists('ROLE_PERMISSIONS');
        Schema::dropIfExists('PERMISSIONS');
        Schema::dropIfExists('ROLES');
    }
};