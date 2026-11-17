<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Убеждаемся, что тариф (ID 4) существует в базе
        if (Schema::hasTable('tariffs')) {
            $exists = DB::table('tariffs')->where('id', 4)->exists();
            if (!$exists) {
                DB::table('tariffs')->insert([
                    'id' => 4,
                    'name' => 'Тариф Главная страница',
                    'property_type' => 'homepage',
                    'price' => 50.00,
                    'duration_days' => 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 2. Добавляем промокод FREE100HOME на 100% скидку
        if (Schema::hasTable('promocodes')) {
            $existsPromo = DB::table('promocodes')->where('code', 'FREE100HOME')->exists();
            if (!$existsPromo) {
                DB::table('promocodes')->insert([
                    'code' => 'FREE100HOME',
                    'type' => 'free',
                    'value' => 100.00,
                    'max_uses' => null,
                    'max_uses_per_user' => 1,
                    'starts_at' => null,
                    'expires_at' => null,
                    'is_active' => true,
                    'description' => 'Бесплатное размещение на Главной странице',
                    'bonus_days' => 30,
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
            DB::table('promocodes')->where('code', 'FREE100HOME')->delete();
        }
        if (Schema::hasTable('tariffs')) {
            DB::table('tariffs')->where('id', 4)->delete();
        }
    }
};
