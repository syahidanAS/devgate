<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name'); // Snapshot
            $table->string('product_sku')->nullable(); // Snapshot
            $table->unsignedBigInteger('price'); // Price at time of purchase
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('subtotal'); // price * quantity
            $table->json('product_snapshot'); // Full product data snapshot
            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
