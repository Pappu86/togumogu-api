<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyCategoryCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_category_company', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->comment('Company id');
            $table->unsignedBigInteger('company_category_id')->comment('Daycare category id');

            $table->unique(['company_category_id', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_category_company');
    }
}
