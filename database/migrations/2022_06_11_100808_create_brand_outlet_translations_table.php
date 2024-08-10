<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandOutletTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_outlet_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_outlet_id');
            $table->string('locale')->index();
       
            $table->string('name')->nullable()->comment('Brand outlet name');
            $table->string('slug')->nullable()->unique()->comment('Brand slug');
            $table->longText('short_description')->nullable()->comment('Brand short description');
          
            $table->unique(['brand_outlet_id', 'locale']);
            $table->foreign('brand_outlet_id')->references('id')->on('brand_outlets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand_outlet_translations');
    }
}
