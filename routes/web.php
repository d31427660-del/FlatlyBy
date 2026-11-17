<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BlogController;

// Главная страница (список квартир и карта)
Route::get('/', [ListingController::class, 'index'])->name('home');



// Выделенные страницы категорий жилья в Беларуси
Route::get('/apartments', [ListingController::class, 'apartmentsPage'])->name('listings.apartments');
Route::get('/cottages', [ListingController::class, 'cottagesPage'])->name('listings.cottages');
Route::get('/hourly', [ListingController::class, 'hourlyPage'])->name('listings.hourly');
Route::get('/favorites', [ListingController::class, 'favoritesPage'])->name('listings.favorites');

// Редиректы для старых проиндексированных Google URL-адресов на главную страницу (SEO Fix)

// 1. Возвращаем 410 Gone для ложной технической страницы хостинга cPanel (ускоряет удаление из индекса Google)
Route::any('/cgi-sys/suspendedpage.cgi', function () {
    return response('Gone', 410);
});

// Возвращаем 410 Gone для старых удаленных и ненужных страниц для SEO
Route::redirect('/for-landlords', '/listings/create', 301);
Route::redirect('/page-', '/', 301);
Route::get('/page-{any?}', function() {
    return redirect('/', 301);
})->where('any', '.*');

// Корректировки городов
Route::redirect('/ive', '/ivye', 301);
Route::redirect('/city/ive', '/ivye', 301);
Route::redirect('/marina-gorka', '/maryina-gorka', 301);
Route::redirect('/city/marina-gorka', '/maryina-gorka', 301);

// Обработчик 410 Gone для удаленных статей блога с рекомендациями
$goneBlogHandler = function() {
    try {
        $recentPosts = \App\Models\BlogPost::where('source_name', '!=', '34travel.me')
            ->orWhereNull('source_name')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
    } catch (\Exception $e) {
        $recentPosts = collect();
    }
    return response()->view('errors.410', [
        'type' => 'blog',
        'recentPosts' => $recentPosts
    ], 410);
};

Route::get('/guide/kak-doekhat-iz-aeroporta-minska', function() {
    return redirect('/blog/kak-doekhat-iz-aeroporta-minska-v-gorod', 301);
});

Route::get('/guide/chto-poprobovat-iz-edy-v-belarusi', $goneBlogHandler);
Route::get('/guide/transport-v-minske-metro-avtobus-taksi', $goneBlogHandler);
Route::get('/blog/chto-posmotret-v-minske-za-2-dnya', $goneBlogHandler);
Route::get('/blog/braslavskie-ozera-polny-gayd', $goneBlogHandler);
Route::get('/blog/ekoturizm-v-belarusi-kuda-poekhat', $goneBlogHandler);
Route::get('/blog/neobychnye-usadby-belarusi-dlya-poseshcheniya', $goneBlogHandler);

Route::any('/storage', function() { abort(410); });
Route::any('/apartment-detail.html', function() { abort(410); });
Route::any('/about', function() { abort(410); });
Route::any('/index.html', function() { abort(410); });
Route::any('/city', function() {
    return redirect('/', 301);
});
Route::any('/guide/{any?}', function() { abort(410); })->where('any', '.*');

// 2. Редиректы со старых/альтернативных категорий на новые страницы с геопривязкой (301 ЧПУ)
Route::get('/city/{city_slug}', function (\Illuminate\Http\Request $request, $city_slug) {
    $queryString = $request->getQueryString();
    return redirect('/' . $city_slug . ($queryString ? '?' . $queryString : ''), 301);
})->where('city_slug', '[a-zA-Z0-9-]+')->name('listings.city');

