<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLongLinkIntoDaycaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daycares', function (Blueprint $table) {
            $table->text('longLink')->nullable()->after('shortLink')->comment('The generated long Dynamic Link.');
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
            $table->dropColumn(['longLink']);
        });
    }
}
