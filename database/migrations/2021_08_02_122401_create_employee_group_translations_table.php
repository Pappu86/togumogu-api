<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeGroupTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_group_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_group_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index()->comment('language');
            $table->string('name')->nullable()->comment('employee group name');
            $table->string('slug')->nullable()->comment('employee group slug');
            $table->text('details')->nullable()->comment('employee group details');

            $table->unique(['employee_group_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_group_translations');
    }
}
