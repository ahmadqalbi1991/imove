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
        
            Schema::create('temp_driver_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('driving_license')->nullable();;
                $table->string('mulkia')->nullable();;
                $table->string('mulkia_number')->nullable();;
                $table->enum('is_company',['yes','no'])->default('no');
                $table->unsignedBigInteger('company_id')->nullable();;
                $table->unsignedBigInteger('truck_type_id')->nullable();;
                $table->integer('total_rides')->nullable();;
                $table->timestamps();
                $table->string('address')->nullable();
                $table->string('latitude')->nullable();
                $table->string('longitude')->nullable();
                $table->string('emirates_id_or_passport')->nullable();
                $table->string('driving_license_number')->nullable();
                $table->date('driving_license_expiry')->nullable();
                $table->string('driving_license_issued_by')->nullable();
                $table->string('vehicle_plate_number')->nullable();
                $table->string('vehicle_plate_place')->nullable();
            });
      
    }
};
