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
            if (!Schema::hasColumn('listings', 'photos')) {
                $table->json('photos')->nullable()->after('image');
            }
            if (!Schema::hasColumn('listings', 'beds_list')) {
                $table->json('beds_list')->nullable()->after('sleeping_places');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (Schema::hasColumn('listings', 'photos')) {
                $table->dropColumn('photos');
            }
            if (Schema::hasColumn('listings', 'beds_list')) {
                $table->dropColumn('beds_list');
            }
        });
    }
};
