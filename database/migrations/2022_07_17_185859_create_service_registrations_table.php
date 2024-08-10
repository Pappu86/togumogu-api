<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_registrations', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0)->comment('publish status');
            $table->unsignedBigInteger('customer_id')->nullable()->comment('The Service registration relation with customer');;
            $table->unsignedBigInteger('service_id')->nullable()->comment('The Service registration relation with service');;
            $table->unsignedBigInteger('brand_id')->nullable()->comment('The Service registration relation with brand');;
            $table->json('questions')->nullable()->comment('Additional questions list');
            $table->json('customer_info')->nullable()->comment('Customer static information');
            $table->json('booking_info')->nullable()->comment('Service booking information');

            $table->string('service_reg_no')->nullable()->comment('Service registration number');
            $table->text('comment')->nullable()->comment('comment by customer');
            $table->enum('platform', ['web', 'android', 'ios', 'manual'])->nullable()->comment('ordered platform');
            $table->string('service_reg_status')->nullable()->comment('order status code');
            
            $table->double('price')->nullable()->comment('Service price');
            $table->double('special_price')->nullable()->comment('Service special price');
            
            $table->string('payment_method')->nullable()->comment('payment method code');
            $table->string('payment_status')->nullable()->comment('payment status code');
            
            $table->decimal('current_latitude', 10, 5)->nullable()->comment('Service restration customer current location latitude');
            $table->decimal('current_longitude', 10, 5)->nullable()->comment('Service restration customer current location longitude');
            $table->string('current_location_name')->nullable()->comment('Service restration customer current location name');

            $table->softDeletes()->comment('soft delete datetime');
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
        Schema::dropIfExists('service_registrations');
    }
}
