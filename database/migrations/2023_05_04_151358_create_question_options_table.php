<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('inactive')->comment('check, if active or inactive');   
            
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
           
            $table->boolean('is_answer')->default(0)->comment('The question option is togumogu partner');
            $table->text('image')->nullable()->comment('image url');
            $table->text('audio')->nullable()->comment('audio url');
            $table->text('video')->nullable()->comment('video url');

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
        Schema::dropIfExists('question_options');
    }
}
