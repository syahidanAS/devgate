<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // Drop the old PostgreSQL CHECK constraint
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');

            // Re-create the CHECK constraint including 'refunding' and 'refunded'
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'awaiting_payment', 'paid', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'refunding', 'refunded'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check');
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'awaiting_payment', 'paid', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'))");
        }
    }
};
