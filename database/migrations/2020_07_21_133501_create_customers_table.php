<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('mobile')->nullable()->unique();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->text('avatar')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('check, if active or inactive');
            $table->string('password')->nullable();
            $table->string('gender')->nullable();
            $table->boolean('newsletter')->default(0);
            $table->string('parent_type')->nullable()->comment("Child's parent type");
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
        Schema::dropIfExists('customers');
    }
}
