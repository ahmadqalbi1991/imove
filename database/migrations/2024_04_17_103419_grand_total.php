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
            $table->double('cost', 15, 2)->default(0);
            $table->double('service_price', 15, 2)->default(0);
            $table->double('tax', 15, 2)->default(0);
            $table->double('grand_total', 15, 2)->default(0);
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
