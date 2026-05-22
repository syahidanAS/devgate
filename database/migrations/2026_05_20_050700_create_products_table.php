<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique()->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();
            $table->longText('body')->nullable(); // Rich text specs/features
            $table->unsignedBigInteger('price'); // in IDR (Rupiah) stored as integer
            $table->unsignedBigInteger('sale_price')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('weight')->default(0); // in grams
            $table->string('brand')->nullable();
            $table->json('specifications')->nullable(); // Key-value specs
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('track_stock')->default(true);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index('slug');
            $table->index('status');
            $table->index('is_featured');
            $table->index(['status', 'stock']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
