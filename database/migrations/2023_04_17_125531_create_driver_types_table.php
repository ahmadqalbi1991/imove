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
        Schema::create('account_types', function (Blueprint $table) {
            $table->integer('id');
            $table->string('type');
            $table->timestamps();
        });

        DB::table('account_types')->insert(['id' => 0, 'type' => 'Individual']);
        DB::table('account_types')->insert(['id' => 1, 'type' => 'Company']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('account_types');
    }
};
