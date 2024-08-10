<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('check, if active or inactive');
            $table->string('type')->default('email')->comment('Notification type');
            $table->string('name')->nullable()->comment('Notification name');
            $table->enum('platform', ['web', 'android', 'ios'])->nullable()->comment('Notification platform');
            $table->foreignId('template_id')->nullable()->comment('Notification template id');
            $table->enum('process_status', ['draft', 'schedule', 'completed'])->default('draft')->comment('Notification processing status');
            
            // for push notification
            $table->string('scheduling_type')->nullable()->comment('Notification scheduling');
            $table->string('scheduling_date')->nullable()->comment('Notification scheduling');
            $table->json('target')->nullable()->comment('Notification target/query data');
            $table->boolean('is_android')->default(0)->comment('Android for push notification');
            $table->boolean('is_ios')->default(0)->comment('IOS for push notification');
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
        Schema::dropIfExists('notifications');
    }
}
