<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_codes', function (Blueprint $table) {
            $table->id();
            $table->string('mobile')->nullable()->comment('customer mobile number');
            $table->string('email')->nullable()->comment('customer email address');
            $table->string('code')->comment('generated code');
            $table->string('type')->comment('OTP Type');
            $table->timestamp('created_at')->nullable()->comment('generation time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_codes');
    }
}
