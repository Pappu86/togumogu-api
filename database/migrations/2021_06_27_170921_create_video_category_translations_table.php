<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoCategoryTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_category_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->comment('Video category id');
            $table->string('locale')->index()->comment('language');
            $table->string('name')->unique()->index()->comment('Video category name');
            $table->string('slug')->unique()->comment('Video category slug');
            $table->text('description')->nullable()->comment('Video category description');
            $table->string('meta_title')->nullable()->comment('Video category seo meta title');
            $table->text('meta_description')->nullable()->comment('Video category seo meta description');
            $table->text('meta_keyword')->nullable()->comment('Video category seo meta keyword');

            $table->unique(['category_id', 'locale']);
            $table->foreign('category_id')->references('id')->on('video_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_category_translations');
    }
}
