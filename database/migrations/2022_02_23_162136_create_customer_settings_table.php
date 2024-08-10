<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('key')->index();
            $table->string('category')->default('General');
            $table->string('context')->default('Context is a group of settings');
            $table->enum('type', ['text', 'textarea', 'boolean', 'image', 'file'])->default('text')->comment('input type');
            $table->longText('value')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('check, if active or inactive');
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
        Schema::dropIfExists('customer_settings');
    }
}
