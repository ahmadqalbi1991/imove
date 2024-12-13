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
        Schema::table('booking_home_relocations', function (Blueprint $table) {
            $table->string('building_from_no')->nullable()->after('building_from_name');
            $table->string('building_to_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_home_relocations', function (Blueprint $table) {
            $table->dropColumn('building_from_no');
            $table->dropColumn('building_to_no');
        });
    }
};
