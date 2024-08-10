<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedDecimal('purchased_price', 20)->default(0)->comment('product purchased price');
            $table->unsignedDecimal('selling_unit_price', 20)->default(0)->comment('product selling unit price without discount');
            $table->unsignedDecimal('regular_unit_price', 20)->default(0)->comment('product regular unit price without discount');
            $table->unsignedDecimal('selling_price', 20)->default(0)->comment('product selling price with discount');
            $table->unsignedInteger('quantity')->nullable()->comment('product quantity');

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
        Schema::dropIfExists('order_products');
    }
}
