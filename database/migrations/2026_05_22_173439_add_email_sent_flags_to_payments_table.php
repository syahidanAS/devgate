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
        Schema::table('payments', function (Blueprint $table) {
            $table->timestamp('reminder_email_sent_at')->nullable();
            $table->timestamp('expired_email_sent_at')->nullable();

            $table->index('expired_at');
            $table->index('reminder_email_sent_at');
            $table->index('expired_email_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['expired_at']);
            $table->dropIndex(['reminder_email_sent_at']);
            $table->dropIndex(['expired_email_sent_at']);

            $table->dropColumn(['reminder_email_sent_at', 'expired_email_sent_at']);
        });
    }
};
