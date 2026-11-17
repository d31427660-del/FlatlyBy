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
            $table->boolean('has_detailed_stats')->default(false)->after('unp');
        });

        // Insert STATS100 promocode if it does not exist
        if (Schema::hasTable('promocodes')) {
            $exists = \Illuminate\Support\Facades\DB::table('promocodes')->where('code', 'STATS100')->exists();
            if (!$exists) {
                \Illuminate\Support\Facades\DB::table('promocodes')->insert([
                    'code' => 'STATS100',
                    'type' => 'free_stats',
                    'value' => 100.00,
                    'max_uses_per_user' => 1,
                    'is_active' => true,
                    'description' => 'Бесплатная детальная статистика',
                    'bonus_days' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('promocodes')) {
            \Illuminate\Support\Facades\DB::table('promocodes')->where('code', 'STATS100')->delete();
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('has_detailed_stats');
        });
    }
};
