<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_answers', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('check, if active or inactive');
            $table->unsignedBigInteger('answerer_id')->nullable()->comment('Question answer information Id');
            $table->unsignedBigInteger('customer_id')->nullable()->comment('If has customer this id');
            $table->unsignedBigInteger('quiz_id')->nullable()->comment('Quiz relation id');
            $table->unsignedBigInteger('question_id')->nullable()->comment('Question relation id');
            $table->unsignedBigInteger('question_option_id')->nullable()->comment('Question option relation id');
            $table->json('question')->nullable()->comment('Question information');
            $table->json('question_options')->nullable()->comment('Question options information');
            
            $table->integer('answerer_score')->nullable();
            $table->boolean('is_right_answer')->default(0)->comment('Is right answer of the question');
            $table->json('answer_option')->nullable()->comment('Question answer option');
            
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
        Schema::dropIfExists('question_answers');
    }
}
