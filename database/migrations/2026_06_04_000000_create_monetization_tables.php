<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create or update tariffs table
        if (!Schema::hasTable('tariffs')) {
            Schema::create('tariffs', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('property_type', 20)->default('apartment'); // 'apartment' or 'cottage'
                $table->decimal('price', 10, 2);
                $table->integer('duration_days')->default(30);
                $table->timestamps();
            });
        } else {
            Schema::table('tariffs', function (Blueprint $table) {
                if (!Schema::hasColumn('tariffs', 'property_type')) {
                    $table->string('property_type', 20)->default('apartment')->after('name');
                }
            });
        }

        // Seed tariffs if empty
        $hasTariffs = DB::table('tariffs')->count() > 0;
        if (!$hasTariffs) {
            DB::table('tariffs')->insert([
                [
                    'name' => 'Тариф Квартира',
                    'property_type' => 'apartment',
                    'price' => 10.00,
                    'duration_days' => 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Тариф Загородный',
                    'property_type' => 'cottage',
                    'price' => 30.00,
                    'duration_days' => 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        } else {
            // Ensure our default property-type tariffs exist
            $aptTariff = DB::table('tariffs')->where('property_type', 'apartment')->first();
            if (!$aptTariff) {
                DB::table('tariffs')->insert([
                    'name' => 'Тариф Квартира',
                    'property_type' => 'apartment',
                    'price' => 10.00,
                    'duration_days' => 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $cotTariff = DB::table('tariffs')->where('property_type', 'cottage')->first();
            if (!$cotTariff) {
                DB::table('tariffs')->insert([
                    'name' => 'Тариф Загородный',
                    'property_type' => 'cottage',
                    'price' => 30.00,
                    'duration_days' => 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 2. Create or update user_tariffs table
        if (!Schema::hasTable('user_tariffs')) {
            Schema::create('user_tariffs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('listing_id')->constrained('listings')->onDelete('cascade');
                $table->foreignId('tariff_id')->constrained('tariffs')->onDelete('cascade');
                $table->timestamp('started_at');
                $table->timestamp('expires_at');
                $table->string('status', 20)->default('active'); // active, expired
                $table->timestamps();
            });
        }

        // 3. Create or update payments table
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('listing_id')->nullable()->constrained('listings')->onDelete('cascade');
                $table->foreignId('tariff_id')->constrained('tariffs')->onDelete('cascade');
                $table->decimal('amount', 10, 2);
                $table->string('currency', 3)->default('BYN');
                $table->string('status', 20)->default('pending'); // pending, completed, failed
                $table->string('payment_provider_id')->nullable();
                $table->string('checkout_token')->nullable();
                $table->integer('duration_months')->default(1);
                $table->bigInteger('promocode_id')->unsigned()->nullable();
                $table->decimal('discount_amount', 10, 2)->default(0.00);
                $table->text('metadata')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'listing_id')) {
                    $table->foreignId('listing_id')->nullable()->constrained('listings')->onDelete('cascade')->after('user_id');
                }
                if (!Schema::hasColumn('payments', 'duration_months')) {
                    $table->integer('duration_months')->default(1)->after('checkout_token');
                }
                if (!Schema::hasColumn('payments', 'promocode_id')) {
                    $table->bigInteger('promocode_id')->unsigned()->nullable()->after('duration_months');
                }
                if (!Schema::hasColumn('payments', 'discount_amount')) {
                    $table->decimal('discount_amount', 10, 2)->default(0.00)->after('promocode_id');
                }
                if (!Schema::hasColumn('payments', 'checkout_token')) {
                    $table->string('checkout_token')->nullable()->after('payment_provider_id');
                }
            });
        }

        // 4. Create or update promocodes table
        if (!Schema::hasTable('promocodes')) {
            Schema::create('promocodes', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('type', 20)->default('percentage'); // percentage, fixed, free
                $table->decimal('value', 10, 2); // e.g. 10.00 for 10%
                $table->decimal('min_amount', 10, 2)->nullable();
                $table->integer('max_uses')->nullable();
                $table->integer('max_uses_per_user')->default(1);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('description')->nullable();
                $table->integer('bonus_days')->nullable();
                $table->timestamps();
            });
        }

        // Seed promocodes if empty
        $hasPromos = DB::table('promocodes')->count() > 0;
        if (!$hasPromos) {
            DB::table('promocodes')->insert([
                [
                    'code' => 'SKIDKA10',
                    'type' => 'percentage',
                    'value' => 10.00,
                    'min_amount' => null,
                    'max_uses' => null,
                    'max_uses_per_user' => 1,
                    'starts_at' => null,
                    'expires_at' => null,
                    'is_active' => true,
                    'description' => 'Скидка 10% на подписку',
                    'bonus_days' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'FREE30',
                    'type' => 'free',
                    'value' => 100.00,
                    'min_amount' => null,
                    'max_uses' => null,
                    'max_uses_per_user' => 1,
                    'starts_at' => null,
                    'expires_at' => null,
                    'is_active' => true,
                    'description' => 'Бесплатное размещение на 1 месяц',
                    'bonus_days' => 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'БЕСПЛАТНО',
                    'type' => 'free',
                    'value' => 100.00,
                    'min_amount' => null,
                    'max_uses' => null,
                    'max_uses_per_user' => 1,
                    'starts_at' => null,
                    'expires_at' => null,
                    'is_active' => true,
                    'description' => 'Бесплатное размещение на 1 месяц',
                    'bonus_days' => 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'STATS100',
                    'type' => 'free_stats',
                    'value' => 100.00,
                    'min_amount' => null,
                    'max_uses' => null,
                    'max_uses_per_user' => 1,
                    'starts_at' => null,
                    'expires_at' => null,
                    'is_active' => true,
                    'description' => 'Бесплатная детальная статистика',
                    'bonus_days' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        } else {
            // Ensure our default ones exist
            foreach ([
                'SKIDKA10' => ['percentage', 10.00],
                'FREE30' => ['free', 100.00],
                'БЕСПЛАТНО' => ['free', 100.00],
                'STATS100' => ['free_stats', 100.00]
            ] as $code => $info) {
                $exists = DB::table('promocodes')->where('code', $code)->exists();
                if (!$exists) {
                    $description = 'Скидка 10% на подписку';
                    $bonus_days = null;
                    if ($info[0] === 'free') {
                        $description = 'Бесплатное размещение на 1 месяц';
                        $bonus_days = 30;
                    } elseif ($info[0] === 'free_stats') {
                        $description = 'Бесплатная детальная статистика';
                        $bonus_days = null;
                    }
                    
                    DB::table('promocodes')->insert([
                        'code' => $code,
                        'type' => $info[0],
                        'value' => $info[1],
                        'max_uses_per_user' => 1,
                        'is_active' => true,
                        'description' => $description,
                        'bonus_days' => $bonus_days,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 5. Create or update promocode_usage table
        if (!Schema::hasTable('promocode_usage')) {
            Schema::create('promocode_usage', function (Blueprint $table) {
                $table->id();
                $table->foreignId('promocode_id')->constrained('promocodes')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('cascade');
                $table->decimal('discount_amount', 10, 2);
                $table->timestamp('used_at');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promocode_usage');
        Schema::dropIfExists('promocodes');
        Schema::dropIfExists('user_tariffs');
        Schema::dropIfExists('tariffs');
        // We shouldn't drop payments if it was already existing, but for completeness:
        // Schema::dropIfExists('payments');
    }
};
