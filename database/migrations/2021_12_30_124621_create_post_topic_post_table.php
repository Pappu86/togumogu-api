<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostTopicPostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_topic_post', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->comment('Post id');
            $table->unsignedBigInteger('topic_id')->comment('Topic id');
            $table->unique(['topic_id', 'post_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_topic_post');
    }
}