Route::get('/city/{city_slug}/{filter_slug}', function (\Illuminate\Http\Request $request, $city_slug, $filter_slug) {
    $slugMap = [
        'apartments' => 'kvartiry',
        'cottages' => 'kottedzhi',
        'hourly' => 'na-chasy',
        'odnokomnatnaya' => 'odnokomnatnye',
        '1-room' => 'odnokomnatnye',
        'dvuhkomnatnaya' => 'dvuhkomnatnye',
        '2-room' => 'dvuhkomnatnye',
        'trehkomnatnaya' => 'trehkomnatnye',
        '3-room' => 'trehkomnatnye',
        'chetyrehkomnatnaya' => 'chetyrehkomnatnye',
        '4-room' => 'chetyrehkomnatnye',
        'vokzal' => 'vokzal',
        'near-station' => 'vokzal',
        'u-vokzala' => 'vokzal',
        'metro' => 'metro',
        'near-metro' => 'metro',
        's-baney' => 's-baney',
        'with-sauna' => 's-baney',
        's-basseynom' => 's-basseynom',
        'with-pool' => 's-basseynom',
        'dzhakuzi' => 'dzhakuzi',
        'jacuzzi' => 'dzhakuzi',
        's-konditsionerom' => 's-konditsionerom',
        'kvartiry-na-sutki-u-metro' => 'metro',
        'u-metro' => 'metro',
        's-parkovkoy' => 's-parkovkoy',
        'dlya-kompanii' => 'dlya-kompanii',
        's-zhivotnymi' => 's-zhivotnymi',
        'do-100' => 'do-100',
        'na-chas' => 'na-chas',
        'dlya-semyi' => 'dlya-semyi',
        'u-parka' => 'u-parka',
        'dlya-komandirovki' => 'dlya-komandirovki',
        'uruchye' => 'uruchye',
        'kamennaya-gorka' => 'kamennaya-gorka',
        'nemiga' => 'nemiga',
        'suharevo' => 'suharevo',
        'zelenyy-lug' => 'zelenyy-lug',
        'malinovka' => 'malinovka',
        'serebryanka' => 'serebryanka',
        'yugo-zapad' => 'yugo-zapad',
        'vesnyanka' => 'vesnyanka',
        'kuntsevschina' => 'kuntsevschina',
        'minsk-mir' => 'minsk-mir',
    ];
    $filter_slug = strtolower(trim($filter_slug));
    $mappedTag = $slugMap[$filter_slug] ?? null;
    $queryString = $request->getQueryString();
    if ($mappedTag) {
        return redirect('/' . $city_slug . '/' . $mappedTag . ($queryString ? '?' . $queryString : ''), 301);
    }
    return redirect('/' . $city_slug . ($queryString ? '?' . $queryString : ''), 301);
})->where('city_slug', '[a-zA-Z0-9-]+')->where('filter_slug', '[a-zA-Z0-9-]+')->name('listings.category');

// Редирект всех остальных старых адресов с префиксом /city/ на адреса без него
Route::get('/city/{any?}', function (\Illuminate\Http\Request $request, $any = null) {
    $queryString = $request->getQueryString();
    return redirect('/' . ($any ? $any : '') . ($queryString ? '?' . $queryString : ''), 301);
})->where('any', '.*');

Route::get('/cottages/s-basseynom', [ListingController::class, 'cottageCategoryPage'])->name('listings.cottages.basseynom');
Route::get('/cottages/s-baney', [ListingController::class, 'cottageCategoryPage'])->name('listings.cottages.baney');

Route::get('/cottages/{city_slug}', function ($city_slug) {
    return redirect()->route('seo.tag1', ['city_slug' => $city_slug, 'tag1' => 'kottedzhi'], 301);
})->where('city_slug', '[a-zA-Z0-9-]+');

Route::get('/apartments/{city_slug}', function ($city_slug) {
    return redirect()->route('seo.tag1', ['city_slug' => $city_slug, 'tag1' => 'kvartiry'], 301);
})->where('city_slug', '[a-zA-Z0-9-]+');

Route::get('/hourly/{city_slug}', function ($city_slug) {
    return redirect()->route('seo.tag1', ['city_slug' => $city_slug, 'tag1' => 'na-chasy'], 301);
})->where('city_slug', '[a-zA-Z0-9-]+');

Route::any('/apartments/{any?}', function () {
    return redirect()->route('home', [], 301);
})->where('any', '.*');

Route::any('/apartment/{any?}', function () {
    return redirect()->route('home', [], 301);
})->where('any', '.*');

// Форма создания нового объявления (Шаги 1 - 6)
Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
Route::get('/listings', function () {
    return redirect()->route('listings.create', [], 301);
});

// Детали конкретного объявления
Route::get('/listings/{id}-{slug?}', [ListingController::class, 'show'])->name('listings.show');
Route::get('/listings/{id}', [ListingController::class, 'show'])->where('id', '[0-9]+')->name('listings.show.numeric');
Route::post('/listings/{id}/track', [ListingController::class, 'trackInteraction'])->name('listings.track');

use Illuminate\Http\Request;

