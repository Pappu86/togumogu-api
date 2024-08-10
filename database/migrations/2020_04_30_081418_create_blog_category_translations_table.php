<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogCategoryTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_category_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->comment('blog category id');
            $table->string('locale')->index()->comment('language');
            $table->string('name')->unique()->index()->comment('blog category name');
            $table->string('slug')->unique()->comment('blog category slug');
            $table->text('description')->nullable()->comment('blog category description');
            $table->string('meta_title')->nullable()->comment('blog category seo meta title');
            $table->text('meta_description')->nullable()->comment('blog category seo meta description');
            $table->text('meta_keyword')->nullable()->comment('blog category seo meta keyword');

            $table->unique(['category_id', 'locale']);
            $table->foreign('category_id')->references('id')->on('blog_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_category_translations');
    }
}
