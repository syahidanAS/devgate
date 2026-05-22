<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->enum('platform', ['youtube', 'tiktok'])->default('youtube')->index();
            $table->string('video_id');          // YouTube video ID or TikTok video ID
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('duration', 20)->nullable(); // e.g. "12:34" or "0:58"
            $table->string('views', 20)->nullable();    // e.g. "18.4K"
            $table->unsignedSmallInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
