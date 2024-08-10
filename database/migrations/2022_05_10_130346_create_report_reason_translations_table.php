<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportReasonTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_reason_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_reason_id')->comment('reason id');
            $table->string('locale')->index()->comment('language');
            $table->string('title')->unique()->index()->comment('Report reason title');
            $table->string('slug')->unique()->comment('Report reason slug');
            $table->text('description')->nullable()->comment('Report reason description');

            $table->unique(['report_reason_id', 'locale']);
            $table->foreign('report_reason_id')->references('id')->on('report_reasons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_reason_translations');
    }
}
