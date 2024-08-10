<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();

            $table->text('src')->nullable()->comment('image src');
            $table->longText('srcset')->nullable()->comment('image srcset');
            $table->text('lazy')->nullable()->comment('image lazy src');
            $table->boolean('is_featured')->default(0)->comment('image is featured');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_images');
    }
}
