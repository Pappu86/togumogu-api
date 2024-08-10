<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->string('locale')->index();
       
            $table->string('title')->nullable()->comment('Service title');
            $table->string('slug')->nullable()->unique()->comment('Service slug');
            $table->longText('short_description')->comment('service short description');
            $table->longText('long_description')->nullable()->comment('service long description');
            $table->string('later_btn_text')->nullable()->comment('Later button text');
            $table->string('now_btn_text')->nullable()->comment('Now button text');
            $table->string('external_btn_text')->nullable()->comment('External button text');
            $table->string('external_url_btn_text')->nullable()->comment('External button text');
            $table->string('reg_btn_text')->nullable()->comment('Registration button text');
            $table->string('booking_btn_text')->nullable()->comment('Booking button text');
            $table->string('cta_btn_text')->nullable()->comment('Primary CTA button text');
            $table->text('special_price_message')->nullable()->comment('Special Price Message');


            $table->unique(['service_id', 'locale']);
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_translations');
    }
}
