<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TERMINAL_CONFIGURATIONS', function (Blueprint $table) {
            $table->id();
            $table->string('operation_type'); // 'decantation', 'toploading', 'fuel_dispensing'
            $table->json('terminal_ids'); // Array of terminal IDs
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TERMINAL_CONFIGURATIONS');
    }
};