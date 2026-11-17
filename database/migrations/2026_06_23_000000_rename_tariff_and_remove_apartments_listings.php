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
        // Update the tariff name
        DB::table('tariffs')
            ->where('name', 'Тариф Квартира / Апартаменты')
            ->update(['name' => 'Тариф Квартира']);

        // Update listing types from 'apartments' to 'apartment'
        DB::table('listings')
            ->where('type', 'apartments')
            ->update(['type' => 'apartment']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the tariff name
        DB::table('tariffs')
            ->where('name', 'Тариф Квартира')
            ->update(['name' => 'Тариф Квартира / Апартаменты']);
    }
};
