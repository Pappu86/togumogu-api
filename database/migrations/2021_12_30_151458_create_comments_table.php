<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('check, if active or inactive');
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('parent_id')->nullable();
            $table->longText('content');
            $table->unsignedInteger('commentable_id')->nullable();
            $table->string('commentable_type');
            
            $table->softDeletes();
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
        Schema::dropIfExists('comments');
    }
}
