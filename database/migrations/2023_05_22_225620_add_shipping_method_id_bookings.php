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
        Schema::table('bookings', function (Blueprint $table) {
            // $table->integer('shipping_method_id')->nullable();
            // $table->string('invoice_number')->nullable();
            // $table->double('border_charges')->default(0);
            // $table->double('shipping_charges')->default(0);
            // $table->double('waiting_charges')->default(0);
            // $table->double('custom_charges')->default(0);
            // $table->double('cost_of_truck')->default(0);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // $table->dropColumn('shipping_method_id');
            // $table->dropColumn('invoice_number');
            // $table->dropColumn('border_charges');
            // $table->dropColumn('shipping_charges');
            // $table->dropColumn('waiting_charges');
            // $table->dropColumn('custom_charges');
            // $table->dropColumn('cost_of_truck');
        });
    }
};
