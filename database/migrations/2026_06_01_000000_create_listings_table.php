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
        if (!Schema::hasTable('listings')) {
            Schema::create('listings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('type')->default('apartment'); // 'apartment', 'cottage', etc.
                $table->string('title');
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2);
                $table->decimal('weekend_price', 10, 2)->nullable();
                $table->boolean('weekend_price_negotiable')->default(false);
                $table->decimal('holiday_price', 10, 2)->nullable();
                $table->boolean('holiday_price_negotiable')->default(false);
                $table->string('city')->default('Минск');
                $table->string('district')->nullable();
                $table->string('address')->nullable();
                $table->integer('rooms')->nullable();
                $table->decimal('area', 8, 2)->nullable();
                $table->decimal('total_area', 8, 2)->nullable();
                $table->integer('sleeping_places')->default(2);
                $table->boolean('children_allowed')->default(true);
                $table->boolean('animals_allowed')->default(false);
                $table->boolean('events_allowed')->default(false);
                $table->time('check_in_time')->default('14:00:00');
                $table->time('check_out_time')->default('12:00:00');
                $table->integer('minimum_rent_days')->default(1);
                $table->decimal('deposit', 10, 2)->nullable();
                $table->string('apartment_type')->nullable();
                $table->integer('total_floors')->nullable();
                $table->string('layout')->nullable();
                $table->string('renovation')->nullable();
                $table->string('building_material')->nullable();
                $table->boolean('has_balcony')->default(false);
                $table->string('balcony_type')->nullable();
                $table->boolean('balcony_glazed')->default(false);
                $table->string('cottage_type')->nullable();
                $table->decimal('plot_area', 8, 2)->nullable();
                $table->integer('floors_in_house')->nullable();
                $table->string('cottage_material')->nullable();
                $table->integer('bathrooms_count')->nullable();
                $table->text('amenities')->nullable(); // JSON configuration array
                $table->text('communications')->nullable(); // JSON configuration array
                $table->text('territory_features')->nullable(); // JSON configuration array
                $table->string('unp', 50)->nullable();
                $table->string('telegram')->nullable();
                $table->string('whatsapp')->nullable();
                $table->string('viber')->nullable();
                $table->string('nearest_metro')->nullable();
                $table->integer('floor')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->string('status')->default('pending'); // active, hidden, pending, rejected
                $table->boolean('featured')->default(false);
                $table->integer('views_count')->default(0);
                $table->integer('contacts_count')->default(0);
                $table->integer('impressions_count')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
