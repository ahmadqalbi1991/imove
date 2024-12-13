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
        Schema::create('driver_booking_requests', function (Blueprint $table) {
            $table->id();  // Auto-incrementing primary key
            $table->string('booking_id')->nullable();  // Nullable and string
            $table->string('bid_amount')->nullable();  // Nullable and string
            $table->string('driver_id')->nullable();  // Nullable and string
            $table->timestamps();  // Created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_booking_requests');
    }
};
