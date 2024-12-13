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
        Schema::table('user_vehicles', function (Blueprint $table) {
            // Add nullable string columns for manufacturer_id and model_id
            $table->string('manufacturer_id')->nullable();
            $table->string('model_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_vehicles', function (Blueprint $table) {
            // Drop the manufacturer_id and model_id columns
            $table->dropColumn('manufacturer_id');
            $table->dropColumn('model_id');
        });
    }
};
