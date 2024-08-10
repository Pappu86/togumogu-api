<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaycareTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daycare_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daycare_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index()->comment('language');
            $table->string('name')->nullable()->comment('day care name');
            $table->string('slug')->nullable()->comment('day care slug');
            $table->text('description')->nullable()->comment('day care description');
            $table->longText('content')->nullable()->comment('day care description');
            $table->json('location')->nullable()->comment('day care location');
            $table->text('hospital_address')->nullable()->comment('nearest hospital address');
            
            //location 
            $table->text('house')->nullable()->comment('Daycare house location');
            $table->text('street')->nullable()->comment('Daycare street location');
            $table->integer('zip')->nullable()->comment('Daycare zip location');
            $table->integer('division_id')->nullable()->comment('Daycare division location');
            $table->integer('district_id')->nullable()->comment('Daycare disctrict location');
            $table->integer('area_id')->nullable()->comment('Daycare area/thana location');

            $table->string('meta_title')->nullable()->comment('seo meta title');
            $table->longText('meta_description')->nullable()->comment('seo meta description');
            $table->longText('meta_keyword')->nullable()->comment('seo meta keyword');

            $table->unique(['daycare_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daycare_translations');
    }
}
