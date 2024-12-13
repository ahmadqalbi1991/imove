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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('sender_id');
            $table->enum('admin_response',['pending','approved','rejected']);
            $table->integer('qouted_amount')->nullable();
            $table->integer('comission_amount')->nullable();
            $table->integer('customer_signature')->nullable();
            $table->text('delivery_note')->nullable();
            $table->unsignedBigInteger('company_id');
            $table->enum('status',['customer_requested','company_qouted','customer_accepted','cancelled','item_collected','on_the_way','delivered'])->default('customer_requested');
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
        Schema::dropIfExists('bookings');
    }
};
