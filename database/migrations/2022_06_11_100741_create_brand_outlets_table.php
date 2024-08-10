<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandOutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_outlets', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0)->comment('publish status');
            $table->string('website_link')->nullable()->comment('Brand outlet website link');
            $table->string('google_map_link')->nullable()->comment('Brand outlet google map link');
            $table->unsignedBigInteger('brand_id')->nullable()->comment('The outlet relation with brand');;

            $table->bigInteger('area_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('division_id')->nullable();
            $table->string('address_line')->nullable();
            $table->string('country')->default('Bangladesh');
            $table->decimal('latitude', 10, 5)->nullable()->comment('location latitude');
            $table->decimal('longitude', 11, 5)->nullable()->comment('location longitude');
           
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
        Schema::dropIfExists('brand_outlets');
    }
}
