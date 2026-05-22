<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_product', function (Blueprint $table) {
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->string('context')->nullable(); // "used_in_article", "sponsored", etc.
            $table->primary(['article_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_product');
    }
};
