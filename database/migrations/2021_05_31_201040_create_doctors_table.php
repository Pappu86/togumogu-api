<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->nullable()->comment("Doctor registration number");
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('check, if active or inactive');
            $table->string('department')->nullable()->comment('Department');
            $table->string('degree')->nullable()->comment('Doctor degree');
            $table->foreignId('hospital_id')->nullable()->comment('Hospital');
            $table->foreignId('area_id')->nullable()->comment('Area');
            $table->string('contact_number')->nullable()->comment('Doctor contact number');
            $table->float('visiting_fee')->nullable()->comment('Doctor visiting fee');
            $table->string('avatar')->nullable()->comment('Doctor profile photo');
            $table->string('website')->nullable()->comment('Doctor website');
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
        Schema::dropIfExists('doctors');
    }
}