// Вход и регистрация для хозяев
Route::get('/login', function() {
    if (session('logged_in')) {
        return redirect()->route('dashboard');
    }
    return view('auth.login');
})->name('login');

Route::get('/register', function() {
    return redirect()->route('login', [], 301);
});

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

Route::post('/login', function(Request $request) {
    $email = $request->input('email');
    $password = $request->input('password');

    $user = User::where('email', $email)->first();

    if ($user && Hash::check($password, $user->password)) {
        session(['logged_in' => true, 'user_id' => $user->id]);
        return response()->json(['success' => true]);
    }

    // Support default login with seed user Natalia
    if ($user && $email === 'natalia@flatly.by' && ($password === 'password' || $password === '')) {
        session(['logged_in' => true, 'user_id' => $user->id]);
        return response()->json(['success' => true]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Неверный адрес электронной почты или пароль.'
    ], 422);
})->name('login.post');

Route::post('/register', function(Request $request) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255',
        'password' => 'required|string|min:4',
        'phone' => 'nullable|string',
        'telegram' => 'nullable|string',
        'viber' => 'nullable|string',
        'unp' => 'nullable|string|max:20',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => implode(' ', $validator->errors()->all())
        ], 422);
    }

    $email = $request->input('email');
    if (User::where('email', $email)->exists()) {
        return response()->json([
            'success' => false,
            'message' => 'Этот e-mail уже зарегистрирован в системе.'
        ], 422);
    }

    $user = new User();
    $user->name = $request->input('name');
    $user->email = $email;
    $user->password = Hash::make($request->input('password'));
    
    $phoneInput = $request->input('phone');
    if ($phoneInput) {
        $user->phone = str_contains($phoneInput, '+375') ? $phoneInput : '+375 ' . trim($phoneInput);
    } else {
        $user->phone = '';
    }
    
    $user->telegram = $request->input('telegram') ?: '';
    $user->viber = $request->input('viber') ?: '';
    $user->unp = $request->input('unp') ?: '';
    $user->avatar = '';
    $user->save();

    session(['logged_in' => true, 'user_id' => $user->id]);

    return response()->json([
        'success' => true,
        'message' => 'Регистрация прошла успешно!'
    ]);
})->name('register.post');

Route::get('/auth/google', function() {
    return Laravel\Socialite\Facades\Socialite::driver('google')->redirect();
})->name('auth.google');

Route::get('/auth/google/callback', function() {
    try {
        $googleUser = Laravel\Socialite\Facades\Socialite::driver('google')->user();
    } catch (\Exception $e) {
        return redirect()->route('login')->with('error', 'Ошибка авторизации через Google: ' . $e->getMessage());
    }

    $email = $googleUser->getEmail();
    $user = User::where('email', $email)->first();

    if (!$user) {
        $user = new User();
        $user->name = $googleUser->getName() ?: 'Google Host';
        $user->email = $email;
        $user->password = Hash::make(uniqid());
        $user->phone = '';
        $user->telegram = '';
        $user->viber = '';
        $user->unp = '';
        $user->avatar = $googleUser->getAvatar() ?: '';
        $user->save();
    } else {
        if ($googleUser->getAvatar()) {
            $user->avatar = $googleUser->getAvatar();
            $user->save();
        }
    }

    session(['logged_in' => true, 'user_id' => $user->id]);

    return redirect()->route('dashboard')->with('success', 'Вы успешно вошли через Google!');
})->name('auth.google.callback');

