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
        Schema::table('reviews', function (Blueprint $table) {
            
            DB::statement("ALTER TABLE booking_reviews DROP CONSTRAINT booking_reviews_status_check");

            $types = ['pending','approve','disapprove'];
            $result = join( ', ', array_map(function ($value){
                return sprintf("'%s'::character varying", $value);
            }, $types));
    
            DB::statement("ALTER TABLE booking_reviews ADD CONSTRAINT booking_reviews_status_check CHECK (status::text = ANY (ARRAY[$result]::text[]))");
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            DB::statement("ALTER TABLE booking_reviews DROP CONSTRAINT booking_reviews_status_check");

            $types = ['active','inactive'];
            $result = join( ', ', array_map(function ($value){
                return sprintf("'%s'::character varying", $value);
            }, $types));
    
            DB::statement("ALTER TABLE booking_reviews ADD CONSTRAINT booking_reviews_status_check CHECK (status::text = ANY (ARRAY[$result]::text[]))");
        });
    }
};
