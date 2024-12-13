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
        Schema::table('driver_details', function (Blueprint $table) {
            $table->string('emirates_id_or_passport')->nullable();
            $table->string('driving_license_number')->nullable();
            $table->date('driving_license_expiry')->nullable();
            $table->string('driving_license_issued_by')->nullable();
            $table->string('vehicle_plate_number')->nullable();
            $table->string('vehicle_plate_place')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('driver_details', function (Blueprint $table) {
            $table->dropColumn('emirates_id_or_passport');
            $table->dropColumn('driving_license_number');
            $table->dropColumn('driving_license_expiry');
            $table->dropColumn('driving_license_issued_by');
            $table->dropColumn('vehicle_plate_number');
            $table->dropColumn('vehicle_plate_place');
        });
    }
};
