<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidCustomerDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_devices', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->comment('Customer device uuid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_devices', function (Blueprint $table) {
            $table->dropColumn(['uuid']);
        });
    }
}
