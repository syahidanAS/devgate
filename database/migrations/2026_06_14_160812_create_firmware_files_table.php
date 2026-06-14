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
        Schema::create('firmware_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firmware_project_id')->constrained('firmware_projects')->cascadeOnDelete();
            $table->string('version'); // e.g. v1.0.0
            $table->string('file_path');
            $table->string('flash_offset')->default('0x1000');
            $table->text('changelog')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firmware_files');
    }
};
