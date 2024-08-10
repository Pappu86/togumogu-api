<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrackerTrackerStartDayTrackerEndDayTrackerRangeVideoUrlProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
           
            //Tracker
            $table->string('tracker')->default('other')->comment('tracker');
            $table->bigInteger('tracker_start_day')->nullable()->comment('tracker start day');
            $table->bigInteger('tracker_end_day')->nullable()->comment('tracker end day');
            $table->json('tracker_range')->nullable()->comment('tracker start range and end range');
            $table->text('video_url')->nullable()->comment('product video url');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('tracker', 'tracker_start_day','tracker_end_day', 'tracker_range', 'video_url');
        });
    }
}
