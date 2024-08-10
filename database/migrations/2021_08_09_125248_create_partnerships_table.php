<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnershipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partnerships', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('inactive')->comment('check, if active or inactive');
            $table->foreignId('company_id')->nullable()->comment('Company id of corporate partnership');
            $table->float('discount')->nullable()->comment('Discount of corporate partnership');
            $table->boolean('is_free_shipping')->default(0)->comment('Partnership free shipping');
            $table->integer('free_shipping_spend')->nullable()->comment('Minimum spend for Free shipping (Active when yes)');
            $table->foreignId('coupon_id')->nullable()->comment('Coupon id of corporate partnership');
            $table->foreignId('group_id')->nullable()->comment('Group id of corporate partnership');
            $table->text('offer_image')->nullable()->comment('Partnership Offer Image:');
            $table->string('hotline_number')->nullable()->comment('Partnership hotline phone number');
            $table->string('offer_code')->nullable()->comment('Partnership offer code');
            $table->string('pse')->nullable()->comment('Partnership PSE');
            $table->string('togumogu_customer_offer')->nullable()->comment('Partnership togumogu customer offer');
            $table->dateTime('start_date')->nullable()->comment('Partnership starting date');
            $table->dateTime('expiration_date')->nullable()->comment('Partnership expiration date');
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
        Schema::dropIfExists('partnerships');
    }
}
