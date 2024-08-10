<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogArticleTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_article_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->string('locale')->index();

            $table->string('title')->nullable()->comment('article title');
            $table->string('slug')->nullable()->unique()->comment('article slug');
            $table->longText('excerpt')->nullable()->comment('article excerpt');
            $table->longText('content')->nullable()->comment('article content');
            $table->string('meta_title')->nullable()->comment('article seo meta title');
            $table->longText('meta_description')->nullable()->comment('article seo meta description');
            $table->longText('meta_keyword')->nullable()->comment('article seo meta keyword');

            $table->unique(['article_id', 'locale']);
            $table->foreign('article_id')->references('id')->on('blog_articles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_article_translations');
    }
}
