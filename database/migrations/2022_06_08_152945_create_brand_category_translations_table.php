<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandCategoryTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_category_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->comment('Brand category id');
            $table->string('locale')->index()->comment('language');
            $table->string('name')->unique()->index()->comment('Brand category name');
            $table->string('slug')->unique()->comment('Brand category slug');
            $table->text('description')->nullable()->comment('Brand category description');
            $table->string('meta_title')->nullable()->comment('Brand category seo meta title');
            $table->text('meta_description')->nullable()->comment('Brand category seo meta description');
            $table->text('meta_keyword')->nullable()->comment('Brand category seo meta keyword');

            $table->unique(['category_id', 'locale']);
            $table->foreign('category_id')->references('id')->on('brand_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand_category_translations');
    }
}
