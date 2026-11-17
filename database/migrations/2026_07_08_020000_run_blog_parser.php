<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // Run the parser to populate the table with fresh working posts & images
            Artisan::call('blog:parse');
        } catch (\Exception $e) {
            // Prevent migration failure if site is offline or DB connection issues during migration
            Log::warning('Failed to run blog:parse during migration: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed as table schema didn't change
    }
};
