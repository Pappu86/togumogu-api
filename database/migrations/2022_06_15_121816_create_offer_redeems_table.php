<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferRedeemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_redeems', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0)->comment('publish status');
            $table->integer('spent_reward_point')->default(0)->comment('Spent Reward amount');
            $table->integer('validity_day')->default(0)->comment('Offer redeem Validity days');
            $table->unsignedBigInteger('offer_id')->nullable()->comment('The offer redeem relation with offer');;
            $table->unsignedBigInteger('brand_id')->nullable()->comment('The offer relation with brand');;
            $table->unsignedBigInteger('customer_id')->nullable()->comment('The offer relation with customer');;
            $table->dateTime('start_date')->nullable()->comment('Offer start date');
            $table->dateTime('expired_date')->nullable()->comment('Offer expired date');
            $table->string('coupon')->nullable()->comment('Coporate coupon code');;
            $table->string('offer_redeem_no')->nullable()->comment('offer redeem no number');
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
        Schema::dropIfExists('offer_redeems');
    }
}
