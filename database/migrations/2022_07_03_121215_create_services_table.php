<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0)->comment('publish status');
            $table->boolean('is_promoted')->default(0)->comment('Service promoted');
            $table->boolean('is_featured')->default(0)->comment('Service featured');
            $table->string('image')->nullable()->comment('Service image');
            $table->text('video_url')->nullable()->comment('Service video url');
            $table->unsignedBigInteger('brand_id')->nullable()->comment('The Service relation with brand');;
            $table->dateTime('start_date')->nullable()->comment('Service start date');
            $table->dateTime('end_date')->nullable()->comment('Service end date'); 
            $table->unsignedBigInteger('view_count')->default(0)->comment('Service view count');
            $table->unsignedBigInteger('registration_count')->default(0)->comment('Service registration count');

            $table->string('type')->nullable()->comment('Service type');
            $table->string('external_url')->nullable()->comment('External url');

            // Price information
            $table->double('price')->nullable()->comment('Service price');
            $table->double('special_price')->nullable()->comment('Service special price');
            $table->dateTime('special_price_start_date')->nullable()->comment('Service start date');
            $table->dateTime('special_price_end_date')->nullable()->comment('Service end date');
            $table->json('payment_method')->nullable()->comment('Service payment methods');
            $table->boolean('is_payment')->default(0)->comment('Is Service payment requred');
            $table->string('external_payment_url')->nullable()->comment('External payment url');

            //Booking information
            $table->boolean('is_booking')->default(0)->comment('Is Service booking requred');
            $table->string('booking_type')->nullable()->comment('Service booking type');
            $table->dateTime('booking_start_date')->nullable()->comment('Service start date');
            $table->dateTime('booking_end_date')->nullable()->comment('Service end date');
            
            //Service provider information
            $table->string('provider_email')->nullable()->comment('Service provider email');
            $table->string('provider_phone')->nullable()->comment('Service provider phone');

            //Registrations information
            $table->boolean('is_reg')->default(0)->comment('Is Service Registration requred');
            $table->boolean('is_customer_name')->default(0)->comment('Is Service Registration user name requred');
            $table->boolean('is_customer_phone')->default(0)->comment('Is Service Registration user phone requred');
            $table->boolean('is_customer_email')->default(0)->comment('Is Service Registration user email requred');
            $table->boolean('is_child_name')->default(0)->comment('Is Service Registration user child age requred');
            $table->boolean('is_child_age')->default(0)->comment('Is Service Registration user child age requred');
            $table->boolean('is_child_gender')->default(0)->comment('Is Service Registration user child gender requred');
          
            //Tracker
            $table->string('tracker')->default('other')->comment('tracker');
            $table->bigInteger('tracker_start_day')->nullable()->comment('tracker start day');
            $table->bigInteger('tracker_end_day')->nullable()->comment('tracker end day');
            $table->json('tracker_range')->nullable()->comment('tracker start range and end range');

            //Additional Qeustions
            $table->boolean('is_additional_qus')->default(0)->comment('Is Service Additional Question requred');
            $table->json('questions')->nullable()->comment('Additional questions list');

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
        Schema::dropIfExists('services');
    }
}
