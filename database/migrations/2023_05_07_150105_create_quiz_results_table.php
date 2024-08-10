<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('check, if active or inactive');
            $table->unsignedBigInteger('customer_id')->nullable()->comment('If has customer this id');
            $table->unsignedBigInteger('quiz_id')->nullable()->comment('Quiz relation id');
            
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('referral_url')->nullable()->comment('The generated short Dynamic Link.');
 
            $table->integer('quiz_score')->nullable();
            $table->integer('answerer_score')->nullable();
            $table->dateTime('view_time')->nullable();
            $table->dateTime('submit_time')->nullable();
            $table->dateTime('taken_time')->nullable();
            $table->enum('submission_status', ['pending','success'])->default('pending')->comment('Quiz submisstion status');
            
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
        Schema::dropIfExists('quiz_results');
    }
}
