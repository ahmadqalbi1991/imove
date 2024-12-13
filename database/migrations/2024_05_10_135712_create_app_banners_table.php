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
        Schema::create('app_banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('banner_title', 600);
            $table->string('banner_image', 600);
            $table->tinyInteger('active')->default(1);
            $table->unsignedInteger('created_by')->default(0);
            $table->unsignedInteger('updated_by')->default(0);
            $table->integer('type')->default(0)->nullable();
            $table->integer('category')->default(0);
            $table->integer('product')->default(0);
            $table->integer('service')->default(0);
            $table->integer('banner_type')->default(0)->nullable();
            $table->integer('activity')->default(0);
            $table->integer('store')->default(0);
            $table->string('url', 1600)->nullable();
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
        Schema::table('app_banners', function (Blueprint $table) {
            //
        });
    }
};
