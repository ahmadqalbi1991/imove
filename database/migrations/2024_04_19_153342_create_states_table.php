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
        Schema::create('states', function (Blueprint $table) {
            $table->bigIncrements('id'); // Use bigIncrements for auto-incrementing bigint primary key
            $table->string('name', 100)->notNull();
            $table->smallInteger('active')->default(1)->notNull();
            $table->unsignedInteger('country_id')->nullable(); // Allow null values for country_id
            $table->boolean('deleted')->default(false); // Use boolean for deleted flag
            $table->timestamps(true); // Include both created_at and updated_at with timestamps()
          });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('states');
    }
};
