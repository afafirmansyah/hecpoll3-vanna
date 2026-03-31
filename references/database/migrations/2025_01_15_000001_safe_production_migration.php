<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create ROLES table if not exists
        if (!Schema::hasTable('ROLES')) {
            Schema::create('ROLES', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('display_name');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Create PERMISSIONS table if not exists
        if (!Schema::hasTable('PERMISSIONS')) {
            Schema::create('PERMISSIONS', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('display_name');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // Create ROLE_PERMISSIONS table if not exists
        if (!Schema::hasTable('ROLE_PERMISSIONS')) {
            Schema::create('ROLE_PERMISSIONS', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained('ROLES')->onDelete('cascade');
                $table->foreignId('permission_id')->constrained('PERMISSIONS')->onDelete('cascade');
                $table->timestamps();
                $table->unique(['role_id', 'permission_id']);
            });
        }

        // Add columns to WEBUSERS if they don't exist
        if (Schema::hasTable('WEBUSERS')) {
            Schema::table('WEBUSERS', function (Blueprint $table) {
                if (!Schema::hasColumn('WEBUSERS', 'username')) {
                    $table->string('username')->nullable()->after('name');
                }
                if (!Schema::hasColumn('WEBUSERS', 'role_id')) {
                    $table->foreignId('role_id')->nullable()->constrained('ROLES')->onDelete('set null')->after('username');
                }
                if (!Schema::hasColumn('WEBUSERS', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('role_id');
                }
                if (!Schema::hasColumn('WEBUSERS', 'last_login_at')) {
                    $table->timestamp('last_login_at')->nullable()->after('updated_at');
                }
            });
        }

        // Create TERMINAL_CONFIGURATIONS table if not exists
        if (!Schema::hasTable('TERMINAL_CONFIGURATIONS')) {
            Schema::create('TERMINAL_CONFIGURATIONS', function (Blueprint $table) {
                $table->id();
                $table->string('operation_type');
                $table->json('terminal_ids');
                $table->timestamps();
            });
        }

        // Create USER_ACCESS table if not exists
        if (!Schema::hasTable('USER_ACCESS')) {
            Schema::create('USER_ACCESS', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->json('station_ids')->nullable();
                $table->json('terminal_ids')->nullable();
                $table->timestamps();
                
                // Only add foreign key if WEBUSERS table exists
                if (Schema::hasTable('WEBUSERS')) {
                    $table->foreign('user_id')->references('id')->on('WEBUSERS')->onDelete('cascade');
                }
            });
        }
    }

    public function down(): void
    {
        // Remove added columns from WEBUSERS
        if (Schema::hasTable('WEBUSERS')) {
            Schema::table('WEBUSERS', function (Blueprint $table) {
                if (Schema::hasColumn('WEBUSERS', 'role_id')) {
                    $table->dropForeign(['role_id']);
                    $table->dropColumn(['role_id', 'is_active', 'username', 'last_login_at']);
                }
            });
        }

        Schema::dropIfExists('USER_ACCESS');
        Schema::dropIfExists('TERMINAL_CONFIGURATIONS');
        Schema::dropIfExists('ROLE_PERMISSIONS');
        Schema::dropIfExists('PERMISSIONS');
        Schema::dropIfExists('ROLES');
    }
};