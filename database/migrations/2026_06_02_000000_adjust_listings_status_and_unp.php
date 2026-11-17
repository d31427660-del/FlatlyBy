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
        Schema::table('listings', function (Blueprint $table) {
            // Adjust status to string (varchar) to allow 'pending', 'rejected', 'hidden', etc.
            $table->string('status', 50)->default('pending')->change();
            
            // Adjust unp to nullable
            $table->string('unp', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed for production adjustments
    }
};
