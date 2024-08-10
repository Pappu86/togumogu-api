<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('check, if active or inactive');
            $table->foreignId('customer_id')->comment('Reward customer');
            $table->integer('debit')->default(0)->nullable()->comment('Reward debit');
            $table->integer('credit')->default(0)->nullable()->comment('Reward credit');
            $table->foreignId('reward_setting_id')->nullable()->comment('Reward setting');
            $table->string('category')->comment('Reward transaction category');
            $table->string('action')->comment('Reward transaction action');
            $table->foreignId('reference_id')->comment('Reward reference');
            $table->text('description')->nullable()->comment('Reward transaction note');
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
        Schema::dropIfExists('reward_transactions');
    }
}
