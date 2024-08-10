<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostFavouritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_favourites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->comment('Customer id');
            $table->unsignedBigInteger('post_id')->comment('Post id');
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
        Schema::dropIfExists('post_favourites');
    }
}
