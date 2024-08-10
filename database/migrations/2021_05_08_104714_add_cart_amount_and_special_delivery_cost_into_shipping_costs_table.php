<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCartAmountAndSpecialDeliveryCostIntoShippingCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipping_costs', function (Blueprint $table) {
            $table->double('cart_amount', 20, 2)->nullable()->comment('shipping cost free of cart amount');
            $table->double('special_delivery_cost', 20, 2)->nullable()->comment('special delivery cost is when has free shipping cost offer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipping_costs', function (Blueprint $table) {
            $table->dropColumn('cart_amount');
            $table->dropColumn('special_delivery_cost');
        });
    }
}
