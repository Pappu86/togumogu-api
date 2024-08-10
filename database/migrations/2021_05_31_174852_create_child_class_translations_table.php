<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChildClassTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('child_class_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_class_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index()->comment('language');
            $table->string('name')->unique()->comment('Class name');
            $table->unique(['child_class_id', 'locale']);
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
        Schema::dropIfExists('child_class_translations');
    }
}
