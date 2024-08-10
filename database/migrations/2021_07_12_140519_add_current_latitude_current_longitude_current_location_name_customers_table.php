<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentLatitudeCurrentLongitudeCurrentLocationNameCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('current_latitude', 10, 5)->nullable()->comment('Customer current location latitude');
            $table->decimal('current_longitude', 10, 5)->nullable()->comment('Customer current location longitude');
            $table->string('current_location_name')->nullable()->comment('Customer current location name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['current_latitude', 'current_longitude', 'current_location_name']);
        });
    }
}
