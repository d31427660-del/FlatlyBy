<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('telegram_users')) {
            Schema::create('telegram_users', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('chat_id')->unique();
                $table->string('username')->nullable();
                $table->string('first_name')->nullable();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->boolean('notifications_enabled')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_users');
    }
};
