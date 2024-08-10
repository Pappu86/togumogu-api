<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('check, if active or inactive');
            $table->enum('type', ['customer', 'partnership'])->nullable()->comment('type, customer or partnership');
            $table->foreignId('customer_id')->unique()->nullable()->comment('Referral customer');
            $table->foreignId('partnership_id')->unique()->nullable()->comment('Referral partnership');
            $table->foreignId('reference_id')->nullable()->comment('Reference customer partnership');
            $table->string('uid')->unique()->comment('Referral sender UID');
            $table->string('url')->nullable()->comment('Referral sender url');
            $table->string('dynamic_url')->nullable()->comment('Referral sender dynamic url');
            $table->string('preview_url')->nullable()->comment('Referral sender preview url');
            
            $table->softDeletes();
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
        Schema::dropIfExists('referral');
    }
}
