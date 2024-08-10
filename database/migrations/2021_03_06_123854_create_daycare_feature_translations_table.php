<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaycareFeatureTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daycare_feature_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daycare_feature_id')->constrained('daycare_features')->cascadeOnDelete();
            $table->string('locale')->index()->comment('language');
            $table->string('title')->nullable()->comment('Daycare feature title');

            $table->unique(['daycare_feature_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daycare_feature_translations');
    }
}
