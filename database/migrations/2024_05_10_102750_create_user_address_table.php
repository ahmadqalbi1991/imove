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
        Schema::create('user_address', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->default(0);
            $table->string('full_name', 255);
            $table->string('dial_code', 5);
            $table->string('phone', 20);
            $table->text('address');
            $table->unsignedInteger('country_id')->default(0);
            $table->unsignedInteger('state_id')->default(0);
            $table->unsignedInteger('city_id')->default(0);
            $table->string('address_type', 255)->default('Home');
            $table->integer('status')->default(1);
            $table->integer('is_default')->default(0);
            $table->string('land_mark', 600)->nullable();
            $table->string('building_name', 600)->nullable();
            $table->string('latitude', 600)->nullable();
            $table->string('longitude', 600)->nullable();
            $table->string('location', 1600)->nullable();
            $table->string('area_id', 255)->default('');
            $table->string('apartment', 255)->default('');
            $table->string('street', 255)->default('');
            $table->timestamps(true); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_address', function (Blueprint $table) {
            //
        });
    }
};
