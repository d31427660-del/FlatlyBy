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
        // Удаляем тестовые объявления с ID от 1 до 6
        DB::table('listings')->whereIn('id', [1, 2, 3, 4, 5, 6])->delete();

        // Удаляем пользователя Натальи
        DB::table('users')->where('email', 'natalia@flatly.by')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Восстановление тестовых данных не требуется
    }
};
