<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHashtaggablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hashtaggables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hashtag_id')->comment('Hash tag id');
            $table->unsignedBigInteger('hashtaggable_id')->comment('Hash tag model id');
            $table->string('hashtaggable_type')->comment('model class name');

            $table->foreign('hashtag_id')->references('id')->on('hashtags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hashtaggables');
    }
}
