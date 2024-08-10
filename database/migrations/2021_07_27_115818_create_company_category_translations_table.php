<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyCategoryTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_category_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index()->comment('language');
            $table->string('name')->nullable()->comment('category name');
            $table->string('slug')->nullable()->comment('category slug');
            $table->text('description')->nullable()->comment('category description');

            $table->unique(['company_category_id', 'locale']);
    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_category_translations');
    }
}