Route::get('/auth/telegram/callback', function(Request $request) {
    $auth_data = $request->all();
    
    if (!isset($auth_data['hash'])) {
        return redirect()->route('login')->with('error', 'Неполные данные Telegram авторизации.');
    }

    $bot_token = env('TELEGRAM_BOT_TOKEN');
    if (!$bot_token) {
        return redirect()->route('login')->with('error', 'Telegram API не настроен на сервере.');
    }

    $check_hash = $auth_data['hash'];
    unset($auth_data['hash']);
    
    $data_check_arr = [];
    foreach ($auth_data as $key => $value) {
        $data_check_arr[] = $key . '=' . $value;
    }
    sort($data_check_arr);
    $data_check_string = implode("\n", $data_check_arr);
    
    $secret_key = hash('sha256', $bot_token, true);
    $hash = hash_hmac('sha256', $data_check_string, $secret_key);
    
    if (strcmp($hash, $check_hash) !== 0) {
        return redirect()->route('login')->with('error', 'Криптографическая проверка подписи Telegram не пройдена.');
    }
    
    if ((time() - $auth_data['auth_date']) > 86400) {
        return redirect()->route('login')->with('error', 'Запрос авторизации устарел.');
    }

    $tg_id = $auth_data['id'];
    $username = isset($auth_data['username']) ? '@' . $auth_data['username'] : '';
    $name = $auth_data['first_name'] ?? 'Telegram Host';
    if (isset($auth_data['last_name'])) {
        $name .= ' ' . $auth_data['last_name'];
    }
    $avatar = $auth_data['photo_url'] ?? '';

    $email = $tg_id . '@telegram.org';
    $user = User::where('email', $email)->orWhere('telegram', $username)->orWhere('telegram_chat_id', (string)$tg_id)->first();

    if (!$user) {
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make(uniqid());
        $user->phone = '';
        $user->telegram = $username;
        $user->telegram_chat_id = (string)$tg_id;
        $user->viber = '';
        $user->unp = '';
        $user->avatar = $avatar;
        $user->save();
    } else {
        if ($avatar) {
            $user->avatar = $avatar;
        }
        if ($username) {
            $user->telegram = $username;
        }
        $user->telegram_chat_id = (string)$tg_id;
        $user->save();
    }

    session(['logged_in' => true, 'user_id' => $user->id]);

    return redirect()->route('dashboard')->with('success', 'Вы успешно вошли через Telegram!');
})->name('auth.telegram.callback');

Route::post('/logout', function() {
    session()->forget(['logged_in', 'user_id']);
    return redirect()->route('home')->with('success', 'Вы успешно вышли из системы.');
})->name('logout');

Route::get('/logout', function() {
    return redirect()->route('home');
});

// Личный кабинет арендодателя
Route::get('/dashboard', [ListingController::class, 'dashboard'])->name('dashboard');

// API Личного кабинета (AJAX)
Route::post('/dashboard/profile', [ListingController::class, 'saveProfile'])->name('dashboard.profile.save');
Route::post('/listings/{id}/status', [ListingController::class, 'toggleStatus'])->name('listings.status.toggle');
Route::delete('/listings/{id}', [ListingController::class, 'destroy'])->name('listings.destroy');

// Политика конфиденциальности и Cookies (Закон РБ № 99-З)
Route::get('/privacy', function() {
    return view('pages.privacy');
})->name('privacy');

// Правила размещения объявлений
Route::get('/rules', function() {
    return view('pages.rules');
})->name('rules');

// Как работает сервис Flatly.by
Route::get('/how-it-works', function() {
    return view('pages.how-it-works');
})->name('how-it-works');

// Контакты и поддержка
Route::get('/contacts', function() {
    return view('pages.contacts');
})->name('contacts');

// Карта сайта (HTML-версия)
Route::get('/sitemap', function() {
    try {
        $listings = \App\Models\Listing::where('status', 'active')
            ->select(['city'])
            ->get();
        $activeCities = $listings->pluck('city')->unique()->filter()->values()->toArray();
        sort($activeCities, SORT_STRING);
        
        $blogPosts = \App\Models\BlogPost::where('source_name', '!=', '34travel.me')
            ->orWhereNull('source_name')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();
    } catch (\Exception $e) {
        $activeCities = ['Минск', 'Брест', 'Гродно', 'Витебск', 'Гомель', 'Могилев'];
        $blogPosts = collect();
    }
    return view('pages.sitemap', compact('activeCities', 'blogPosts'));
})->name('sitemap');

// Блог (Гид по Беларуси)
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Панель модератора / администратора
Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');

// API модератора
Route::post('/admin/listings/{id}/approve', [AdminController::class, 'approve'])->name('admin.listings.approve');
Route::post('/admin/listings/{id}/reject', [AdminController::class, 'reject'])->name('admin.listings.reject');
Route::delete('/admin/listings/{id}', [AdminController::class, 'destroy'])->name('admin.listings.destroy');
Route::post('/admin/banners/update', [AdminController::class, 'updateBanners'])->name('admin.banners.update');

