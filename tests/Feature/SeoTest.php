<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_listing_slug_generation_and_transliteration()
    {
        $listing = new Listing();
        $listing->title = 'Уютная двушка в центре Минска!';
        
        $this->assertEquals('uyutnaya-dvushka-v-tsentre-minska', $listing->slug);
    }

    public function test_listing_details_page_loads_with_and_without_slug()
    {
        $user = new User();
        $user->name = 'Test User';
        $user->email = 'test@example.com';
        $user->password = bcrypt('password');
        $user->phone = '+375 29 111 22 33';
        $user->save();
        
        $listing = Listing::create([
            'user_id' => $user->id,
            'title' => 'Тестовое объявление',
            'description' => 'Описание тестового объявления',
            'price' => 100,
            'city' => 'Минск',
            'address' => 'ул. Ленина 1',
            'status' => 'active',
            'rooms' => 2,
            'sleeping_places' => 4,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
        ]);

        // Detail page request without slug (redirects to canonical slug URL)
        $response = $this->get("/listings/{$listing->id}");
        $response->assertRedirect("/listings/{$listing->id}-testovoe-obyavlenie");

        // Detail page request with slug
        $responseWithSlug = $this->get("/listings/{$listing->id}-testovoe-obyavlenie");
        $responseWithSlug->assertStatus(200);
        $responseWithSlug->assertSee('Тестовое объявление');

        // Detail page request with incorrect slug (redirects to canonical slug URL)
        $responseIncorrectSlug = $this->get("/listings/{$listing->id}-nepravilnyj-slug");
        $responseIncorrectSlug->assertRedirect("/listings/{$listing->id}-testovoe-obyavlenie");

        // Test with empty slug in listing (e.g. title with only punctuation)
        $listingEmptySlug = Listing::create([
            'user_id' => $user->id,
            'title' => '!!!',
            'description' => 'Описание тестового объявления',
            'price' => 100,
            'city' => 'Минск',
            'address' => 'ул. Ленина 1',
            'status' => 'active',
            'rooms' => 2,
            'sleeping_places' => 4,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
        ]);

        $responseEmptySlug = $this->get("/listings/{$listingEmptySlug->id}");
        $responseEmptySlug->assertRedirect("/listings/{$listingEmptySlug->id}-listing");

        $responseEmptySlugUrl = $this->get("/listings/{$listingEmptySlug->id}-listing");
        $responseEmptySlugUrl->assertStatus(200);
    }

    public function test_similar_listings_block()
    {
        $user = new User();
        $user->name = 'Test User';
        $user->email = 'test_similar_' . uniqid() . '@example.com';
        $user->password = bcrypt('password');
        $user->save();

        $listing1 = Listing::create([
            'user_id' => $user->id,
            'title' => 'Первая квартира в Минске',
            'city' => 'Минск',
            'address' => 'ул. Ленина 1',
            'status' => 'active',
            'price' => 100,
            'latitude' => 53.9,
            'longitude' => 27.5,
        ]);

        $listing2 = Listing::create([
            'user_id' => $user->id,
            'title' => 'Вторая квартира в Минске',
            'city' => 'Минск',
            'address' => 'ул. Ленина 2',
            'status' => 'active',
            'price' => 120,
            'latitude' => 53.9,
            'longitude' => 27.5,
        ]);

        $listing3 = Listing::create([
            'user_id' => $user->id,
            'title' => 'Третья квартира в Минске',
            'city' => 'Минск',
            'address' => 'ул. Ленина 3',
            'status' => 'active',
            'price' => 130,
            'latitude' => 53.9,
            'longitude' => 27.5,
        ]);

        $listingBrest = Listing::create([
            'user_id' => $user->id,
            'title' => 'Квартира в Бресте',
            'city' => 'Брест',
            'address' => 'ул. Московская 10',
            'status' => 'active',
            'price' => 80,
            'latitude' => 52.1,
            'longitude' => 23.7,
        ]);

        $response1 = $this->get("/listings/{$listing1->id}-pervaya-kvartira-v-minske");
        $response1->assertStatus(200);
        $response1->assertSee('Похожие объявления в городе Минск');
        $response1->assertSee('Вторая квартира в Минске');
        $response1->assertSee('Третья квартира в Минске');
        $response1->assertDontSee('Квартира в Бресте');

        $responseBrest = $this->get("/listings/{$listingBrest->id}-kvartira-v-breste");
        $responseBrest->assertStatus(200);
        $responseBrest->assertDontSee('Похожие объявления в городе');
    }

    public function test_similar_listings_block_prioritizes_matching_prices()
    {
        $user = new User();
        $user->name = 'Test User';
        $user->email = 'test_similar_prices_' . uniqid() . '@example.com';
        $user->password = bcrypt('password');
        $user->save();

        // 1. Создаем текущее объявление (город Минск, цена 100)
        $currentListing = Listing::create([
            'user_id' => $user->id,
            'title' => 'Current Listing',
            'city' => 'Минск',
            'address' => 'ул. Ленина 1',
            'status' => 'active',
            'price' => 100,
            'latitude' => 53.9,
            'longitude' => 27.5,
        ]);

        // 2. Создаем квартиру со схожей ценой (город Минск, цена 110 - попадает в диапазон 70-130)
        $idealListing = Listing::create([
            'user_id' => $user->id,
            'title' => 'Ideal Price Listing',
            'city' => 'Минск',
            'address' => 'ул. Ленина 2',
            'status' => 'active',
            'price' => 110,
            'latitude' => 53.9,
            'longitude' => 27.5,
        ]);

        // 3. Создаем квартиру с сильно отличающейся ценой (город Минск, цена 300 - не попадает в диапазон)
        $expensiveListing = Listing::create([
            'user_id' => $user->id,
            'title' => 'Expensive Listing',
            'city' => 'Минск',
            'address' => 'ул. Ленина 3',
            'status' => 'active',
            'price' => 300,
            'latitude' => 53.9,
            'longitude' => 27.5,
        ]);

        // Вызываем страницу
        $response = $this->get("/listings/{$currentListing->id}-current-listing");

        $response->assertStatus(200);

        // Проверяем, что в переданной коллекции похожих объявлений схожая по цене квартира идет раньше дорогой
        $similar = $response->viewData('similarListings');

        $this->assertTrue($similar->contains($idealListing->id));
        $this->assertTrue($similar->contains($expensiveListing->id));
        
        // Первая в списке должна быть идеальная квартира по цене
        $this->assertEquals($idealListing->id, $similar->first()->id);
    }

    public function test_sitemap_xml_generation()
    {
        $user = new User();
        $user->name = 'Test User';
        $user->email = 'test@example.com';
        $user->password = bcrypt('password');
        $user->phone = '+375 29 111 22 33';
        $user->save();
        
        $listing = Listing::create([
            'user_id' => $user->id,
            'title' => 'Квартира на сутки',
            'description' => 'Уютное жилье',
            'price' => 80,
            'city' => 'Минск',
            'address' => 'ул. Маркса 5',
            'status' => 'active',
            'rooms' => 1,
            'sleeping_places' => 2,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
        ]);

        // Create a unique blog post
        $uniquePost = \App\Models\BlogPost::create([
            'title' => 'Наша уникальная статья',
            'slug' => 'nasha-unikalnaya-statya-sitemap',
            'summary' => 'Краткое описание нашей статьи',
            'content' => '<p>Полный текст нашей статьи</p>',
            'source_name' => 'Flatly.by',
        ]);

        // Create a parsed blog post
        $parsedPost = \App\Models\BlogPost::create([
            'title' => 'Спарсенная чужая статья',
            'slug' => 'sparsennaya-chuzhaya-statya-sitemap',
            'summary' => 'Краткое описание спарсенной статьи',
            'content' => '<p>Полный текст спарсенной статьи</p>',
            'source_name' => '34travel.me',
        ]);

        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
        
        // Assert the generated sitemap matches listing slug
        $response->assertSee("/listings/{$listing->id}-kvartira-na-sutki");

        // Assert sitemap contains unique blog post
        $response->assertSee("/blog/nasha-unikalnaya-statya-sitemap");

        // Assert sitemap does NOT contain parsed blog post
        $response->assertDontSee("/blog/sparsennaya-chuzhaya-statya-sitemap");

        // Assert that all requested SEO URLs are present in the sitemap
        $response->assertSee(url('/blog'));
        $response->assertSee(url('/minsk/s-konditsionerom'));
        $response->assertSee(url('/minsk/s-basseynom'));
        $response->assertSee(url('/brestskaya-oblast/s-basseynom'));
        $response->assertSee(url('/minsk/u-vokzala'));
        $response->assertSee(url('/mogilev/u-vokzala'));
        $response->assertSee(url('/minsk/u-parka'));
        $response->assertSee(url('/minsk/dlya-semyi'));
        $response->assertSee(url('/brest/dlya-semyi'));
        $response->assertSee(url('/gomel/dlya-semyi'));
        $response->assertSee(url('/minsk/dlya-komandirovki'));
        $response->assertSee(url('/mogilev/dlya-komandirovki'));
        $response->assertSee(url('/minsk/na-chas'));
        $response->assertSee(url('/minsk/kvartiry-na-sutki-u-metro'));
        $response->assertSee(url('/grodno/dlya-kompanii'));
        $response->assertSee(url('/minsk/kamennaya-gorka'));
        $response->assertSee(url('/minsk/nemiga'));
        $response->assertSee(url('/minsk/zelenyy-lug'));
        $response->assertSee(url('/minsk/serebryanka'));
    }

    public function test_robots_txt_contains_sitemap()
    {
        $robotsContent = file_get_contents(public_path('robots.txt'));
        $this->assertStringContainsString('Sitemap: https://flatly.by/sitemap.xml', $robotsContent);
        $this->assertStringContainsString('Disallow: /admin', $robotsContent);
    }

    public function test_legacy_apartments_urls_redirect_to_home()
    {
        // Test /apartments/minsk -> redirects to /minsk/kvartiry (301)
        $response = $this->get('/apartments/minsk');
        $response->assertRedirect('/minsk/kvartiry');
        $this->assertEquals(301, $response->getStatusCode());

        // Test /apartments -> now returns 200 OK (restored for indexing)
        $response = $this->get('/apartments');
        $response->assertStatus(200);

        // Test /apartment/123-some-slug -> redirects to / (301)
        $response = $this->get('/apartment/123-some-slug');
        $response->assertRedirect('/');
        $this->assertEquals(301, $response->getStatusCode());
        
        // Test /apartments/some-random/nested/path -> redirects to / (301)
        $response = $this->get('/apartments/some-random/nested/path');
        $response->assertRedirect('/');
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function test_pagination_pages_unique_seo_tags_and_canonical()
    {
        $response = $this->get('/minsk/page-2');
        $response->assertStatus(200);

        // Assert Title contains page number
        $response->assertSee(' | Flatly — Страница 2', false);

        // Assert H1 contains page number
        $response->assertSee(' — Страница 2</h1>', false);

        // Assert Meta Description contains page number
        $response->assertSee('Страница 2 списка предложений.', false);

        // Assert Canonical URL points to page 2 (with page-2)
        $response->assertSee('<link rel="canonical" href="https://flatly.by/minsk/page-2">', false);
    }

    public function test_quick_filter_seo_links_rendering_and_active_state()
    {
        // 1. Visit /minsk and assert it contains quick filter SEO link to 1-room apartments
        $response = $this->get('/minsk');
        $response->assertStatus(200);
        $response->assertSee('href="/minsk/odnokomnatnye"', false);
        $response->assertSee('class="qf-link-tag ">1-комнатная</a>', false);

        // 2. Visit /minsk/odnokomnatnye and assert it returns 200 and the link is active
        $response2 = $this->get('/minsk/odnokomnatnye');
        $response2->assertStatus(200);
        $response2->assertSee('href="/minsk/odnokomnatnye"', false);
        $response2->assertSee('class="qf-link-tag active">1-комнатная</a>', false);

        // 3. Visit /apartments (no city selected) and assert it contains quick filter SEO links to cities
        $response3 = $this->get('/apartments');
        $response3->assertStatus(200);
        $response3->assertSee('href="/minsk/kvartiry"', false);
        $response3->assertSee('class="qf-link-tag ">Минск</a>', false);
    }

    public function test_seo_pages_with_empty_listings_does_not_crash()
    {
        $response1 = $this->get('/petrikov/chetyrehkomnatnye/dzhakuzi');
        $response1->assertStatus(200);
        $response1->assertSee('<meta name="robots" content="noindex, nofollow">', false);

        $response2 = $this->get('/khotimsk/odnokomnatnye');
        $response2->assertStatus(200);
        $response2->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_city_legacy_redirects()
    {
        // 1. Redirect /city/korma/chetyrehkomnatnaya -> /korma/chetyrehkomnatnye (301)
        $response1 = $this->get('/city/korma/chetyrehkomnatnaya');
        $response1->assertRedirect('/korma/chetyrehkomnatnye');
        $this->assertEquals(301, $response1->getStatusCode());

        // 2. Redirect /city/minsk -> /minsk (301)
        $response2 = $this->get('/city/minsk');
        $response2->assertRedirect('/minsk');
        $this->assertEquals(301, $response2->getStatusCode());

        // 3. Redirect catch-all nested other pages under /city/ -> /any (301)
        $response3 = $this->get('/city/korma/chetyrehkomnatnaya/na-chasy');
        $response3->assertRedirect('/korma/chetyrehkomnatnaya/na-chasy');
        $this->assertEquals(301, $response3->getStatusCode());
    }

    public function test_minsk_district_landing_pages_and_redirects()
    {
        // Test Uruchye district page
        $responseUruchye = $this->get('/minsk/uruchye');
        $responseUruchye->assertStatus(200);
        $responseUruchye->assertSee('Снять квартиру в Уручье');

        // Test legacy redirect for Uruchye
        $responseRedirectUruchye = $this->get('/minsk/sutki-uruchye');
        $responseRedirectUruchye->assertRedirect('/minsk/uruchye');
        $this->assertEquals(301, $responseRedirectUruchye->getStatusCode());

        // Test Serebryanka district page
        $responseSerebryanka = $this->get('/minsk/serebryanka');
        $responseSerebryanka->assertStatus(200);
        $responseSerebryanka->assertSee('Снять квартиру в Серебрянке');

        // Test legacy redirect for Serebryanka
        $responseRedirectSerebryanka = $this->get('/minsk/sutki-serebryanka');
        $responseRedirectSerebryanka->assertRedirect('/minsk/serebryanka');
        $this->assertEquals(301, $responseRedirectSerebryanka->getStatusCode());
    }

    public function test_parsed_blog_posts_are_excluded()
    {
        // Create a unique post
        $uniquePost = \App\Models\BlogPost::create([
            'title' => 'Наша уникальная статья',
            'slug' => 'nasha-unikalnaya-statya',
            'summary' => 'Краткое описание нашей статьи',
            'content' => '<p>Полный текст нашей статьи</p>',
            'source_name' => 'Flatly.by',
        ]);

        // Create a parsed post
        $parsedPost = \App\Models\BlogPost::create([
            'title' => 'Спарсенная чужая статья',
            'slug' => 'sparsennaya-chuzhaya-statya',
            'summary' => 'Краткое описание спарсенной статьи',
            'content' => '<p>Полный текст спарсенной статьи</p>',
            'source_name' => '34travel.me',
        ]);

        // 1. Check homepage
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Наша уникальная статья');
        $homeResponse->assertDontSee('Спарсенная чужая статья');

        // 2. Check blog index
        $blogResponse = $this->get('/blog');
        $blogResponse->assertStatus(200);
        $blogResponse->assertSee('Наша уникальная статья');
        $blogResponse->assertDontSee('Спарсенная чужая статья');

        // 3. Check unique post detail page
        $uniquePostResponse = $this->get("/blog/{$uniquePost->slug}");
        $uniquePostResponse->assertStatus(200);
        $uniquePostResponse->assertSee('Наша уникальная статья');

        // 4. Check parsed post detail page
        $parsedPostResponse = $this->get("/blog/{$parsedPost->slug}");
        $parsedPostResponse->assertStatus(410);
    }

    public function test_blog_posts_have_correct_images()
    {
        $petsPost = \App\Models\BlogPost::where('slug', 'gde-snyat-kvartiru-s-zhivotnymi-v-minske')->first();
        $this->assertNotNull($petsPost);
        $this->assertEquals('/images/news_pets.png', $petsPost->image);

        $districtsPost = \App\Models\BlogPost::where('slug', 'v-kakom-rajone-minska-luchshe-snyat-kvartiru')->first();
        $this->assertNotNull($districtsPost);
        $this->assertEquals('/images/news_minsk_districts.jpg', $districtsPost->image);

        $checkPost = \App\Models\BlogPost::where('slug', 'na-chto-obratit-vnimanie-pri-osmotre-kvartiry')->first();
        $this->assertNotNull($checkPost);
        $this->assertEquals('/images/news_apartment_check.png', $checkPost->image);

        $appliancesPost = \App\Models\BlogPost::where('slug', 'chto-delat-esli-v-kvartire-slomalas-tehnika')->first();
        $this->assertNotNull($appliancesPost);
        $this->assertEquals('/images/news_apartment_appliances.png', $appliancesPost->image);

        $automationPost = \App\Models\BlogPost::where('slug', 'kak-avtomatizirovat-zaselenie-v-posutochnoy-arende')->first();
        $this->assertNotNull($automationPost);
        $this->assertEquals('/images/news_checkin_automation.png', $automationPost->image);

        $airportPost = \App\Models\BlogPost::where('slug', 'kak-doekhat-iz-aeroporta-minska-v-gorod')->first();
        $this->assertNotNull($airportPost);
        $this->assertEquals('/images/news_airport.png', $airportPost->image);
    }

    public function test_legacy_blog_slugs_redirect_to_new_slugs()
    {
        $response = $this->get('/blog/top-5-rayonov-minska-dlya-posutochnoy-arendy');
        $response->assertRedirect('/blog/v-kakom-rajone-minska-luchshe-snyat-kvartiru');
        $response->assertStatus(301);

        $response2 = $this->get('/blog/kak-vybrat-kvartiru-na-sutki-s-zhivotnymi-v-minske');
        $response2->assertRedirect('/blog/gde-snyat-kvartiru-s-zhivotnymi-v-minske');
        $response2->assertStatus(301);
    }

    public function test_index_php_and_public_urls_redirect_to_clean_urls()
    {
        $response = $this->get('/index.php/guide');
        $response->assertRedirect('/guide');
        $response->assertStatus(301);

        $response2 = $this->get('/public/index.php/guide');
        $response2->assertRedirect('/guide');
        $response2->assertStatus(301);

        $response3 = $this->get('/public/guide');
        $response3->assertRedirect('/guide');
        $response3->assertStatus(301);
    }

    public function test_sitemap_renders_successfully_with_invalid_or_null_listing_attributes(): void
    {
        $user = new User();
        $user->name = 'Test User';
        $user->email = 'test_invalid_' . uniqid() . '@example.com';
        $user->password = bcrypt('password');
        $user->save();

        // Create listing with dirty/malformed attributes
        Listing::create([
            'user_id' => $user->id,
            'title' => 'Некорректное объявление',
            'description' => null,
            'price' => 80,
            'city' => 'Минск',
            'address' => 'ул. Маркса 5',
            'status' => 'active',
            'rooms' => 1,
            'sleeping_places' => 2,
            'bathrooms_count' => 1,
            'latitude' => 53.9,
            'longitude' => 27.5,
            'amenities' => [
                'Основные удобства' => [null, true, ['nested_array'], 123, 'Работает'],
            ],
            'territory_features' => [null, false, 'Парковка'],
            'communications' => [null, 'Разрешено курить'],
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
    }
}
