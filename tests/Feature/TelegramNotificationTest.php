<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup env variables for Telegram bot
        config(['services.telegram-bot-token' => 'test-token']);
        putenv('TELEGRAM_BOT_TOKEN=test-token');
        putenv('TELEGRAM_ADMIN_CHAT_ID=123456789');
    }

    public function test_telegram_view_notification_prepends_agency_prefix_for_agency_listings()
    {
        Http::fake();

        $agencyUser = new User();
        $agencyUser->name = 'Agency Host';
        $agencyUser->email = 'agency@flatly.by';
        $agencyUser->password = bcrypt('password');
        $agencyUser->phone = '+375293197117';
        $agencyUser->save();

        $regularUser = new User();
        $regularUser->name = 'Regular Host';
        $regularUser->email = 'regular@flatly.by';
        $regularUser->password = bcrypt('password');
        $regularUser->phone = '+375 29 111-22-33';
        $regularUser->save();

        $agencyListing = Listing::create([
            'user_id' => $agencyUser->id,
            'title' => 'Agency Apartment',
            'description' => 'Description',
            'price' => 100,
            'city' => 'Минск',
            'address' => 'ул. Ленина 1',
            'status' => 'active',
            'rooms' => 1,
            'sleeping_places' => 2,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
            'featured' => false
        ]);

        $regularListing = Listing::create([
            'user_id' => $regularUser->id,
            'title' => 'Regular Apartment',
            'description' => 'Description',
            'price' => 80,
            'city' => 'Минск',
            'address' => 'ул. Маркса 2',
            'status' => 'active',
            'rooms' => 1,
            'sleeping_places' => 2,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
            'featured' => false
        ]);

        // 1. Test view notification for agency listing
        $this->get(route('listings.show', ['id' => $agencyListing->id, 'slug' => $agencyListing->slug]));

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.telegram.org/bot')
                && str_contains($request['text'], '<b>АГЕНТСТВО</b>')
                && str_contains($request['text'], 'Agency Apartment');
        });

        // Clear HTTP history
        Http::fake();

        // 2. Test view notification for regular listing
        $this->get(route('listings.show', ['id' => $regularListing->id, 'slug' => $regularListing->slug]));

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.telegram.org/bot')
                && !str_contains($request['text'], '<b>АГЕНТСТВО</b>')
                && str_contains($request['text'], 'Regular Apartment');
        });
    }

    public function test_telegram_contact_click_notification_prepends_agency_prefix_for_agency_listings()
    {
        Http::fake();

        $agencyUser = new User();
        $agencyUser->name = 'Agency Host';
        $agencyUser->email = 'agency@flatly.by';
        $agencyUser->password = bcrypt('password');
        $agencyUser->phone = '+375447076448';
        $agencyUser->save();

        $regularUser = new User();
        $regularUser->name = 'Regular Host';
        $regularUser->email = 'regular@flatly.by';
        $regularUser->password = bcrypt('password');
        $regularUser->phone = '+375 29 111-22-33';
        $regularUser->save();

        $agencyListing = Listing::create([
            'user_id' => $agencyUser->id,
            'title' => 'Agency Apartment',
            'description' => 'Description',
            'price' => 100,
            'city' => 'Минск',
            'address' => 'ул. Ленина 1',
            'status' => 'active',
            'rooms' => 1,
            'sleeping_places' => 2,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
            'featured' => false
        ]);

        $regularListing = Listing::create([
            'user_id' => $regularUser->id,
            'title' => 'Regular Apartment',
            'description' => 'Description',
            'price' => 80,
            'city' => 'Минск',
            'address' => 'ул. Маркса 2',
            'status' => 'active',
            'rooms' => 1,
            'sleeping_places' => 2,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
            'featured' => false
        ]);

        // Click contact on agency listing
        $this->post(route('listings.track', ['id' => $agencyListing->id]), [
            'type' => 'phone'
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.telegram.org/bot')
                && str_contains($request['text'], '<b>АГЕНТСТВО</b>')
                && str_contains($request['text'], 'Agency Apartment')
                && str_contains($request['text'], 'Клик по контактам!');
        });

        // Clear HTTP history
        Http::fake();

        // Click contact on regular listing
        $this->post(route('listings.track', ['id' => $regularListing->id]), [
            'type' => 'phone'
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.telegram.org/bot')
                && !str_contains($request['text'], '<b>АГЕНТСТВО</b>')
                && str_contains($request['text'], 'Regular Apartment');
        });
    }
}