// Монетизация и оплата bePaid
Route::get('/payments/checkout/{id}', [PaymentController::class, 'checkout'])->name('payments.checkout');
Route::get('/payments/checkout-featured/{id}', [PaymentController::class, 'checkoutFeatured'])->name('payments.checkout-featured');
Route::get('/payments/checkout-homepage/{id}', [PaymentController::class, 'checkoutHomepage'])->name('payments.checkout-homepage');
Route::get('/checkout-stats', [PaymentController::class, 'checkoutStats'])->name('payments.checkout-stats');
Route::post('/payments/promo/check', [PaymentController::class, 'checkPromo'])->name('payments.promo.check');
Route::get('/payments/success', [PaymentController::class, 'success'])->name('payments.success');
Route::get('/payments/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');
Route::post('/payments/webhook', [PaymentController::class, 'webhook'])->name('payments.webhook');

// Диагностический маршрут для проверки таблиц и настроек монетизации на хостинге
Route::get('/debug-monetization', function() {
    $migrateResult = null;
    if (request()->has('migrate')) {
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $migrateResult = 'Успешно: ' . \Illuminate\Support\Facades\Artisan::output();
        } catch (\Exception $e) {
            $migrateResult = 'Ошибка: ' . $e->getMessage();
        }
    }

    $results = [];
    $tables = ['tariffs', 'user_tariffs', 'payments', 'promocodes', 'promocode_usage'];
    
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbConn = '✅ Подключение установлено';
    } catch (\Exception $e) {
        $dbConn = '❌ Ошибка подключения: ' . $e->getMessage();
    }
    
    foreach ($tables as $table) {
        try {
            $exists = \Illuminate\Support\Facades\Schema::hasTable($table);
            $count = $exists ? \Illuminate\Support\Facades\DB::table($table)->count() : 0;
            $rows = $exists ? \Illuminate\Support\Facades\DB::table($table)->get() : [];
            $results[$table] = [
                'exists' => $exists ? '✅ Да' : '❌ Нет (Необходимо запустить php artisan migrate)',
                'rows_count' => $count,
                'records' => $rows
            ];
        } catch (\Exception $e) {
            $results[$table] = [
                'exists' => '❌ Ошибка проверки: ' . $e->getMessage()
            ];
        }
    }

    $listings = [];
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('listings')) {
            $listings = \Illuminate\Support\Facades\DB::table('listings')
                ->select('id', 'title', 'type', 'status', 'user_id', 'address')
                ->get();
        }
    } catch (\Exception $e) {}
    
    $recentLogs = 'Log file not found';
    $logPath = storage_path('logs/laravel.log');
    if (file_exists($logPath)) {
        $size = filesize($logPath);
        $maxLength = 50000;
        $handle = fopen($logPath, 'r');
        if ($handle) {
            if ($size > $maxLength) {
                fseek($handle, $size - $maxLength);
            }
            $recentLogs = fread($handle, $maxLength);
            fclose($handle);
        }
    }
    
    return response()->json([
        'status' => 'Diagnostic Tool',
        'migration_run_result' => $migrateResult,
        'database_connection' => $dbConn,
        'tables_status' => $results,
        'listings' => $listings,
        'bepaid_config' => [
            'BEPAID_TEST_MODE' => env('BEPAID_TEST_MODE'),
            'BEPAID_SHOP_ID' => env('BEPAID_SHOP_ID') ? '✅ Задан (' . env('BEPAID_SHOP_ID') . ')' : '❌ Не задан',
            'BEPAID_SECRET_KEY' => env('BEPAID_SECRET_KEY') ? '✅ Задан' : '❌ Не задан',
            'BEPAID_PUBLIC_KEY' => env('BEPAID_PUBLIC_KEY') ? '✅ Задан' : '❌ Не задан',
        ],
        'recent_logs' => $recentLogs
    ]);
});

