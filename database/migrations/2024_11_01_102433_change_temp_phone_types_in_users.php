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
        Schema::table('users', function (Blueprint $table) {
            $table->string('temp_dial_code', 10)->nullable()->change();
            $table->string('temp_mobile', 20)->nullable()->change();
            $table->string('temp_phone', 20)->nullable()->change();
        });
    }
};
