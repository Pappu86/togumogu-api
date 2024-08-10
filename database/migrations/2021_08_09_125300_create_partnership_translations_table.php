<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnershipTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partnership_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partnership_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index()->comment('language');
            $table->string('special_note')->nullable()->comment('Special Note of partnership');
            $table->longtext('details')->nullable()->comment('Partnership details');
            $table->longtext('offer_text')->nullable()->comment('Partnership offer text');
            $table->longtext('offer_instruction')->nullable()->comment('Partnership Offer Instruction:');
         
            $table->unique(['partnership_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partnership_translations');
    }
}
