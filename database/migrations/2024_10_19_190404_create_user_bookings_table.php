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
        Schema::create('user_bookings', function (Blueprint $table) {
            $table->id();
            $table->text('drop_off_location')->nullable();
            $table->string('drop_off_lat')->nullable();
            $table->string('drop_off_lng')->nullable();
            $table->text('pick_up_location')->nullable();
            $table->string('pick_up_lat')->nullable();
            $table->string('pick_up_lng')->nullable();
            $table->bigInteger('vehicle_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('emergency_type_id')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'canceled', 'confirmed', 'completed'])->nullable();
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
        Schema::dropIfExists('user_bookings');
    }
};
