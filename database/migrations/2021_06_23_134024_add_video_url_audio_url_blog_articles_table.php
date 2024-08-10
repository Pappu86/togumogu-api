<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoUrlAudioUrlBlogArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blog_articles', function (Blueprint $table) {
            $table->text('video_url')->nullable()->comment('Article video url');
            $table->text('audio_url')->nullable()->comment('Article audio url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blog_articles', function (Blueprint $table) {
            $table->dropColumn('video_url', 'audio_url');
        });
    }
}
