<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('topic_id')->comment('Post topic id');
            $table->string('locale')->index()->comment('language');
            $table->string('name')->unique()->index()->comment('Post topic name');
            $table->string('slug')->unique()->comment('Post topic slug');
            $table->text('description')->nullable()->comment('Topic description');
            $table->unique(['topic_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topic_translations');
    }
}
