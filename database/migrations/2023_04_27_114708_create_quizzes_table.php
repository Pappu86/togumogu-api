<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('inactive')->comment('check, if active or inactive');                      
            $table->json('platforms')->nullable()->comment('Quiz platforms');
            $table->json('area')->nullable()->comment('Allowed area of quiz');            
            $table->integer('max_time')->nullable()->comment('Quiz max time');
            $table->integer('total_points')->nullable()->comment('Quiz total points');
            $table->integer('reward_points')->nullable()->comment('Quiz reward points');
            $table->string('image')->nullable()->comment('Quiz image');
            $table->dateTime('start_date')->nullable()->comment('Start date of quiz');
            $table->dateTime('end_date')->nullable()->comment('End date of quiz');            
            $table->string('dynamic_link')->nullable()->comment('Quiz dynamic link'); 
            $table->boolean('is_featured')->default(0)->comment('Quiz is featured');
            $table->string('color')->nullable()->comment('Quiz theme color');  
            $table->boolean('retry_allow')->default(0)->comment('Quiz retry allow');
            
            //Tracker
            $table->string('tracker')->default('other')->comment('tracker');
            $table->bigInteger('tracker_start_day')->nullable()->comment('tracker start day');
            $table->bigInteger('tracker_end_day')->nullable()->comment('tracker end day');
            $table->json('tracker_range')->nullable()->comment('tracker start range and end range');
            
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
        Schema::dropIfExists('quizzes');
    }
}
