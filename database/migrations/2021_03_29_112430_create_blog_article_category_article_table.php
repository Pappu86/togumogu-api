<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogArticleCategoryArticleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_article_category_article', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id')->comment('Article id');
            $table->unsignedBigInteger('category_id')->comment('Article category id');

            $table->unique(['category_id', 'article_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_article_category_article');
    }
}
