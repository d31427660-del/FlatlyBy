<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. По умолчанию устанавливаем всем подпискам и платежам тариф для квартир (ID 1)
        DB::table('user_tariffs')->update(['tariff_id' => 1]);
        DB::table('payments')->update(['tariff_id' => 1]);

        // 2. Находим все объявления, которые являются коттеджами или агроусадьбами
        $cottageIds = DB::table('listings')
            ->whereIn('type', ['cottage', 'agrotourism'])
            ->pluck('id');

        // 3. Обновляем подписки и платежи для коттеджей на тариф для загородной недвижимости (ID 2)
        if ($cottageIds->isNotEmpty()) {
            DB::table('user_tariffs')
                ->whereIn('listing_id', $cottageIds)
                ->update(['tariff_id' => 2]);

            DB::table('payments')
                ->whereIn('listing_id', $cottageIds)
                ->update(['tariff_id' => 2]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Обратная операция не требуется, так как старая сетка тарифов упразднена
    }
};
