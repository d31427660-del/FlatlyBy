<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BlogPost;
use Illuminate\Support\Str;

class ParseBlogPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse articles from 34travel.me and seed/update database with working images and data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting blog cleanup...');

        // 1. Delete old 34travel.me posts
        $this->info('Deleting all 34travel.me parsed posts...');
        BlogPost::where('source_name', '34travel.me')->delete();

        // 2. Update Flatly own posts with beautiful high-resolution Unsplash images
        $this->updateOwnPosts();

        $this->info('Blog cleanup completed!');
        return Command::SUCCESS;
    }

    /**
     * Helper to fetch page HTML using cURL
     */
    private function fetchUrl(string $url): ?string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200) {
            return null;
        }

        return $response;
    }

    /**
     * Update Flatly's own blog posts with high-quality Unsplash photos
     */
    private function updateOwnPosts()
    {
        $this->info('Updating own Flatly articles with Unsplash images...');

        $ownPosts = [
            'kak-bezopasno-snyat-kvartiru-posutochno-minsk' => [
                'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=800&q=80',
            ],
            'kakie-pravila-zaseleniya-pri-posutochnoy-arende' => [
                'image' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80',
            ],
            'kak-podgotovit-kvartiru-k-posutochnoy-arende' => [
                'image' => 'https://images.unsplash.com/photo-1556912172-45b7abe8b7e1?auto=format&fit=crop&w=800&q=80',
            ],
            'kakie-oshibki-dopuskayut-vladelcy-posutochnoy-arendy' => [
                'image' => 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=800&q=80',
            ],
            'chto-deshevle-kvartira-ili-otel-v-minske' => [
                'image' => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=800&q=80',
            ],
            'v-kakom-rajone-minska-luchshe-snyat-kvartiru' => [
                'image' => '/images/news_minsk_districts.jpg',
            ],
            'gde-snyat-kvartiru-s-zhivotnymi-v-minske' => [
                'image' => '/images/news_pets.png',
            ],
            'na-chto-obratit-vnimanie-pri-osmotre-kvartiry' => [
                'image' => '/images/news_apartment_check.png',
            ],
            'chto-delat-esli-v-kvartire-slomalas-tehnika' => [
                'image' => '/images/news_apartment_appliances.png',
            ],
            'kak-avtomatizirovat-zaselenie-v-posutochnoy-arende' => [
                'image' => '/images/news_checkin_automation.png',
            ],
            'chto-posmotret-v-breste-za-1-2-dnya' => [
                'image' => '/images/news_brest.png',
            ],
            'kak-doekhat-iz-aeroporta-minska-v-gorod' => [
                'image' => '/images/news_airport.png',
            ],
        ];

        foreach ($ownPosts as $slug => $data) {
            $post = BlogPost::where('slug', $slug)->first();
            if ($post) {
                $post->update(['image' => $data['image']]);
                $this->info("Updated image for own article: {$slug}");
            }
        }
    }
}
