<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChildClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('child_classes', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('check, if active or inactive');
            $table->foreignId('school_id')->nullable()->comment("Class of school");
            $table->string('type')->nullable()->comment('Class type');
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
        Schema::dropIfExists('child_classes');
    }
}
