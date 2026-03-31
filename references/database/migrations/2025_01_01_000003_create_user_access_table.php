<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('USER_ACCESS', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('WEBUSERS')->onDelete('cascade');
            $table->json('station_ids')->nullable(); // Array of station IDs
            $table->json('terminal_ids')->nullable(); // Array of terminal IDs
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('USER_ACCESS');
    }
};