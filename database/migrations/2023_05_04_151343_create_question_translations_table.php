<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->string('locale')->index();
       
            $table->string('title')->nullable()->comment('title');
            $table->string('link_text')->nullable()->comment('link text');
            $table->longText('description')->nullable()->comment('description');
            $table->text('hint')->nullable()->comment('Hint');

            $table->unique(['question_id', 'locale']);
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_translations');
    }
}