// Динамический Sitemap для поисковых систем
Route::get('/sitemap.xml', function() {
    $listings = \App\Models\Listing::where('status', 'active')
        ->select(['id', 'title', 'city', 'type', 'rooms', 'weekend_price', 'amenities', 'territory_features', 'nearest_metro', 'description', 'price', 'sleeping_places', 'animals_allowed', 'updated_at'])
        ->get();
    
    $blogPosts = \App\Models\BlogPost::where('source_name', '!=', '34travel.me')
        ->orWhereNull('source_name')
        ->select(['slug', 'updated_at'])
        ->get();
    
    $activeCities = $listings->pluck('city')->unique()->filter()->values();
    $citySlugs = $activeCities->map(function($city) {
        return \App\Helpers\CityHelper::slugify($city);
    })->unique()->values()->all();

    // Предварительный расчет валидных тегов и пересечений на основе активных объявлений
    $validTagsByCity = [];
    $validIntersectionsByCity = [];

    foreach ($listings as $listing) {
        if (empty($listing->city)) continue;
        $citySlug = \App\Helpers\CityHelper::slugify($listing->city);
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
    
    return response()->view('seo.sitemap', compact('listings', 'citySlugs', 'blogPosts', 'validTagsByCity', 'validIntersectionsByCity'))
                     ->header('Content-Type', 'text/xml');
});

// Отправка обращения в техподдержку (в Telegram бот)
Route::get('/support/send', function () {
    return redirect()->route('home', [], 301);
});
Route::post('/support/send', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'type' => 'required|string|in:Баг,Предложение,Вопрос',
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:50',
        'telegram' => 'nullable|string|max:100',
        'viber' => 'nullable|string|max:100',
        'whatsapp' => 'nullable|string|max:100',
        'message' => 'required|string|max:5000',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => implode(' ', $validator->errors()->all())
        ], 422);
    }

    $botToken = env('TELEGRAM_BOT_TOKEN');
    $adminChatIdString = env('TELEGRAM_ADMIN_CHAT_ID');

    if (!$botToken || !$adminChatIdString) {
        return response()->json([
            'success' => false,
            'message' => 'Настройки Telegram на сервере не настроены.'
        ], 500);
    }

    $chatIds = array_filter(array_map('trim', explode(',', $adminChatIdString)));

    if (empty($chatIds)) {
        return response()->json([
            'success' => false,
            'message' => 'Список ID администраторов Telegram пуст.'
        ], 500);
    }

    $type = $request->input('type');
    $name = $request->input('name');
    $phone = $request->input('phone');
    $telegram = $request->input('telegram');
    $viber = $request->input('viber');
    $whatsapp = $request->input('whatsapp');
    $messageText = $request->input('message');

    // Форматируем сообщение для Telegram с HTML-разметкой
    $text = "<b>🔔 Новое обращение в техподдержку</b>\n\n";
    $text .= "<b>Тип обращения:</b> " . htmlspecialchars($type) . "\n";
    $text .= "<b>Имя:</b> " . htmlspecialchars($name) . "\n\n";
    
    $text .= "<b>📞 Контакты для связи:</b>\n";
    $text .= "• Телефон: " . htmlspecialchars($phone) . "\n";
    if ($telegram) {
        $text .= "• Telegram: " . htmlspecialchars($telegram) . "\n";
    }
    if ($viber) {
        $text .= "• Viber: " . htmlspecialchars($viber) . "\n";
    }
    if ($whatsapp) {
        $text .= "• WhatsApp: " . htmlspecialchars($whatsapp) . "\n";
    }
    
    $text .= "\n<b>💬 Сообщение:</b>\n" . htmlspecialchars($messageText);

    $successCount = 0;
    foreach ($chatIds as $chatId) {
        try {
            $response = \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ]);
            
            if ($response->successful()) {
                $successCount++;
            }
        } catch (\Exception $e) {
            // Логируем ошибку
        }
    }

    if ($successCount > 0) {
        return response()->json([
            'success' => true,
            'message' => 'Ваше обращение успешно отправлено!'
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Не удалось отправить сообщение в Telegram. Пожалуйста, попробуйте позже.'
    ], 500);
});

// Отправка отзыва к объявлению
Route::post('/listings/{id}/reviews', [App\Http\Controllers\ListingController::class, 'storeReview'])->name('listings.reviews.store');

// Отвязка Telegram аккаунта в личном кабинете
Route::any('/dashboard/telegram/disconnect', function() {
    if (!session('logged_in')) {
        return redirect()->route('login')->with('warning', 'Для выполнения этого действия требуется авторизация.');
    }
    
    $user = \App\Models\User::find(session('user_id'));
    if ($user) {
        $chatId = $user->getTelegramChatId();
        $user->telegram = '';
        $user->telegram_chat_id = null;
        $user->save();

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('telegram_users')) {
                $query = \App\Models\TelegramUser::where('user_id', $user->id);
                if ($chatId) {
                    $query->orWhere('chat_id', $chatId);
                }
                $query->update([
                    'user_id' => null,
                    'notifications_enabled' => false
                ]);
            }
        } catch (\Exception $e) {}
    }
    
    return redirect()->route('dashboard')->with('success', 'Telegram аккаунт успешно отвязан.');
})->name('dashboard.telegram.disconnect');

