<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaycareDaycareFeatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daycare_daycare_feature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daycare_id')->constrained()->cascadeOnDelete();
            $table->foreignId('daycare_feature_id')->constrained('daycare_features')->cascadeOnDelete();
            $table->boolean('active')->default(0);

            $table->unique(['daycare_id', 'daycare_feature_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daycare_daycare_feature');
    }
}
