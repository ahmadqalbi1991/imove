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
        Schema::create('temp_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->nullable();
            $table->string('email', 255)->unique();
            $table->string('dial_code', 255)->nullable();
            $table->string('phone', 255)->nullable();
            $table->boolean('phone_verified')->default(false);
            $table->boolean('active')->default(false);
            $table->boolean('email_verified')->default(false);
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('role', 255)->nullable();
            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('user_image', 255)->nullable();
            $table->string('user_phone_otp', 255)->nullable();
            $table->string('user_email_otp', 255)->nullable();
            $table->string('user_device_token', 255)->nullable();
            $table->string('user_device_type', 255)->nullable();
            $table->string('user_access_token', 255)->nullable();
            $table->string('firebase_user_key', 255)->nullable();
            $table->string('device_cart_id', 255)->nullable();
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
        Schema::dropIfExists('temp_users');
    }
};
