<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFacebookDeepLinkIntoDaycaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daycares', function (Blueprint $table) {
            $table->string('facebookLink')->nullable()->comment('The generated facebook short Dynamic Link.');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daycares', function (Blueprint $table) {
            $table->dropColumn(['facebookLink']);
        });
    }
}
