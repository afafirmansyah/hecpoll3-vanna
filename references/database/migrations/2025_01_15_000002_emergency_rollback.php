<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration is for emergency rollback only
        // It will remove the role-based system if needed
        
        $this->command->warn('This is an emergency rollback migration. Use with caution!');
    }

    public function down(): void
    {
        // Emergency rollback - remove role system
        if (Schema::hasTable('WEBUSERS')) {
            Schema::table('WEBUSERS', function (Blueprint $table) {
                if (Schema::hasColumn('WEBUSERS', 'role_id')) {
                    $table->dropForeign(['role_id']);
                }
                if (Schema::hasColumn('WEBUSERS', 'username')) {
                    $table->dropColumn(['username']);
                }
                if (Schema::hasColumn('WEBUSERS', 'is_active')) {
                    $table->dropColumn(['is_active']);
                }
                if (Schema::hasColumn('WEBUSERS', 'last_login_at')) {
                    $table->dropColumn(['last_login_at']);
                }
                if (Schema::hasColumn('WEBUSERS', 'role_id')) {
                    $table->dropColumn(['role_id']);
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