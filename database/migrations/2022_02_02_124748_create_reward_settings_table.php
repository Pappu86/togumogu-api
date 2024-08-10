<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reward_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('inactive')->comment('check, if active or inactive');
            $table->string('category')->unique()->comment('Reward category settings');
            $table->enum('type', ['percentage', 'fixed'])->default('percentage')->comment('Reward type');
            $table->integer('award_points')->nullable()->comment('Reward points');
            $table->integer('min_amount')->nullable()->comment('Reward min Order amount');
            $table->integer('max_award_points')->nullable()->comment('Reward max points');
            $table->dateTime('start_date')->nullable()->comment('Start date of award');
            $table->dateTime('end_date')->nullable()->comment('End date of award');
            $table->json('platforms')->nullable()->comment('Reward platforms');
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
        Schema::dropIfExists('reward_settings');
    }
}
