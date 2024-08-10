<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceCategoryServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_category_service', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id')->comment('Service id');
            $table->unsignedBigInteger('category_id')->comment('Service category id');

            $table->unique(['category_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_category_service');
    }
}
