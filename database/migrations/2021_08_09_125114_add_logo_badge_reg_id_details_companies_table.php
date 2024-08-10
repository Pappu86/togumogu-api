<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLogoBadgeRegIdDetailsCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->text('logo')->nullable()->comment('Company logo');
            $table->text('badge')->nullable()->comment('Company badge');
            $table->string('reg_id')->nullable()->comment('Company registration id');
            $table->longText('details')->nullable()->comment('Company details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['logo', 'badge', 'reg_id', 'details']);
        });
    }
}
