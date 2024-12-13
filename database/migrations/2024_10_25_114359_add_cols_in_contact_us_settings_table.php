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
        Schema::table('contact_us_settings', function (Blueprint $table) {
            $table->string('tiktok', 600)->nullable();
            $table->string('snapchat', 600)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact_us_settings', function (Blueprint $table) {
            $table->dropColumn(['snapchat', 'tiktok']);
        });
    }
};
