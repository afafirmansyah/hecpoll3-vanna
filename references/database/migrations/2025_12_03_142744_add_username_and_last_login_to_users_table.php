<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('WEBUSERS', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
            $table->timestamp('last_login_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('WEBUSERS', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropColumn('last_login_at');
        });
    }
};
