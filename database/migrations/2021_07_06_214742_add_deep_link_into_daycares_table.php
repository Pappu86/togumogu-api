<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeepLinkIntoDaycaresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daycares', function (Blueprint $table) {
            $table->string('shortLink')->nullable()->after('meta_image')->comment('The generated short Dynamic Link.');
            $table->string('previewLink')->nullable()->after('shortLink')->comment('A link to a flowchart of the Dynamic Link\'s behavior.');
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
            $table->dropColumn(['shortLink', 'previewLink']);
        });
    }
}
