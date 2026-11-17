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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'telegram')) {
                $table->string('telegram')->nullable();
            }
            if (!Schema::hasColumn('users', 'viber')) {
                $table->string('viber')->nullable();
            }
            if (!Schema::hasColumn('users', 'unp')) {
                $table->string('unp')->nullable();
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->longText('avatar')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'telegram', 'viber', 'unp', 'avatar']);
        });
    }
};
