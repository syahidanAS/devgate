<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Drop old unique constraint
            $table->dropUnique(['user_id', 'product_id']);
            
            $table->foreignId('product_variant_id')->nullable()->after('product_id')->constrained()->cascadeOnDelete();
            
            // Add new unique constraint including variant
            $table->unique(['user_id', 'product_id', 'product_variant_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('product_id')->constrained()->nullOnDelete();
            $table->string('variant_name')->nullable()->after('product_name');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn(['product_variant_id', 'variant_name']);
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'product_id', 'product_variant_id']);
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
            $table->unique(['user_id', 'product_id']);
        });
    }
};
