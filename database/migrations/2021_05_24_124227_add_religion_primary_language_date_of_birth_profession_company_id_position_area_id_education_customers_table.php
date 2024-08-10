<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReligionPrimaryLanguageDateOfBirthProfessionCompanyIdPositionAreaIdEducationCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('religion')->nullable()->comment('Customer religion');
            $table->string('primary_language')->nullable()->comment('Customer primary language');
            $table->dateTime('date_of_birth')->nullable()->comment('Customer birth day');
            $table->string('profession')->nullable()->comment('Customer profession');
            $table->foreignId('company_id')->nullable()->comment('Customer company status');
            $table->string('position')->nullable()->comment('Customer position of the company');
            $table->foreignId('area_id')->nullable()->comment('Customer location');
            $table->string('education')->nullable()->comment('Customer education');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('religion', 'primary_language', 'date_of_birth', 'profession', 'company_id', 'position', 'area_id', 'education');
        });
    }
}
