<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expand video_id to TEXT so it can hold full TikTok URLs
     * (e.g. https://www.tiktok.com/@username/video/7380919256498258177)
     * which exceed the previous varchar(255) limit.
     */
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->text('video_id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('video_id')->change();
        });
    }
};
