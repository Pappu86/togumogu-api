<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenstrualCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menstrual_calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('check, if active or inactive');
            $table->string('month')->nullable()->comment('Menstrual month');
            $table->dateTime('start_date')->nullable()->comment('Menstrual start date');
            $table->integer('active_days')->default(4)->comment('Menstrual active days');
            $table->integer('cycle_length')->default(28)->comment('Menstrual cycle length');
            $table->dateTime('ovulation_date')->nullable()->comment('Menstrual predicted ovulation date');
            $table->dateTime('next_cycle_date')->nullable()->comment('Menstrual predicted next cycle date');            
            $table->dateTime('next_ovulation_date')->nullable()->comment('Menstrual next predicted ovulation date');
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
        Schema::dropIfExists('menstrual_calendars');
    }
}
