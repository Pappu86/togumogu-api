<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0)->comment('publish status');
            $table->boolean('is_togumogu_partner')->default(1)->comment('The brand is togumogu partner');
            $table->string('website_link')->nullable()->comment('Brand website link');
            $table->decimal('latitude', 10, 5)->nullable()->comment('location latitude');
            $table->decimal('longitude', 11, 5)->nullable()->comment('location longitude');
            $table->string('logo')->nullable()->comment('Brand logo image');
            $table->string('banner')->nullable()->comment('Brand banner image');
            $table->text('video_url')->nullable()->comment('Brand video url');
            $table->unsignedBigInteger('company_id')->nullable()->comment('The brand relation with our corporate company');;

            $table->bigInteger('area_id')->nullable();
            $table->bigInteger('district_id')->nullable();
            $table->bigInteger('division_id')->nullable();
            $table->string('address_line')->nullable();
            $table->string('country')->default('Bangladesh');
            $table->json('social_links')->nullable()->comment('social links');
            
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
        Schema::dropIfExists('brands');
    }
}
