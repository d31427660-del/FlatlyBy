<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class MigrateLegacyDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flatly:migrate-legacy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate legacy database from flatly1.sql into the new design schema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting legacy database migration...');
        
        $sqlPath = base_path('flatly1.sql');
        if (!File::exists($sqlPath)) {
            $this->error("File flatly1.sql not found in the project root: {$sqlPath}");
            return Command::FAILURE;
        }

        $this->info('Parsing SQL dump and renaming tables...');
        $sqlContent = File::get($sqlPath);
        
        // List of legacy tables to rename to legacy_* to avoid collision with the new schema
        $tables = [
            'deleted_listings_blacklist',
            'failed_jobs',
            'favorites',
            'listings',
            'listing_clicks',
            'listing_contact_clicks',
            'listing_images',
            'listing_price_histories',
            'listing_price_history',
            'listing_time_spent',
            'listing_time_spents',
            'listing_views',
            'market_price_snapshots',
            'migrations',
            'password_reset_tokens',
            'payments',
            'personal_access_tokens',
            'promocodes',
            'promocode_usage',
            'reviews',
            'tariffs',
            'users',
            'user_tariffs',
        ];

        // Drop legacy tables if they exist to avoid collision and allow clean reruns
        $this->info('Dropping any existing legacy tables to start clean...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $table) {
            Schema::dropIfExists("legacy_{$table}");
        }
        $this->info('Existing legacy tables dropped.');

        // Replace `table_name` with `legacy_table_name`
        foreach ($tables as $table) {
            $sqlContent = str_replace("`{$table}`", "`legacy_{$table}`", $sqlContent);
        }

        // Prepend legacy_ to constraint names to avoid global InnoDB duplicate constraint conflicts
        $sqlContent = str_replace("ADD CONSTRAINT `", "ADD CONSTRAINT `legacy_", $sqlContent);

        // We temporarily disable foreign key checks to perform safe truncation and operations
        $this->info('Importing legacy tables into database...');
        
        try {
            DB::unprepared($sqlContent);
            $this->info('Legacy tables imported successfully as legacy_*');
        } catch (\Exception $e) {
            $this->error('Failed to import legacy SQL: ' . $e->getMessage());
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return Command::FAILURE;
        }

        $this->info('Truncating current new tables to start clean migration...');
        $newTables = [
            'promocode_usage',
            'payments',
            'user_tariffs',
            'listings',
            'users',
            'promocodes',
        ];
        
        foreach ($newTables as $tbl) {
            if (Schema::hasTable($tbl)) {
                DB::table($tbl)->truncate();
            }
        }
        $this->info('New tables truncated.');

        // 1. Migrate Users
        $this->info('Migrating Users...');
        $usersCount = 0;
        if (Schema::hasTable('legacy_users')) {
            $oldUsers = DB::table('legacy_users')->get();
            foreach ($oldUsers as $old) {
                $old = (array) $old;
                
                DB::table('users')->insert([
                    'id' => $old['id'],
                    'name' => $old['name'],
                    'email' => $old['email'],
                    'email_verified_at' => $old['email_verified_at'] ?? null,
                    'password' => $old['password'],
                    'phone' => $old['phone'] ?? null,
                    'telegram' => $old['telegram'] ?? null,
                    'whatsapp' => $old['whatsapp'] ?? null,
                    'viber' => $old['viber'] ?? null,
                    'unp' => $old['unp'] ?? null,
                    'avatar' => $old['avatar'] ?? null,
                    'has_detailed_stats' => $old['has_detailed_stats'] ?? 0,
                    'remember_token' => $old['remember_token'] ?? null,
                    'created_at' => $old['created_at'] ?? null,
                    'updated_at' => $old['updated_at'] ?? null,
                ]);
                $usersCount++;
            }
            $this->info("Migrated {$usersCount} users.");
        } else {
            $this->warn('legacy_users table not found, skipping user migration.');
        }

        // 2. Migrate Listings (and compile photos/cover image)
        $this->info('Migrating Listings and photos...');
        $listingsCount = 0;
        if (Schema::hasTable('legacy_listings')) {
            $oldListings = DB::table('legacy_listings')->get();
            
            foreach ($oldListings as $old) {
                $old = (array) $old;
                // Fetch images for this listing
                $images = [];
                if (Schema::hasTable('legacy_listing_images')) {
                    $images = DB::table('legacy_listing_images')
                        ->where('listing_id', $old['id'])
                        ->orderBy('order')
                        ->pluck('image_path')
                        ->map(function ($path) {
                            if (!str_starts_with($path, '/') && !str_starts_with($path, 'http')) {
                                return '/storage/' . $path;
                            }
                            return $path;
                        })
                        ->toArray();
                }
                
                $coverImage = count($images) > 0 ? $images[0] : null;

                // Obtain phone number from user if listing doesn't specify one
                $phone = $old['phone'] ?? null;
                if (empty($phone) && !empty($old['user_id'])) {
                    $phone = DB::table('legacy_users')->where('id', $old['user_id'])->value('phone');
                }

                // Map status: enum('active','inactive') -> active, hidden
                $status = 'hidden';
                if (isset($old['status'])) {
                    if ($old['status'] === 'active') {
                        $status = 'active';
                    } elseif ($old['status'] === 'inactive') {
                        $status = 'hidden';
                    }
                }

                DB::table('listings')->insert([
                    'id' => $old['id'],
                    'user_id' => $old['user_id'],
                    'type' => $old['type'] ?? 'apartment',
                    'title' => $old['title'],
                    'description' => $old['description'] ?? null,
                    'price' => $old['price'],
                    'weekend_price' => $old['weekend_price'] ?? null,
                    'weekend_price_negotiable' => $old['weekend_price_negotiable'] ?? 0,
                    'holiday_price' => $old['holiday_price'] ?? null,
                    'holiday_price_negotiable' => $old['holiday_price_negotiable'] ?? 0,
                    'city' => $old['city'] ?? 'Минск',
                    'district' => $old['district'] ?? null,
                    'address' => $old['address'] ?? null,
                    'rooms' => $old['rooms'] ?? null,
                    'area' => $old['area'] ?? null,
                    'total_area' => $old['total_area'] ?? null,
                    'sleeping_places' => $old['sleeping_places'] ?? 2,
                    'children_allowed' => $old['children_allowed'] ?? 1,
                    'animals_allowed' => $old['animals_allowed'] ?? 0,
                    'events_allowed' => $old['events_allowed'] ?? 0,
                    'check_in_time' => $old['check_in_time'] ?? '14:00:00',
                    'check_out_time' => $old['check_out_time'] ?? '12:00:00',
                    'minimum_rent_days' => $old['minimum_rent_days'] ?? 1,
                    'deposit' => $old['deposit'] ?? null,
                    'apartment_type' => $old['apartment_type'] ?? null,
                    'total_floors' => $old['total_floors'] ?? null,
                    'layout' => $old['layout'] ?? null,
                    'renovation' => $old['renovation'] ?? null,
                    'building_material' => $old['building_material'] ?? null,
                    'has_balcony' => $old['has_balcony'] ?? 0,
                    'balcony_type' => $old['balcony_type'] ?? null,
                    'balcony_glazed' => $old['balcony_glazed'] ?? 0,
                    'cottage_type' => $old['cottage_type'] ?? null,
                    'plot_area' => $old['plot_area'] ?? null,
                    'floors_in_house' => $old['floors_in_house'] ?? null,
                    'cottage_material' => $old['cottage_material'] ?? null,
                    'bathrooms_count' => $old['bathrooms_count'] ?? null,
                    'amenities' => $old['amenities'] ?? null,
                    'communications' => $old['communications'] ?? null,
                    'territory_features' => $old['territory_features'] ?? null,
                    'unp' => $old['unp'] ?? null,
                    'phone' => $phone,
                    'telegram' => $old['telegram'] ?? null,
                    'whatsapp' => $old['whatsapp'] ?? null,
                    'viber' => $old['viber'] ?? null,
                    'nearest_metro' => $old['nearest_metro'] ?? null,
                    'floor' => $old['floor'] ?? null,
                    'latitude' => $old['latitude'] ?? null,
                    'longitude' => $old['longitude'] ?? null,
                    'status' => $status,
                    'featured' => $old['featured'] ?? 0,
                    'views_count' => $old['views_count'] ?? 0,
                    'contacts_count' => $old['contacts_count'] ?? 0,
                    'impressions_count' => $old['impressions_count'] ?? 0,
                    'image' => $coverImage,
                    'photos' => json_encode($images),
                    'created_at' => $old['created_at'] ?? null,
                    'updated_at' => $old['updated_at'] ?? null,
                    'deleted_at' => $old['deleted_at'] ?? null,
                ]);
                $listingsCount++;
            }
            $this->info("Migrated {$listingsCount} listings.");
        } else {
            $this->warn('legacy_listings table not found, skipping listing migration.');
        }

        // 3. Migrate Promocodes
        $this->info('Migrating Promocodes...');
        $promocodesCount = 0;
        if (Schema::hasTable('legacy_promocodes')) {
            $oldPromocodes = DB::table('legacy_promocodes')->get();
            foreach ($oldPromocodes as $old) {
                $old = (array) $old;
                DB::table('promocodes')->updateOrInsert(
                    ['id' => $old['id']],
                    [
                        'code' => $old['code'],
                        'type' => $old['type'] ?? 'percentage',
                        'value' => $old['value'],
                        'min_amount' => $old['min_amount'] ?? null,
                        'max_uses' => $old['max_uses'] ?? null,
                        'max_uses_per_user' => $old['max_uses_per_user'] ?? 1,
                        'starts_at' => $old['starts_at'] ?? null,
                        'expires_at' => $old['expires_at'] ?? null,
                        'is_active' => $old['is_active'] ?? 1,
                        'description' => $old['description'] ?? null,
                        'bonus_days' => $old['bonus_days'] ?? null,
                        'created_at' => $old['created_at'] ?? null,
                        'updated_at' => $old['updated_at'] ?? null,
                    ]
                );
                $promocodesCount++;
            }
            $this->info("Migrated {$promocodesCount} promocodes.");
        } else {
            $this->warn('legacy_promocodes table not found, skipping promocode migration.');
        }

        // 4. Migrate User Tariffs (Subscriptions)
        $this->info('Migrating User Tariffs...');
        $userTariffsCount = 0;
        if (Schema::hasTable('legacy_user_tariffs')) {
            $oldUserTariffs = DB::table('legacy_user_tariffs')->get();
            foreach ($oldUserTariffs as $old) {
                $old = (array) $old;
                if (!DB::table('listings')->where('id', $old['listing_id'])->exists() || 
                    !DB::table('users')->where('id', $old['user_id'])->exists()) {
                    continue;
                }

                $listingType = DB::table('listings')->where('id', $old['listing_id'])->value('type');
                $newTariffId = ($listingType === 'cottage') ? 2 : 1;

                DB::table('user_tariffs')->insert([
                    'id' => $old['id'],
                    'user_id' => $old['user_id'],
                    'listing_id' => $old['listing_id'],
                    'tariff_id' => $newTariffId,
                    'started_at' => $old['started_at'],
                    'expires_at' => $old['expires_at'],
                    'status' => $old['status'] ?? 'active',
                    'created_at' => $old['created_at'] ?? null,
                    'updated_at' => $old['updated_at'] ?? null,
                ]);
                $userTariffsCount++;
            }
            $this->info("Migrated {$userTariffsCount} user tariffs.");
        } else {
            $this->warn('legacy_user_tariffs not found, skipping subscription migration.');
        }

        // 5. Migrate Payments
        $this->info('Migrating Payments...');
        $paymentsCount = 0;
        if (Schema::hasTable('legacy_payments')) {
            $oldPayments = DB::table('legacy_payments')->get();
            foreach ($oldPayments as $old) {
                $old = (array) $old;
                if (!DB::table('users')->where('id', $old['user_id'])->exists()) {
                    continue;
                }

                // Extract listing_id from metadata JSON
                $listingId = null;
                $metadataArray = [];
                if (!empty($old['metadata'])) {
                    $meta = json_decode($old['metadata'], true);
                    if (is_array($meta)) {
                        $metadataArray = $meta;
                        $listingId = $meta['listing_id'] ?? ($meta['listing_data']['id'] ?? null);
                        if (empty($listingId) && isset($meta['webhook_data']['transaction']['order_id'])) {
                            $listingId = $meta['webhook_data']['transaction']['order_id'];
                        }
                    }
                }

                if ($listingId && !DB::table('listings')->where('id', $listingId)->exists()) {
                    $listingId = null;
                }

                $newTariffId = 1;
                if ($listingId) {
                    $listingType = DB::table('listings')->where('id', $listingId)->value('type');
                    $newTariffId = ($listingType === 'cottage') ? 2 : 1;
                } else {
                    if (($old['tariff_id'] ?? null) == 2 || ($old['tariff_id'] ?? null) == 4) {
                        $newTariffId = 2;
                    }
                }

                DB::table('payments')->insert([
                    'id' => $old['id'],
                    'user_id' => $old['user_id'],
                    'listing_id' => $listingId,
                    'tariff_id' => $newTariffId,
                    'amount' => $old['amount'],
                    'currency' => $old['currency'] ?? 'BYN',
                    'status' => $old['status'] ?? 'pending',
                    'payment_provider_id' => $old['payment_provider_id'] ?? null,
                    'checkout_token' => $metadataArray['checkout_token'] ?? null,
                    'duration_months' => 1,
                    'promocode_id' => $old['promocode_id'] ?? null,
                    'discount_amount' => $old['discount_amount'] ?? 0.00,
                    'metadata' => $old['metadata'] ?? null,
                    'created_at' => $old['created_at'] ?? null,
                    'updated_at' => $old['updated_at'] ?? null,
                ]);
                $paymentsCount++;
            }
            $this->info("Migrated {$paymentsCount} payments.");
        } else {
            $this->warn('legacy_payments table not found, skipping payment migration.');
        }

        // 6. Migrate Promocode Usage Logs
        $this->info('Migrating Promocode Usage...');
        $promoUsageCount = 0;
        if (Schema::hasTable('legacy_promocode_usage')) {
            $oldUsage = DB::table('legacy_promocode_usage')->get();
            foreach ($oldUsage as $old) {
                $old = (array) $old;
                if (!DB::table('promocodes')->where('id', $old['promocode_id'])->exists() || 
                    !DB::table('users')->where('id', $old['user_id'])->exists()) {
                    continue;
                }
                
                if (($old['payment_id'] ?? null) && !DB::table('payments')->where('id', $old['payment_id'])->exists()) {
                    continue;
                }

                DB::table('promocode_usage')->insert([
                    'id' => $old['id'],
                    'promocode_id' => $old['promocode_id'],
                    'user_id' => $old['user_id'],
                    'payment_id' => $old['payment_id'] ?? null,
                    'discount_amount' => $old['discount_amount'] ?? 0.00,
                    'used_at' => $old['used_at'] ?? ($old['created_at'] ?? null),
                    'created_at' => $old['created_at'] ?? null,
                    'updated_at' => $old['updated_at'] ?? null,
                ]);
                $promoUsageCount++;
            }
            $this->info("Migrated {$promoUsageCount} promocode usage logs.");
        } else {
            $this->warn('legacy_promocode_usage not found, skipping usage migration.');
        }

        // Clean up temporary legacy_* tables
        $this->info('Cleaning up legacy tables...');
        foreach ($tables as $table) {
            Schema::dropIfExists("legacy_{$table}");
        }
        $this->info('Legacy tables deleted.');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->info('Migration complete!');
        return Command::SUCCESS;
    }
}