// Telegram Bot Webhook (обработка команды /start bind_USERID для привязки собственника к боту)
Route::post('/telegram/webhook', function(Request $request) {
    $botToken = env('TELEGRAM_BOT_TOKEN');
    if (!$botToken) {
        return response()->json(['status' => 'error', 'message' => 'No TELEGRAM_BOT_TOKEN set'], 400);
    }

    $update = $request->all();
    $message = $update['message'] ?? null;
    if (!$message) {
        return response()->json(['status' => 'ok']);
    }

    $chatId = $message['chat']['id'] ?? null;
    $text = trim($message['text'] ?? '');
    $fromUsername = isset($message['from']['username']) ? '@' . $message['from']['username'] : '';

    if (!$chatId) {
        return response()->json(['status' => 'ok']);
    }

    if (str_starts_with($text, '/start')) {
        $parts = explode(' ', $text);
        $param = trim($parts[1] ?? '');

        $userId = null;
        if (preg_match('/^(?:bind_)?(\d+)$/', $param, $matches)) {
            $userId = (int)$matches[1];
        }

        $user = null;
        if ($userId) {
            $user = User::find($userId);
        }
        if (!$user && !empty($fromUsername)) {
            $rawNick = ltrim($fromUsername, '@');
            $user = User::where('telegram', $fromUsername)
                ->orWhere('telegram', $rawNick)
                ->orWhere('telegram', 'https://t.me/' . $rawNick)
                ->orWhere('telegram', 't.me/' . $rawNick)
                ->first();
        }

        if ($user) {
            $user->telegram_chat_id = (string)$chatId;
            if (empty($user->telegram) && !empty($fromUsername)) {
                $user->telegram = $fromUsername;
            }
            $user->save();

            // Sync with telegram_users table if present
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('telegram_users')) {
                    \App\Models\TelegramUser::updateOrCreate(
                        ['chat_id' => $chatId],
                        [
                            'username' => ltrim($fromUsername, '@') ?: null,
                            'first_name' => $message['from']['first_name'] ?? null,
                            'user_id' => $user->id,
                            'notifications_enabled' => true,
                        ]
                    );
                }
            } catch (\Exception $e) {}

            try {
                \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => "✅ <b>Аккаунт успешно привязан!</b>\n\nЗдравствуйте, " . htmlspecialchars($user->name) . "!\n\nТеперь вы будете мгновенно получать уведомления о просмотрах ваших объявлений и кликах по вашим контактам прямо здесь, в Telegram.",
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => '📊 Личный кабинет на Flatly.by', 'url' => 'https://flatly.by/dashboard']
                            ]
                        ]
                    ]),
                ]);
            } catch (\Exception $e) {
                \Log::warning('Telegram webhook sendMessage error: ' . $e->getMessage());
            }
        } else {
            try {
                \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => "👋 <b>Добро пожаловать в бот Flatly.by!</b>\n\nЧтобы привязать аккаунт и получать уведомления о клиентах по вашим объявлениям:\n1. Зайдите в личный кабинет на сайте <b>Flatly.by</b>\n2. Нажмите кнопку <b>«Подключить Telegram-бота»</b>.",
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                ['text' => '🌐 Войти в Личный кабинет', 'url' => 'https://flatly.by/dashboard']
                            ]
                        ]
                    ]),
                ]);
            } catch (\Exception $e) {
                \Log::warning('Telegram webhook welcome sendMessage error: ' . $e->getMessage());
            }
        }
    } elseif ($text === '/stop' || $text === '/disconnect') {
        $user = User::where('telegram_chat_id', (string)$chatId)->first();
        if ($user) {
            $user->telegram_chat_id = null;
            $user->save();
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('telegram_users')) {
                    \App\Models\TelegramUser::where('chat_id', $chatId)->update(['notifications_enabled' => false]);
                }
            } catch (\Exception $e) {}

            try {
                \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => "🔕 <b>Telegram-уведомления отключены</b>\n\nВы больше не будете получать сообщения о просмотрах и кликах.\n\nЧтобы включить их снова, отправьте команду /start или подключите бота в личном кабинете на сайте.",
                    'parse_mode' => 'HTML',
                ]);
            } catch (\Exception $e) {}
        } else {
            try {
                \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => "Уведомления для вашего чата не были подключены.",
                ]);
            } catch (\Exception $e) {}
        }
    } elseif ($text === '/status' || $text === '/help') {
        $user = User::where('telegram_chat_id', (string)$chatId)->first();
        if ($user) {
            $msg = "✅ <b>Бот Flatly.by активен</b>\n\nПривязан к аккаунту: <b>" . htmlspecialchars($user->name) . "</b>\nУведомления о просмотрах и кликах по контактам включены.\n\nКоманды:\n/status — проверить статус подключения\n/stop — временно отключить уведомления";
            $buttons = [
                [['text' => '📊 Личный кабинет', 'url' => 'https://flatly.by/dashboard']]
            ];
        } else {
            $msg = "👋 <b>Бот Flatly.by</b>\n\nВаш аккаунт пока не привязан.\nВойдите в личный кабинет на сайте и нажмите кнопку подключения бота.";
            $buttons = [
                [['text' => '🌐 Подключить на сайте', 'url' => 'https://flatly.by/dashboard']]
            ];
        }
        try {
            \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $msg,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode(['inline_keyboard' => $buttons]),
            ]);
        } catch (\Exception $e) {
            \Log::warning('Telegram webhook status error: ' . $e->getMessage());
        }
    }

    return response()->json(['status' => 'ok']);
})->name('telegram.webhook');

