<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ROLES', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('PERMISSIONS', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('ROLE_PERMISSIONS', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('ROLES')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('PERMISSIONS')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['role_id', 'permission_id']);
        });

        if (Schema::hasTable('WEBUSERS')) {
            Schema::table('WEBUSERS', function (Blueprint $table) {
                $table->foreignId('role_id')->nullable()->constrained('ROLES')->onDelete('set null');
                $table->boolean('is_active')->default(true);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('WEBUSERS')) {
            Schema::table('WEBUSERS', function (Blueprint $table) {
                if (Schema::hasColumn('WEBUSERS', 'role_id')) {
                    $table->dropForeign(['role_id']);
                    $table->dropColumn(['role_id', 'is_active']);
                }
            });
        }
        
        Schema::dropIfExists('ROLE_PERMISSIONS');
        Schema::dropIfExists('PERMISSIONS');
        Schema::dropIfExists('ROLES');
    }
};