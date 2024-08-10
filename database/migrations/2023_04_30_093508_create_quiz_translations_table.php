<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('title')->nullable()->comment('Quiz title');
            $table->string('sub_title')->nullable()->comment('Quiz sub-title'); 
            $table->string('slug')->nullable()->comment('Quiz slug');
            $table->longText('description')->nullable()->comment('Quiz description');
            $table->longText('meta_description')->nullable()->comment('Quiz meta description');
            $table->longText('meta_keyword')->nullable()->comment('Quiz meta keyword');
            $table->string('button_text')->nullable()->comment('Quiz button text');
            $table->longText('terms_and_conditions')->nullable()->comment('Quiz terms and conditions');   
            $table->text('ending_msg')->nullable();
            $table->unique(['quiz_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_translations');
    }
}
