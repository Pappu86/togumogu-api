<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->nullable()->comment('School registration number');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('check, if active or inactive');
            $table->string('type')->nullable()->comment('School type');
            $table->string('address')->nullable()->comment('School address');
            $table->foreignId('area_id')->nullable()->comment('School area');
            $table->foreignId('daycare_id')->nullable()->comment('Daycare');
            $table->string('contact_number')->nullable()->comment('School contact number');
            $table->string('website')->nullable()->comment('School website');
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
        Schema::dropIfExists('schools');
    }
}
