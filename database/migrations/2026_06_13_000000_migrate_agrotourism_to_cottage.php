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
        // Переводим все агроусадьбы в категорию коттеджей
        DB::table('listings')->where('type', 'agrotourism')->update(['type' => 'cottage']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Обратная операция не требуется
    }
};
