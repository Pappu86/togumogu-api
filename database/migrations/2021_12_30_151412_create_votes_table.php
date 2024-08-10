<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('check, if active or inactive');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('post_id')->nullable();
            $table->unsignedInteger('comment_id')->nullable();
            $table->boolean('like')->nullable()->comment("Post or Comment likeable by customer ");
            $table->boolean('dislike')->nullable()->comment("Post or Comment dislikeable by customer ");
            $table->boolean('love')->nullable()->comment("Post or Comment love by customer ");
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
        Schema::dropIfExists('votes');
    }
}
