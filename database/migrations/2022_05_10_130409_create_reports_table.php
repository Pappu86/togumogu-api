<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['pending', 'rejected', 'approved'])->default('pending')->comment('check');
            $table->foreignId('customer_id')->comment('Report customer');
            $table->foreignId('reported_id')->nullable()->comment('Reported post/comment or etc.');
            $table->foreignId('reason_id')->nullable()->comment('Report Reason');
            $table->text('note')->nullable()->comment('Report note');
            $table->enum('category', ['post', 'comment'])->comment('Report category of post/comment or etc');

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
        Schema::dropIfExists('reports');
    }
}
