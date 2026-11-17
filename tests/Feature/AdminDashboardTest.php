<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use App\Models\Tariff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_loads_with_pending_payment_listing()
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
            'status' => 'pending_payment',
            'rooms' => 2,
            'sleeping_places' => 4,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
            'featured' => false
        ]);

        $response = $this->withSession([
            'admin_logged_in' => true,
            'error' => 'Some test error message'
        ])->get('/admin');

        $response->assertStatus(200);
    }

    public function test_admin_dashboard_shows_agency_listings()
    {
        $agencyUser = new User();
        $agencyUser->name = 'Агентство Flatly';
        $agencyUser->email = 'agency@flatly.by';
        $agencyUser->password = bcrypt('password');
        $agencyUser->phone = '+375293197117';
        $agencyUser->save();

        $regularUser = new User();
        $regularUser->name = 'Обычный Хозяин';
        $regularUser->email = 'host@flatly.by';
        $regularUser->password = bcrypt('password');
        $regularUser->phone = '+375 29 123-45-67';
        $regularUser->save();

        // Listing 1: Agency listing (matching one of the numbers)
        $agencyListing = Listing::create([
            'user_id' => $agencyUser->id,
            'title' => 'Агентская квартира 1',
            'description' => 'Описание',
            'price' => 120,
            'city' => 'Минск',
            'address' => 'ул. Ленина 5',
            'status' => 'active',
            'rooms' => 2,
            'sleeping_places' => 4,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
            'featured' => false
        ]);

        // Listing 2: Non-agency listing
        $regularListing = Listing::create([
            'user_id' => $regularUser->id,
            'title' => 'Обычная квартира',
            'description' => 'Описание',
            'price' => 90,
            'city' => 'Минск',
            'address' => 'ул. Маркса 10',
            'status' => 'active',
            'rooms' => 1,
            'sleeping_places' => 2,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
            'featured' => false
        ]);

        $response = $this->withSession([
            'admin_logged_in' => true
        ])->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Агентская квартира 1');
        $response->assertSee('Обычная квартира');
        
        $viewData = $response->original->getData();
        $this->assertArrayHasKey('agencyListings', $viewData);
        $this->assertCount(1, $viewData['agencyListings']);
        $this->assertEquals($agencyListing->id, $viewData['agencyListings']->first()->id);
    }
}
