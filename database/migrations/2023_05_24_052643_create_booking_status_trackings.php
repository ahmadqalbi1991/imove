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
        Schema::create('booking_status_trackings', function (Blueprint $table) {
            $table->id();
            $table->integer('booking_id');
            $table->enum('status_tracking',['request_created','qouted','accepted','journey_started','item_collected','on_the_way','border_crossing','custom_clearance','delivered']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('booking_status_trackings');
    }
};
