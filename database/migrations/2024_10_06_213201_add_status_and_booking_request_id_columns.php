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
        // Add 'status' column to 'driver_booking_requests'
        Schema::table('driver_booking_requests', function (Blueprint $table) {
            $table->string('status')->nullable()->after('driver_id');  // Adding nullable string status column
        });

        // Add 'booking_request_id' column to 'booking_pick_up_orders'
        Schema::table('booking_pick_up_orders', function (Blueprint $table) {
            $table->integer('booking_request_id')->nullable()->after('some_column'); // Adjust 'some_column' to place it after the correct column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove 'status' column from 'driver_booking_requests'
        Schema::table('driver_booking_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Remove 'booking_request_id' column from 'booking_pick_up_orders'
        Schema::table('booking_pick_up_orders', function (Blueprint $table) {
            $table->dropColumn('booking_request_id');
        });
    }
};
