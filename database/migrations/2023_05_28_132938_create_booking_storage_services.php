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
        Schema::create('booking_storage_services', function (Blueprint $table) {
            $table->id();
            $table->integer('booking_id');
            $table->integer('category_id');
            $table->date('pickup_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('area')->nullable();
            $table->string('building_name')->nullable();
            $table->string('building_no')->nullable();
            $table->string('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('items_to_be_collected')->nullable();
            $table->string('include_insurance')->nullable();
            $table->text('extra_details')->nullable();
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
        Schema::dropIfExists('storage_services');
    }
};
