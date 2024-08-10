<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('check, if active or inactive');
            $table->string('type')->default('email')->comment('Notification template type');
            $table->string('category')->nullable()->comment('Template category');
            $table->string('name')->nullable()->comment('Notification template name');
            $table->foreignId('main_template_id')->nullable()->comment('Template parent id');
            $table->boolean('is_dynamic_value')->default(0)->comment('Dynamic values condition');
            
            // for push notification
            $table->text('image')->nullable()->comment('Template image for push notication');
            $table->string('ad_channel_name')->nullable()->comment('Additional options channel name');
            $table->json('ad_custom_data')->nullable()->comment('Additional options custom data list');
            $table->enum('ad_sound', ['enabled', 'disabled'])->default('enabled')->comment('check, if enabled or disabled');
            $table->enum('ad_apple_badge', ['enabled', 'disabled'])->default('disabled')->comment('check, if enabled or disabled');
            $table->integer('ad_apple_badge_count')->nullable()->comment('Additional apple badge count');
            $table->integer('ad_expire_value')->nullable()->comment('Additional options expire value');
            $table->string('ad_expire_unit')->nullable()->comment('Additional options expire unit');
            
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
        Schema::dropIfExists('templates');
    }
}
