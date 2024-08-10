<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaycaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daycares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('daycare_category_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('inactive')->comment('check, if active or inactive');
            $table->boolean('is_featured')->default(0)->comment('featured day care');
            $table->text('image')->nullable()->comment('day care image');
            $table->text('meta_image')->nullable()->comment('day care seo meta image');
            $table->string('code')->nullable()->comment('day care code');
            $table->float('tgmg_rating', 2, 1)->default(5);
            $table->float('customer_rating', 2, 1)->default(0);
            $table->decimal('latitude', 10, 5)->nullable()->comment('location latitude');
            $table->decimal('longitude', 11, 5)->nullable()->comment('location longitude');
            $table->json('contact')->nullable()->comment('name, email, mobile, website');
            $table->json('social_links')->nullable()->comment('social links');
            $table->integer('year')->nullable()->comment('establishment year');
            $table->integer('rooms')->nullable()->comment('total rooms');
            $table->integer('care_givers')->nullable()->comment('total care givers');
            $table->integer('capacity')->nullable()->comment('total children capacity');
            $table->integer('booked')->nullable()->comment('already booked');
            $table->integer('area')->nullable()->comment('total area in sq. feet');
            $table->json('age_range')->nullable()->comment('age range');
            $table->json('time_range')->nullable()->comment('time range');
            $table->json('opening_days')->nullable()->comment('opening days');
            $table->json('fees')->nullable()->comment('total fees');

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
        Schema::dropIfExists('daycares');
    }
}
