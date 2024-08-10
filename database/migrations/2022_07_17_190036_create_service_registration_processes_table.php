<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceRegistrationProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_registration_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_registration_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->comment('admin id');
            $table->string('service_reg_status')->nullable()->comment('order status code');
            $table->text('comment')->nullable()->comment('comment by user');
            $table->boolean('notify')->default(0)->comment('check, if notify to customer');

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
        Schema::dropIfExists('service_registration_processes');
    }
}
