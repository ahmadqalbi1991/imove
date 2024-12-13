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
        Schema::table('booking_pick_up_orders', function (Blueprint $table) {
            $table->integer('pickup_driver')->default(0);
            $table->integer('delivery_driver')->default(0);
            $table->integer('booking_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_pick_up_orders', function (Blueprint $table) {
            //
        });
    }
};