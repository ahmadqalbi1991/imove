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
            $table->string('booking_status')->nullable()->after('driver_id');  // Adding nullable string status column
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
            $table->dropColumn('booking_status');
        });

    }
};
