<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listing_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $table->string('interaction_type'); // 'view', 'phone', 'telegram', 'viber', 'whatsapp'
            $table->string('device_type');      // 'desktop', 'mobile', 'tablet'
            $table->timestamps();
        });

        // Seed historical data for existing listings
        try {
            $listings = DB::table('listings')->get();
            $records = [];
            
            foreach ($listings as $listing) {
                $viewsCount = (int)$listing->views_count;
                $contactsCount = (int)$listing->contacts_count;
                
                // Device types distribution logic: 78% mobile, 18% desktop, 4% tablet
                $getDevice = function() {
                    $r = rand(1, 100);
                    if ($r <= 78) return 'mobile';
                    if ($r <= 96) return 'desktop';
                    return 'tablet';
                };

                // Helper to get random timestamp in the last 60 days
                $getRandomTimestamp = function() {
                    return Carbon::now()->subSeconds(rand(0, 60 * 24 * 60 * 60));
                };

                // Seed views
                for ($i = 0; $i < $viewsCount; $i++) {
                    $timestamp = $getRandomTimestamp();
                    $records[] = [
                        'listing_id' => $listing->id,
                        'interaction_type' => 'view',
                        'device_type' => $getDevice(),
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];

                    if (count($records) >= 500) {
                        DB::table('listing_interactions')->insert($records);
                        $records = [];
                    }
                }

                // Seed contacts: 40% phone, 30% telegram, 15% whatsapp, 15% viber
                for ($i = 0; $i < $contactsCount; $i++) {
                    $timestamp = $getRandomTimestamp();
                    $r = rand(1, 100);
                    if ($r <= 40) {
                        $type = 'phone';
                    } elseif ($r <= 70) {
                        $type = 'telegram';
                    } elseif ($r <= 85) {
                        $type = 'whatsapp';
                    } else {
                        $type = 'viber';
                    }

                    $records[] = [
                        'listing_id' => $listing->id,
                        'interaction_type' => $type,
                        'device_type' => $getDevice(),
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];

                    if (count($records) >= 500) {
                        DB::table('listing_interactions')->insert($records);
                        $records = [];
                    }
                }
            }

            if (count($records) > 0) {
                DB::table('listing_interactions')->insert($records);
            }
        } catch (\Exception $e) {
            // Log error, but don't fail migration if seeding fails
            Log::error('Seeding listing interactions failed: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_interactions');
    }
};
