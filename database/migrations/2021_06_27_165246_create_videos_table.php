<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('video author id');
            $table->boolean('status')->default(0)->comment('publish status');
            $table->string('image')->nullable()->comment('video image');
            $table->string('meta_image')->nullable()->comment('video seo meta image');
            $table->boolean('is_featured')->default(0)->comment('featured video');
            $table->unsignedBigInteger('view_count')->default(0)->comment('video view count');
            $table->dateTime('datetime')->nullable()->comment('publish date and time of video');
            $table->text('url')->nullable()->comment('video url');
            $table->enum('video_language', ['bn', 'en'])->default('bn')->comment('Video language');
            $table->enum('video_type', ['recorded', 'recorded_live', 'live'])->default('recorded')->comment('Video type');
            $table->dateTime('live_start')->nullable()->comment('Video live start time');
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
        Schema::dropIfExists('videos');
    }
}
