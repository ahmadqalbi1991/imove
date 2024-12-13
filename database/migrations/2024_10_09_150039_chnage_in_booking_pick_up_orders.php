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
        Schema::table('booking_pick_up_orders', function (Blueprint $table) {
            $table->integer('size_id')->nullable()->change();
            $table->integer('care_id')->nullable()->change();
        });
    }
};
