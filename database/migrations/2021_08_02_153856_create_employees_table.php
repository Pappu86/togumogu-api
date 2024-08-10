<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('inactive')->comment('check, if active or inactive');
            $table->string('name')->comment('employee name');
            $table->string('email')->nullable()->unique()->comment('employee email');
            $table->string('phone')->unique()->comment('employee phone');
            $table->timestamp('join_date')->nullable()->comment('employee join date');
            $table->boolean('is_registered')->default(0)->comment('Registered user');
            $table->string('company_employee_id')->nullable()->comment('Company employee id of employee');
            $table->foreignId('group_id')->comment('Employee group id of employee');
            $table->foreignId('company_id')->comment('Company id of employee');
            $table->string('designation')->nullable()->comment('Employee designation');
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
        Schema::dropIfExists('employees');
    }
}
