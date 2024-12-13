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
            $table->string('po_latitude',600)->nullable();
            $table->string('po_longitude',600)->nullable();
            $table->string('do_latitude',600)->nullable();
            $table->string('do_longitude',600)->nullable();
            
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
