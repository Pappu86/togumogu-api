<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferCategoryOfferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_category_offer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('offer_id')->comment('Offer id');
            $table->unsignedBigInteger('category_id')->comment('Brand category id');

            $table->unique(['category_id', 'offer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_category_offer');
    }
}
