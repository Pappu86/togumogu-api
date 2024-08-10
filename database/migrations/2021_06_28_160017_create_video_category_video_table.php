<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoCategoryVideoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_category_video', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('video_id')->comment('Video id');
            $table->unsignedBigInteger('category_id')->comment('Video category id');

            $table->unique(['category_id', 'video_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_category_video');
    }
}
