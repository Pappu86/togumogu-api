<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogArticleFilterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_article_filter', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id')->comment('blog article id');
            $table->unsignedBigInteger('filter_id')->comment('common filter id');

            $table->unique(['filter_id', 'article_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_article_filter');
    }
}
