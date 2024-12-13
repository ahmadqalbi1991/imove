<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Modify the "status" column constraint to include 'on_deliver'
            Schema::table('orders', function (Blueprint $table) {
                $table->string('status')->change();
            });

            // Raw SQL to drop the current check constraint (PostgreSQL specific)
            DB::statement("ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_status_check");

            // Add new check constraint for the allowed values in the "status" column
            DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('pending', 'approved', 'cancelled', 'on_deliver', 'on_going', 'completed'))");
        });
    }
};
