<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('video_id');
            $table->string('locale')->index();
       
            $table->string('title')->nullable()->comment('Video title');
            $table->string('slug')->nullable()->unique()->comment('Video slug');
            $table->longText('excerpt')->nullable()->comment('Video excerpt');
            $table->longText('content')->nullable()->comment('Video content');
            $table->string('meta_title')->nullable()->comment('Video seo meta title');
            $table->longText('sub_title')->nullable()->comment('Video subtitle');
            $table->longText('meta_description')->nullable()->comment('Video seo meta description');
            $table->longText('meta_keyword')->nullable()->comment('Video seo meta keyword');
       
            $table->unique(['video_id', 'locale']);
            $table->foreign('video_id')->references('id')->on('videos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_translations');
    }
}
