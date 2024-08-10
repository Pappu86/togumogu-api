<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandCategoryBrandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_category_brand', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id')->comment('Brand id');
            $table->unsignedBigInteger('category_id')->comment('Brand category id');

            $table->unique(['category_id', 'brand_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand_category_brand');
    }
}
