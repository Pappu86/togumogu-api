<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_id');
            $table->string('locale')->index();
       
            $table->string('name')->nullable()->comment('Brand name');
            $table->string('slug')->nullable()->unique()->comment('Brand slug');
            $table->longText('short_description')->comment('Brand short description');
            $table->longText('long_description')->nullable()->comment('Brand long description');

            $table->unique(['brand_id', 'locale']);
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand_translations');
    }
}
