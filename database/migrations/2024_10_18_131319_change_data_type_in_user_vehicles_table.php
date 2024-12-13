<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_vehicles', function (Blueprint $table) {
            $table->bigInteger('manufacturer_id')->nullable()->change();
            DB::statement('ALTER TABLE user_vehicles ALTER COLUMN manufacturer_id TYPE BIGINT USING manufacturer_id::bigint');
            $table->bigInteger('model_id')->nullable()->change();
            DB::statement('ALTER TABLE user_vehicles ALTER COLUMN model_id TYPE BIGINT USING manufacturer_id::bigint');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_vehicles', function (Blueprint $table) {
            DB::statement('ALTER TABLE user_vehicles ALTER COLUMN manufacturer_id TYPE INTEGER USING manufacturer_id::integer');
            $table->integer('manufacturer_id')->nullable()->change();
            DB::statement('ALTER TABLE user_vehicles ALTER COLUMN model_id TYPE INTEGER USING model_id::integer');
            $table->integer('model_id')->nullable()->change();
        });
    }
};
