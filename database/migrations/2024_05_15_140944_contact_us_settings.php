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
        Schema::create('contact_us_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title_en', 300);
            $table->string('title_ar', 300);
            $table->string('email', 300)->unique();
            $table->string('mobile', 50)->default('');
            $table->text('desc_en')->nullable();
            $table->text('desc_ar')->nullable();
            $table->text('location')->nullable();
            $table->text('latitude')->nullable();
            $table->text('longitude')->nullable();
            $table->string('twitter', 600)->nullable();
            $table->string('instagram', 600)->nullable();
            $table->string('facebook', 600)->nullable();
            $table->string('youtube', 600)->nullable();
            $table->string('linkedin', 600)->nullable();
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
        Schema::dropIfExists('contact_us_settings');
    }
};
