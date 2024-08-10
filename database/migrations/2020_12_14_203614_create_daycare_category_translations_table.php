<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaycareCategoryTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daycare_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daycare_category_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index()->comment('language');
            $table->string('name')->nullable()->comment('category name');
            $table->string('slug')->nullable()->comment('category slug');
            $table->text('description')->nullable()->comment('category description');

            $table->unique(['daycare_category_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daycare_category_translations');
    }
}
