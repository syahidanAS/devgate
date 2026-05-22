<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('Rumah'); // Rumah, Kantor, etc.
            $table->string('recipient_name');
            $table->string('phone', 20);
            $table->text('address');
            $table->string('city');
            $table->string('city_id')->nullable(); // RajaOngkir city ID
            $table->string('province');
            $table->string('province_id')->nullable(); // RajaOngkir province ID
            $table->string('postal_code', 10);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
