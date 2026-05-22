<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds a self-referencing parent_id so replies can be nested under other replies.
     */
    public function up(): void
    {
        Schema::table('replies', function (Blueprint $table) {
            $table->foreignId('parent_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('replies')
                  ->nullOnDelete(); // keep child when parent is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('replies', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
