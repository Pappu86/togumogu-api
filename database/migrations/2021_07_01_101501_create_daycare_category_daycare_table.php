<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaycareCategoryDaycareTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daycare_category_daycare', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('daycare_id')->comment('Daycare id');
            $table->unsignedBigInteger('daycare_category_id')->comment('Daycare category id');

            $table->unique(['daycare_category_id', 'daycare_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daycare_category_daycare');
    }
}
