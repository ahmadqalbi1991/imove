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
        Schema::create('booking_pick_up_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->nullable();
            $table->integer('customer_id');
            $table->integer('category_id');
            $table->string('location');
            $table->string('landmark');
            $table->string('contact_person');
            $table->string('dail_code');
            $table->integer('mobile_no');
            $table->text('description')->nullable();
            $table->string('instruction')->nullable();
            $table->integer('size_id');
            $table->integer('care_id');
            $table->date('date');
            $table->time('time');
            $table->text('delivery_type');
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
        Schema::dropIfExists('booking_pick_up_orders');
    }
};
