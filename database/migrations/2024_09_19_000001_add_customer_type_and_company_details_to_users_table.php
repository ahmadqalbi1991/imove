<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerTypeAndCompanyDetailsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('customer_type')->nullable()->after('email'); // Add customer_type with default null
            $table->string('company_name')->nullable()->after('customer_type'); // Add company_name with default null
            $table->string('trade_license')->nullable()->after('company_name'); // Add trade_license with default null
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['customer_type', 'company_name', 'trade_license']);
        });
    }
}
