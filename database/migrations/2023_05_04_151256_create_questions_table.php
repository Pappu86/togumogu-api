<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('inactive')->comment('check, if active or inactive');                      
            
            $table->foreignId('quiz_id')->nullable();
            $table->unsignedBigInteger('user_id')->comment('admin id');

            $table->boolean('is_multiple')->default(0)->comment('The question is multiple or single');
            $table->text('image')->nullable()->comment('image url');
            $table->text('audio')->nullable()->comment('audio url');
            $table->text('video')->nullable()->comment('video url');
            $table->text('link')->nullable()->comment('link url');
            $table->integer('time')->nullable();
            $table->integer('score')->nullable();
            $table->integer('serial_no')->nullable();
            $table->enum('type', ['only-text','only-image','text-image','audio'])->default('only-text')->comment('check, question type');                      
            
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
        Schema::dropIfExists('questions');
    }
}
