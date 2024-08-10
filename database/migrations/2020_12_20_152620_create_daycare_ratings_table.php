<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaycareRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daycare_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('daycare_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_approved')->default(1)->comment('check if approved');
            $table->unsignedTinyInteger('facility')->default(0)->comment('rating on facility');
            $table->unsignedTinyInteger('fee')->default(0)->comment('rating on fee');
            $table->unsignedTinyInteger('security')->default(0)->comment('rating on security');
            $table->unsignedTinyInteger('hygiene')->default(0)->comment('rating on hygiene');
            $table->unsignedTinyInteger('care_giving')->default(0)->comment('rating on care giving');
            $table->text('comment')->nullable()->comment('rating comment');
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
        Schema::dropIfExists('daycare_ratings');
    }
}