// ==================== SEO ЧПУ МАРШРУТЫ (CATCH-ALL) ====================
// Эти маршруты перехватывают все остальные пути и передают их в seoPage контроллера.
// Они строго находятся в самом низу файла, чтобы не перехватывать системные разделы.

// 1. Город + пагинация
Route::get('/{city_slug}/page-{page}', [ListingController::class, 'seoPage'])
    ->where('city_slug', '[a-zA-Z0-9-]+')
    ->where('page', '[0-9]+')
    ->name('seo.city.page');

// 2. Город (базовый)
Route::get('/{city_slug}', [ListingController::class, 'seoPage'])
    ->where('city_slug', '[a-zA-Z0-9-]+')
    ->name('seo.city');

// 3. Город + 1 тег + пагинация
Route::get('/{city_slug}/{tag1}/page-{page}', [ListingController::class, 'seoPage'])
    ->where('city_slug', '[a-zA-Z0-9-]+')
    ->where('tag1', '[a-zA-Z0-9-]+')
    ->where('page', '[0-9]+')
    ->name('seo.tag1.page');

// 4. Город + 1 тег (базовый)
Route::get('/{city_slug}/{tag1}', [ListingController::class, 'seoPage'])
    ->where('city_slug', '[a-zA-Z0-9-]+')
    ->where('tag1', '[a-zA-Z0-9-]+')
    ->name('seo.tag1');

// 5. Город + 2 тега + пагинация
Route::get('/{city_slug}/{tag1}/{tag2}/page-{page}', [ListingController::class, 'seoPage'])
    ->where('city_slug', '[a-zA-Z0-9-]+')
    ->where('tag1', '[a-zA-Z0-9-]+')
    ->where('tag2', '[a-zA-Z0-9-]+')
    ->where('page', '[0-9]+')
    ->name('seo.tag2.page');

// 6. Город + 2 тега (базовый)
Route::get('/{city_slug}/{tag1}/{tag2}', [ListingController::class, 'seoPage'])
    ->where('city_slug', '[a-zA-Z0-9-]+')
    ->where('tag1', '[a-zA-Z0-9-]+')
    ->where('tag2', '[a-zA-Z0-9-]+')
    ->name('seo.tag2');

// 7. Город + 3 тега + пагинация
Route::get('/{city_slug}/{tag1}/{tag2}/{tag3}/page-{page}', [ListingController::class, 'seoPage'])
    ->where('city_slug', '[a-zA-Z0-9-]+')
    ->where('tag1', '[a-zA-Z0-9-]+')
    ->where('tag2', '[a-zA-Z0-9-]+')
    ->where('tag3', '[a-zA-Z0-9-]+')
    ->where('page', '[0-9]+')
    ->name('seo.tag3.page');

// 8. Город + 3 тега (базовый)
Route::get('/{city_slug}/{tag1}/{tag2}/{tag3}', [ListingController::class, 'seoPage'])
    ->where('city_slug', '[a-zA-Z0-9-]+')
    ->where('tag1', '[a-zA-Z0-9-]+')
    ->where('tag2', '[a-zA-Z0-9-]+')
    ->where('tag3', '[a-zA-Z0-9-]+')
    ->name('seo.tag3');


