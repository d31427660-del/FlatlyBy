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
        // 1. Ensure promotion tariff (ID 3) exists in the database
        if (Schema::hasTable('tariffs')) {
            $exists = DB::table('tariffs')->where('id', 3)->exists();
            if (!$exists) {
                DB::table('tariffs')->insert([
                    'id' => 3,
                    'name' => 'Тариф Продвижение',
                    'property_type' => 'featured',
                    'price' => 5.00,
                    'duration_days' => 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 2. Clear promotion status for all listings as requested
        if (Schema::hasTable('listings')) {
            DB::table('listings')->update(['featured' => false]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback required for data clearance
    }
};
