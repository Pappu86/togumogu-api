<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('customer_id')->nullable()->comment('Post created by customer or admin');
            $table->text('title')->nullable()->comment('Post Title');
            $table->longText('content')->nullable()->comment('Post content');
            $table->string('slug')->nullable()->comment('Post slug');
            $table->enum('status', ['active', 'inactive'])->default('inactive')->comment('check, if active or inactive');
            $table->enum('visible', [0, 1])->default(1)->comment('check, if customer visibility');
            $table->enum('is_anonymous', [0, 1])->default(0)->comment('When will be customer anonymous name');
            $table->unsignedBigInteger('share_count')->nullable()->comment('Post share count');
            $table->unsignedBigInteger('view_count')->nullable()->comment('Post view count');
            $table->foreignId('age_group_id')->nullable()->comment('Post age group Id');

            $table->softDeletes();
            $table->timestamps();
            $table->unique(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
