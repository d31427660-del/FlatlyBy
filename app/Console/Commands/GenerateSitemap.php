<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Listing;
use App\Helpers\CityHelper;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a static sitemap.xml in the public directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap.xml...');

        try {
            $listings = Listing::where('status', 'active')
                ->select(['id', 'title', 'city', 'type', 'rooms', 'weekend_price', 'amenities', 'territory_features', 'nearest_metro', 'description', 'price', 'sleeping_places', 'animals_allowed', 'updated_at'])
                ->get();
            
            $blogPosts = \App\Models\BlogPost::where('source_name', '!=', '34travel.me')
                ->orWhereNull('source_name')
                ->select(['slug', 'updated_at'])
                ->get();
            
            $activeCities = $listings->pluck('city')->unique()->filter()->values();
            
            $citySlugs = $activeCities->map(function($city) {
                return CityHelper::slugify($city);
            })->unique()->values()->all();

            // Предварительный расчет валидных тегов и пересечений на основе активных объявлений
            $validTagsByCity = [];
            $validIntersectionsByCity = [];

            foreach ($listings as $listing) {
                if (empty($listing->city)) continue;
                $citySlug = CityHelper::slugify($listing->city);
                if (empty($citySlug)) continue;

                if (!isset($validTagsByCity[$citySlug])) {
                    $validTagsByCity[$citySlug] = [];
                    $validIntersectionsByCity[$citySlug] = [];
                }

                $isCottage = in_array($listing->type, ['cottage', 'agrotourism']);
                $isHourly = !$isCottage && $listing->weekend_price > 0;
                $isApartment = !$isCottage && !$isHourly;

                $rooms = (int)$listing->rooms;
                $titleLower = is_string($listing->title) ? mb_strtolower($listing->title) : '';
                $descLower = is_string($listing->description) ? mb_strtolower($listing->description) : '';
                $addrLower = is_string($listing->address) ? mb_strtolower($listing->address) : '';

                $amenitiesLower = mb_strtolower(json_encode($listing->amenities, JSON_UNESCAPED_UNICODE) ?: '');
                $territoryLower = mb_strtolower(json_encode($listing->territory_features, JSON_UNESCAPED_UNICODE) ?: '');

                $hasCenter = (str_contains($titleLower, 'центр') || str_contains($descLower, 'центр') || str_contains($addrLower, 'центр'));
                
                $hasMetro = (!empty($listing->nearest_metro) && 
                             !in_array($listing->nearest_metro, ['Нет', 'Метро не указано']) || 
                             str_contains($titleLower, 'метро') || 
                             str_contains($descLower, 'метро') || 
                             str_contains($addrLower, 'метро'));

                $hasJacuzzi = !$isCottage && (str_contains($amenitiesLower, 'джакузи') || str_contains($amenitiesLower, 'гидромассаж') || str_contains($titleLower, 'джакузи') || str_contains($descLower, 'джакузи'));
                
                $hasSauna = $isCottage && (str_contains($amenitiesLower, 'баня') || str_contains($amenitiesLower, 'сауна') || str_contains($territoryLower, 'баня') || str_contains($territoryLower, 'сауна') || str_contains($titleLower, 'баня') || str_contains($titleLower, 'сауна') || str_contains($descLower, 'баня') || str_contains($descLower, 'сауна'));
                
                $hasPool = $isCottage && (str_contains($amenitiesLower, 'бассейн') || str_contains($amenitiesLower, 'бассеин') || str_contains($amenitiesLower, 'купель') || str_contains($territoryLower, 'бассейн') || str_contains($territoryLower, 'бассеин') || str_contains($territoryLower, 'купель') || str_contains($titleLower, 'бассейн') || str_contains($titleLower, 'бассеин') || str_contains($titleLower, 'купель') || str_contains($descLower, 'бассейн') || str_contains($descLower, 'бассеин') || str_contains($descLower, 'купель'));

                $tags = [];
                if ($isApartment || $isHourly) $tags[] = 'kvartiry';
                if ($isCottage) $tags[] = 'kottedzhi';
                if ($isHourly) $tags[] = 'na-chasy';
                
                if ($rooms === 1) $tags[] = 'odnokomnatnye';
                if ($rooms === 2) $tags[] = 'dvuhkomnatnye';
                if ($rooms === 3) $tags[] = 'trehkomnatnye';
                if ($rooms >= 4) $tags[] = 'chetyrehkomnatnye';

                if ($hasCenter) $tags[] = 'v-centre';
                if ($hasMetro) $tags[] = 'metro';
                if ($hasJacuzzi) $tags[] = 'dzhakuzi';
                if ($hasSauna) $tags[] = 's-baney';
                if ($hasPool) $tags[] = 's-basseynom';

                foreach ($tags as $t) {
                    $validTagsByCity[$citySlug][$t] = true;
                }

                $roomsTag = null;
                if ($rooms === 1) $roomsTag = 'odnokomnatnye';
                elseif ($rooms === 2) $roomsTag = 'dvuhkomnatnye';
                elseif ($rooms === 3) $roomsTag = 'trehkomnatnye';
                elseif ($rooms >= 4) $roomsTag = 'chetyrehkomnatnye';

                if ($roomsTag) {
                    $filters = [];
                    if ($isHourly) $filters[] = 'na-chasy';
                    if ($hasCenter) $filters[] = 'v-centre';
                    if ($hasMetro) $filters[] = 'metro';
                    if ($hasJacuzzi) $filters[] = 'dzhakuzi';
                    if ($hasSauna) $filters[] = 's-baney';
                    if ($hasPool) $filters[] = 's-basseynom';

                    foreach ($filters as $f) {
                        $validIntersectionsByCity[$citySlug][$roomsTag . '/' . $f] = true;
                    }
                }
            }

            $xml = view('seo.sitemap', compact('listings', 'citySlugs', 'blogPosts', 'validTagsByCity', 'validIntersectionsByCity'))->render();

            $path = public_path('sitemap.xml');
            File::put($path, $xml);

            $this->info("sitemap.xml generated successfully at: {$path}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to generate sitemap.xml: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
