<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('inactive')->comment('check, if active or inactive');
            $table->foreignId('customer_id')->unique()->nullable()->comment('Reward customer');
            $table->integer('balance')->default(0)->nullable()->comment('Current Reward balance');
            $table->integer('total')->default(0)->nullable()->comment('Total Reward points');
            $table->integer('redeem')->default(0)->nullable()->comment('Total Redeem');
            
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
        Schema::dropIfExists('rewards');
    }
}
