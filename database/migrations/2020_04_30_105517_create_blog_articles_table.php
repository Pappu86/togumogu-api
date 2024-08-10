<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlogArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blog_articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('article author id');
            $table->boolean('status')->default(0)->comment('publish status');
            $table->string('image')->nullable()->comment('article image');
            $table->string('meta_image')->nullable()->comment('article seo meta image');
            $table->boolean('is_featured')->default(0)->comment('featured article');
            $table->unsignedBigInteger('view_count')->default(0)->comment('article view count');
            $table->dateTime('datetime')->nullable()->comment('publish date and time of article');
            $table->softDeletes();

            //Tracker
            $table->string('tracker')->default('other')->comment('tracker');
            $table->bigInteger('tracker_start_day')->nullable()->comment('tracker start day');
            $table->bigInteger('tracker_end_day')->nullable()->comment('tracker end day');
            $table->json('tracker_range')->nullable()->comment('tracker start range and end range');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_articles');
    }
}
