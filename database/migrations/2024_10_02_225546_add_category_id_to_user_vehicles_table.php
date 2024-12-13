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
            $table->unsignedBigInteger('category_id')->nullable()->after('vehicle_name');  // Add nullable category_id column
            
            // Optionally, if you have a categories table, you can define the foreign key constraint
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
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
            $table->dropColumn('category_id');  // Drop the column if the migration is rolled back
        });
    }
};
