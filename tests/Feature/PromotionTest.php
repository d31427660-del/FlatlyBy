<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use App\Models\Payment;
use App\Models\Tariff;
use App\Models\UserTariff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic tariff
        Tariff::create([
            'id' => 1,
            'name' => 'Стандарт',
            'price' => 10.00,
            'duration_months' => 1,
            'property_type' => 'apartment',
            'description' => 'Стандартный тариф'
        ]);

        Tariff::create([
            'id' => 3,
            'name' => 'Тариф Продвижение',
            'property_type' => 'featured',
            'price' => 5.00,
            'duration_days' => 30,
        ]);
    }

    public function test_checkout_featured_requires_login()
    {
        $user = new User();
        $user->name = 'Natalia';
        $user->email = 'natalia@flatly.by';
        $user->password = bcrypt('password');
        $user->phone = '+375 29 688-38-98';
        $user->save();

        $listing = Listing::create([
            'user_id' => $user->id,
            'title' => 'Уютная квартира',
            'description' => 'Описание квартиры',
            'price' => 100,
            'city' => 'Минск',
            'address' => 'ул. Ленина 1',
            'status' => 'active',
            'rooms' => 2,
            'sleeping_places' => 4,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
            'featured' => false
        ]);

        $response = $this->get("/payments/checkout-featured/{$listing->id}");
        $response->assertRedirect(route('login'));
    }

    public function test_checkout_featured_creates_pending_payment()
    {
        $user = new User();
        $user->name = 'Natalia';
        $user->email = 'natalia@flatly.by';
        $user->password = bcrypt('password');
        $user->phone = '+375 29 688-38-98';
        $user->save();

        $listing = Listing::create([
            'user_id' => $user->id,
            'title' => 'Уютная квартира',
            'description' => 'Описание квартиры',
            'price' => 100,
            'city' => 'Минск',
            'address' => 'ул. Ленина 1',
            'status' => 'active',
            'rooms' => 2,
            'sleeping_places' => 4,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
            'featured' => false
        ]);

        $response = $this->withSession(['logged_in' => true, 'user_id' => $user->id])
            ->get("/payments/checkout-featured/{$listing->id}?months=3");

        // Should create a pending payment in database and redirect to bePaid (or simulator success if config is empty)
        $payment = Payment::where('listing_id', $listing->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(3, $payment->duration_months);
        $this->assertEquals(3, $payment->tariff_id);
        $this->assertEquals(40.50, $payment->amount); // 15.00 * 3 * 0.9 = 40.50
        $this->assertEquals('pending', $payment->status);
    }

    public function test_checkout_featured_with_free_promocode_PRODVIZH200()
    {
        $user = new User();
        $user->name = 'Natalia';
        $user->email = 'natalia@flatly.by';
        $user->password = bcrypt('password');
        $user->phone = '+375 29 688-38-98';
        $user->save();

        $listing = Listing::create([
            'user_id' => $user->id,
            'title' => 'Уютная квартира',
            'description' => 'Описание квартиры',
            'price' => 100,
            'city' => 'Минск',
            'address' => 'ул. Ленина 1',
            'status' => 'active',
            'rooms' => 2,
            'sleeping_places' => 4,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
            'featured' => false
        ]);

        $response = $this->withSession(['logged_in' => true, 'user_id' => $user->id])
            ->get("/payments/checkout-featured/{$listing->id}?months=6&promo=PRODVIZH200");

        // The checkout should automatically complete the payment since promo makes it free (0.00 BYN)
        $payment = Payment::where('listing_id', $listing->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(6, $payment->duration_months);
        $this->assertEquals(3, $payment->tariff_id);
        $this->assertEquals(0.00, $payment->amount);
        $this->assertEquals('completed', $payment->status); // Auto completed!

        $this->assertTrue(Listing::find($listing->id)->featured);
        
        $userTariff = UserTariff::where('listing_id', $listing->id)->where('tariff_id', 3)->first();
        $this->assertNotNull($userTariff);
        $this->assertEquals('active', $userTariff->status);
        $this->assertTrue($userTariff->expires_at->isFuture());
    }

    public function test_tariff_payment_ignores_active_promotions()
    {
        $user = new User();
        $user->name = 'Natalia';
        $user->email = 'natalia@flatly.by';
        $user->password = bcrypt('password');
        $user->phone = '+375 29 688-38-98';
        $user->save();

        $listing = Listing::create([
            'user_id' => $user->id,
            'title' => 'Уютная квартира',
            'description' => 'Описание квартиры',
            'price' => 100,
            'city' => 'Минск',
            'address' => 'ул. Ленина 1',
            'status' => 'active',
            'rooms' => 2,
            'sleeping_places' => 4,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
            'featured' => true // Has active promotion
        ]);

        // Pre-create active promotion tariff in DB (tariff_id = 3)
        $promoTariff = UserTariff::create([
            'user_id' => $user->id,
            'listing_id' => $listing->id,
            'tariff_id' => 3,
            'started_at' => now(),
            'expires_at' => now()->addDays(30),
            'status' => 'active'
        ]);

        // Buy a placement tariff (tariff_id = 1) for 6 months
        $payment = Payment::create([
            'user_id' => $user->id,
            'listing_id' => $listing->id,
            'tariff_id' => 1,
            'amount' => 48.00,
            'currency' => 'BYN',
            'status' => 'pending',
            'duration_months' => 6,
            'metadata' => ['type' => 'tariff_checkout']
        ]);

        // Access completePayment method via reflection
        $controller = app(\App\Http\Controllers\PaymentController::class);
        $reflector = new \ReflectionMethod($controller, 'completePayment');
        $reflector->setAccessible(true);
        $reflector->invoke($controller, $payment, 'TEST_TRANSACTION_123');

        // Verify that:
        // 1. A new UserTariff for tariff_id = 1 is created
        $placementTariff = UserTariff::where('listing_id', $listing->id)->where('tariff_id', 1)->first();
        $this->assertNotNull($placementTariff);
        $this->assertEquals('active', $placementTariff->status);
        $this->assertTrue($placementTariff->expires_at->isFuture());

        // 2. The active promotion tariff (tariff_id = 3) was NOT modified/extended
        $freshPromoTariff = UserTariff::where('listing_id', $listing->id)->where('tariff_id', 3)->first();
        $this->assertNotNull($freshPromoTariff);
        // Should still expire in 30 days, not extended by 6 months!
        $this->assertTrue($freshPromoTariff->expires_at->diffInDays(now()) <= 30);
    }
}
