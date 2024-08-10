<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameGenderReligionBloodGroupDoctorSchoolIdClassIdChildrenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('children', function (Blueprint $table) {
            $table->string('name')->nullable()->comment('Child name');
            $table->string('gender')->nullable()->comment('Child gender');
            $table->string('religion')->nullable()->comment('Child religion');
            $table->json('doctor_id')->nullable()->comment('Child checkup doctors list');
            $table->foreignId('school_id')->nullable()->comment('Child current school');
            $table->foreignId('child_class_id')->nullable()->comment('Child current class name');
            $table->string('blood_group')->nullable()->comment('Child blood group');  
            $table->string('birth_registration_number')->nullable()->comment('Child birth certificate id');  
            $table->string('birth_location')->nullable()->comment('Child birth location');  
            $table->foreignId('birth_hospital_id')->nullable()->comment('Child birth hospital');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn('name', 'gender', 'religion', 'doctor_id', 'school_id', 'child_class_id', 'blood_group', 'birth_registration_number', 'birth_location', 'birth_hospital_id');
        });
    }
}
