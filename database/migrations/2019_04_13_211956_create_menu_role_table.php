<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_role', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_id')->comment('menus table id');
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');

            $table->unsignedBigInteger('role_id')->comment('roles table id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->primary(['menu_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_role');
    }
}
