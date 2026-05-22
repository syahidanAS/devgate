<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('midtrans_order_id')->unique(); // e.g., "devgate-order-123-1716..."
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('payment_type')->nullable(); // "bank_transfer", "qris", "credit_card"
            $table->string('payment_method')->nullable(); // "bca", "bni", etc.
            $table->enum('status', [
                'pending',
                'capture',
                'settlement',
                'deny',
                'cancel',
                'expire',
                'failure',
                'refund',
            ])->default('pending');
            $table->unsignedBigInteger('amount');
            $table->string('va_number')->nullable(); // Virtual account number
            $table->string('qris_url')->nullable();
            $table->json('payload')->nullable(); // Full Midtrans response
            $table->string('snap_token')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index('order_id');
            $table->index('status');
            $table->index('midtrans_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
