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
        Schema::create('booking_office_relocations', function (Blueprint $table) {
            $table->id();
            $table->integer('booking_id');
            $table->integer('category_id');
            $table->date('move_out_date');
            $table->date('move_in_date');
            $table->string('country_from')->nullable();
            $table->string('country_to')->nullable();
            $table->string('city_from')->nullable();
            $table->string('city_to')->nullable();
            $table->string('area_from')->nullable();
            $table->string('area_to')->nullable();
            $table->string('building_from_name')->nullable();
            $table->string('building_to_name')->nullable();
            $table->string('building_from_no')->nullable();
            $table->string('building_to_no')->nullable();
            $table->string('address_from')->nullable();
            $table->string('address_to')->nullable();
            $table->string('latitude_from')->nullable();
            $table->string('latitude_to')->nullable();
            $table->string('longitude_from')->nullable();
            $table->string('longitude_to')->nullable();
            $table->text('details_of_items_to_be_excluded')->nullable();
            $table->string('handyman_services_to_dismantle')->nullable();
            $table->string('handyman_services_to_assemble')->nullable();
            $table->string('include_insurance')->nullable();
            $table->text('extra_details_from')->nullable();
            $table->text('extra_details_to')->nullable();
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
        Schema::dropIfExists('office_relocations');
    }
};
