<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0)->comment('publish status');
            $table->boolean('is_togumogu_offer')->default(1)->comment('The offer for togumogu partner');
            $table->boolean('is_free')->default(1)->comment('The offer is free or not');
            $table->boolean('is_promoted')->default(0)->comment('Offer promoted');
            $table->boolean('is_featured')->default(0)->comment('Offer featured');
            $table->integer('reward_amount')->default(0)->comment('Reward amount');
            $table->integer('validity_day')->default(0)->comment('Offer Validity days');
            $table->string('image')->nullable()->comment('Offer image');
            $table->string('card_image')->nullable()->comment('Card image');
            $table->text('video_url')->nullable()->comment('offer video url');
            $table->unsignedBigInteger('brand_id')->nullable()->comment('The offer relation with brand');;
            $table->string('coupon')->nullable()->comment('Coporate coupon code');;
            $table->dateTime('start_date')->nullable()->comment('Offer start date');
            $table->dateTime('end_date')->nullable()->comment('Offer end date');

            $table->string('website_url')->nullable()->comment('Website url');
            $table->string('website_btn')->nullable()->comment('Website button text');
            $table->string('store_location_url')->nullable()->comment('Store location url');
            $table->string('store_location_btn')->nullable()->comment('Website button text');

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
        Schema::dropIfExists('offers');
    }
}
