<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('dial_code')->nullable();
            $table->string('phone')->nullable();
            $table->integer('phone_verified')->default(0);
            $table->string('password')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->unsignedBigInteger('role_id');
            $table->string('user_phone_otp')->nullable();
            $table->string('user_device_token')->nullable();
            $table->string('user_device_type')->nullable();
            $table->string('user_access_token')->nullable();
            $table->string('firebase_user_key')->nullable();
            $table->enum('status',['active','inactive'])->default('inactive');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
        User::create([
            'name'=>'Admin',
            'email'=>'admin@admin.com',
            'dial_code'=>'971',
            'phone'=>'112233445566778899',
            'role_id'=>'1',
            'password'=>\Illuminate\Support\Facades\Hash::make('admin123'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
