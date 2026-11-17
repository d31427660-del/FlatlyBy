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
            if (!Schema::hasColumn('listings', 'banquet_hall_capacity')) {
                $table->integer('banquet_hall_capacity')->nullable()->after('sleeping_places');
            }
            if (!Schema::hasColumn('listings', 'video_surveillance')) {
                $table->json('video_surveillance')->nullable()->after('communications');
            }
            if (!Schema::hasColumn('listings', 'neighborhood')) {
                $table->json('neighborhood')->nullable()->after('territory_features');
            }
            if (!Schema::hasColumn('listings', 'object_purpose')) {
                $table->json('object_purpose')->nullable()->after('neighborhood');
            }
            if (!Schema::hasColumn('listings', 'parking_type')) {
                $table->string('parking_type')->nullable()->after('layout');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (Schema::hasColumn('listings', 'banquet_hall_capacity')) {
                $table->dropColumn('banquet_hall_capacity');
            }
            if (Schema::hasColumn('listings', 'video_surveillance')) {
                $table->dropColumn('video_surveillance');
            }
            if (Schema::hasColumn('listings', 'neighborhood')) {
                $table->dropColumn('neighborhood');
            }
            if (Schema::hasColumn('listings', 'object_purpose')) {
                $table->dropColumn('object_purpose');
            }
            if (Schema::hasColumn('listings', 'parking_type')) {
                $table->dropColumn('parking_type');
            }
        });
    }
};
