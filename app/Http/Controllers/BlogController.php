<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogPost;

class BlogController extends Controller
{
    /**
     * Список всех статей блога (Гид по Беларуси)
     */
    public function index(Request $request)
    {
        try {
            $posts = BlogPost::where('source_name', '!=', '34travel.me')
                ->orWhereNull('source_name')
                ->orderBy('created_at', 'desc')
                ->paginate(9);
        } catch (\Exception $e) {
            $posts = collect(); // Fallback for preview/local without DB
        }

        $seoTitle = 'Блог и гид по Беларуси: полезное для путешественников и арендодателей | Flatly.by';
        $seoDescription = 'Статьи, советы и лайфхаки о краткосрочной аренде жилья, путешествиях и главных достопримечательностях Беларуси от Flatly.';

        return view('blog.index', compact('posts', 'seoTitle', 'seoDescription'));
    }

    /**
     * Детальная страница статьи
     */
    public function show($slug)
    {
        $redirects = [
            'top-5-rayonov-minska-dlya-posutochnoy-arendy' => 'v-kakom-rajone-minska-luchshe-snyat-kvartiru',
            'kak-vybrat-kvartiru-na-sutki-s-zhivotnymi-v-minske' => 'gde-snyat-kvartiru-s-zhivotnymi-v-minske',
            'kak-proverit-kvartiru-pered-zaseleniem-chek-list' => 'na-chto-obratit-vnimanie-pri-osmotre-kvartiry',
            'kak-avtomatizirovat-zaselenie-i-vyselenie-v-posutochnoy-arende' => 'kak-avtomatizirovat-zaselenie-v-posutochnoy-arende',
            'posutochnaya-arenda-protiv-oteley-sravnenie' => 'chto-deshevle-kvartira-ili-otel-v-minske',
            'kak-podgotovit-kvartiru-k-posutochnoy-sdache' => 'kak-podgotovit-kvartiru-k-posutochnoy-arende',
            'top-5-oshibok-arendodateley-s-gostyami' => 'kakie-oshibki-dopuskayut-vladelcy-posutochnoy-arendy',
            'pravila-zaseleniya-i-vyseleniya-posutochno' => 'kakie-pravila-zaseleniya-pri-posutochnoy-arende',
        ];

        if (array_key_exists($slug, $redirects)) {
            return redirect()->route('blog.show', ['slug' => $redirects[$slug]], 301);
        }

        try {
            $post = BlogPost::where('slug', $slug)
                ->where(function($q) {
                    $q->where('source_name', '!=', '34travel.me')
                      ->orWhereNull('source_name');
                })
                ->firstOrFail();
            $post->increment('views');

            // Получить 3 другие статьи для блока "Читать также"
            $related = BlogPost::where('id', '!=', $post->id)
                ->where(function($q) {
                    $q->where('source_name', '!=', '34travel.me')
                      ->orWhereNull('source_name');
                })
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        } catch (\Exception $e) {
            try {
                $recentPosts = BlogPost::where('source_name', '!=', '34travel.me')
                    ->orWhereNull('source_name')
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
            } catch (\Exception $ex) {
                $recentPosts = collect();
            }

            return response()->view('errors.410', [
                'type' => 'blog',
                'recentPosts' => $recentPosts
            ], 410);
        }

        $seoTitle = !empty($post->meta_title) ? $post->meta_title : ($post->title . ' — Гид Flatly.by');
        $seoDescription = !empty($post->meta_description) ? $post->meta_description : $post->summary;

        return view('blog.show', compact('post', 'related', 'seoTitle', 'seoDescription'));
    }
}
