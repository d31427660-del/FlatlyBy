<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\User;
use App\Models\Payment;

class AdminController extends Controller
{
    /**
     * Форма авторизации администратора
     */
    public function loginForm()
    {
        if (session('admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    /**
     * Выполнение авторизации по паролю из .env
     */
    public function login(Request $request)
    {
        $password = $request->input('password');
        $correctPassword = env('ADMIN_PASSWORD', 'flatlyadmin');

        if ($password === $correctPassword) {
            session(['admin_logged_in' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Неверный пароль администратора. Пожалуйста, попробуйте снова.'
        ], 401);
    }

    /**
     * Выход из системы
     */
    public function logout()
    {
        session()->forget('admin_logged_in');
        return redirect()->route('admin.login')->with('success', 'Вы успешно вышли из панели администратора.');
    }

    /**
     * Главная панель модератора/администратора
     */
    public function dashboard()
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login')->with('warning', 'Требуется авторизация администратора.');
        }

        // Fetch all listings with associated host profile and tariffs details
        $listings = Listing::with(['host', 'userTariffs.tariff'])->orderBy('created_at', 'desc')->get();

        // Fetch users
        $users = User::orderBy('created_at', 'desc')->get();

        // Fetch transactions (payments)
        $payments = Payment::with(['user', 'listing', 'tariff', 'promocode'])->orderBy('created_at', 'desc')->get();

        // Calculate accurate metrics
        $pendingPaymentCount = $listings->filter(function ($l) {
            return ($l->status === 'pending_payment' || $l->status === 'pending' || !$l->is_paid) 
                   && $l->status !== 'hidden' 
                   && $l->status !== 'rejected';
        })->count();

        $activeCount = $listings->filter(function ($l) {
            return $l->status === 'active' || ($l->is_paid && $l->status !== 'hidden' && $l->status !== 'rejected');
        })->count();

        $metrics = [
            'total' => $listings->count(),
            'pending_payment' => $pendingPaymentCount,
            'active' => $activeCount,
            'hidden' => Listing::where('status', 'hidden')->count(),
            'hosts' => $users->count(),
        ];

        // Calculate site visits & traffic analytics
        $todayDate = now()->toDateString();
        $uniqueVisitorsToday = 0;
        $pageviewsToday = 0;
        $uniqueVisitors7Days = 0;
        $uniqueVisitors30Days = 0;
        $deviceStats = ['mobile' => 0, 'desktop' => 0, 'tablet' => 0];
        $dailyVisitsChart = [];

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('site_visits')) {
                $uniqueVisitorsToday = \App\Models\SiteVisit::where('visited_date', $todayDate)
                    ->distinct('ip_address')
                    ->count('ip_address');

                $pageviewsToday = \App\Models\SiteVisit::where('visited_date', $todayDate)->count();

                $uniqueVisitors7Days = \App\Models\SiteVisit::where('visited_date', '>=', now()->subDays(7)->toDateString())
                    ->distinct('ip_address')
                    ->count('ip_address');

                $uniqueVisitors30Days = \App\Models\SiteVisit::where('visited_date', '>=', now()->subDays(30)->toDateString())
                    ->distinct('ip_address')
                    ->count('ip_address');

                $deviceCounts = \App\Models\SiteVisit::where('visited_date', '>=', now()->subDays(7)->toDateString())
                    ->selectRaw('device_type, COUNT(*) as count')
                    ->groupBy('device_type')
                    ->pluck('count', 'device_type')
                    ->toArray();

                $deviceStats['mobile'] = $deviceCounts['mobile'] ?? 0;
                $deviceStats['desktop'] = $deviceCounts['desktop'] ?? 0;
                $deviceStats['tablet'] = $deviceCounts['tablet'] ?? 0;

                for ($i = 13; $i >= 0; $i--) {
                    $dateStr = now()->subDays($i)->toDateString();
                    $label = now()->subDays($i)->format('d.m');
                    $uniqueCount = \App\Models\SiteVisit::where('visited_date', $dateStr)
                        ->distinct('ip_address')
                        ->count('ip_address');
                    $viewsCount = \App\Models\SiteVisit::where('visited_date', $dateStr)->count();

                    $dailyVisitsChart[] = [
                        'date' => $dateStr,
                        'label' => $label,
                        'visitors' => $uniqueCount,
                        'pageviews' => $viewsCount,
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch site visits analytics: ' . $e->getMessage());
        }

        $visitorStats = [
            'unique_today' => $uniqueVisitorsToday,
            'views_today' => $pageviewsToday,
            'unique_7days' => $uniqueVisitors7Days,
            'unique_30days' => $uniqueVisitors30Days,
            'devices' => $deviceStats,
            'chart' => $dailyVisitsChart,
        ];

        // Calculate count of active and total listings per city
        $cityStats = $listings->groupBy(function ($l) {
            return trim($l->city) ?: 'Не указан';
        })->map(function ($group) {
            return [
                'active' => $group->filter(function ($l) {
                    return $l->status === 'active';
                })->count(),
                'total' => $group->count(),
            ];
        })->sortByDesc(function ($stats) {
            return $stats['active'];
        });

        $agencyListings = $listings->filter(function ($l) {
            return $l->isAgencyListing();
        });

        return view('admin.dashboard', compact('listings', 'users', 'payments', 'metrics', 'visitorStats', 'cityStats', 'agencyListings'));
    }

    /**
     * Одобрение объявления
     */
    public function approve($id)
    {
        if (!session('admin_logged_in')) {
            return response()->json(['success' => false, 'message' => 'Доступ ограничен.'], 401);
        }

        try {
            $listing = Listing::findOrFail($id);
            
            // Check if listing has an active paid subscription matching its type
            if ($listing->is_paid) {
                $listing->status = 'active';
            } else {
                $listing->status = 'pending_payment';
            }
            
            $listing->moderation_reason = null; // Clear rejection note
            $listing->save();

            return response()->json([
                'success' => true,
                'message' => 'Объявление успешно одобрено и опубликовано!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Отклонение объявления с указанием причины
     */
    public function reject(Request $request, $id)
    {
        if (!session('admin_logged_in')) {
            return response()->json(['success' => false, 'message' => 'Доступ ограничен.'], 401);
        }

        try {
            $reason = $request->input('reason', 'Несоответствие правилам размещения.');
            
            $listing = Listing::findOrFail($id);
            $listing->status = 'rejected';
            $listing->moderation_reason = $reason;
            $listing->save();

            return response()->json([
                'success' => true,
                'message' => 'Объявление отклонено модератором.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Безвозвратное удаление объявления
     */
    public function destroy($id)
    {
        if (!session('admin_logged_in')) {
            return response()->json(['success' => false, 'message' => 'Доступ ограничен.'], 401);
        }

        try {
            $listing = Listing::findOrFail($id);
            $listing->delete(); // SoftDelete

            return response()->json([
                'success' => true,
                'message' => 'Объявление успешно удалено из базы данных.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Обновление файлов баннеров на главной странице
     */
    public function updateBanners(Request $request)
    {
        if (!session('admin_logged_in')) {
            abort(403, 'Доступ ограничен.');
        }

        // Проверяем сырые ошибки загрузки файлов в PHP ($_FILES)
        foreach (['banner1', 'banner2', 'banner3'] as $key) {
            if (isset($_FILES[$key]) && $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE) {
                $error = $_FILES[$key]['error'];
                if ($error !== UPLOAD_ERR_OK) {
                    $messages = [
                        UPLOAD_ERR_INI_SIZE => 'Размер файла превышает лимит сервера (upload_max_filesize в php.ini). Попробуйте сжать изображение.',
                        UPLOAD_ERR_FORM_SIZE => 'Размер файла превышает лимит HTML-формы.',
                        UPLOAD_ERR_PARTIAL => 'Файл был загружен лишь частично.',
                        UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка для загрузки на сервере.',
                        UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск сервера.',
                        UPLOAD_ERR_EXTENSION => 'Загрузка файла прервана PHP-расширением.',
                    ];
                    $msg = $messages[$error] ?? 'Код ошибки: ' . $error;
                    return redirect()->route('admin.dashboard')->with('error', 'Ошибка загрузки файла: ' . $msg);
                }
            }
        }

        $request->validate([
            'banner1' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'banner2' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
            'banner3' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:8192',
        ]);

        try {
            $possiblePaths = [
                public_path('images/banners'),
                base_path('public_html/images/banners'),
                base_path('../public_html/images/banners'),
            ];

            $anyUploaded = false;

            // Обработка баннера 1
            if ($request->hasFile('banner1') && $request->file('banner1')->isValid()) {
                $file = $request->file('banner1');
                foreach ($possiblePaths as $path) {
                    if (is_dir($path) || @mkdir($path, 0755, true)) {
                        @copy($file->getRealPath(), $path . '/flatly_tariffs_banner.png');
                    }
                }
                $anyUploaded = true;
            }

            // Обработка баннера 2
            if ($request->hasFile('banner2') && $request->file('banner2')->isValid()) {
                $file = $request->file('banner2');
                foreach ($possiblePaths as $path) {
                    if (is_dir($path) || @mkdir($path, 0755, true)) {
                        @copy($file->getRealPath(), $path . '/flatly_free_placement_banner.png');
                    }
                }
                $anyUploaded = true;
            }

            // Обработка баннера 3
            if ($request->hasFile('banner3') && $request->file('banner3')->isValid()) {
                $file = $request->file('banner3');
                foreach ($possiblePaths as $path) {
                    if (is_dir($path) || @mkdir($path, 0755, true)) {
                        @copy($file->getRealPath(), $path . '/flatly_minsk_apartments_banner.png');
                    }
                }
                $anyUploaded = true;
            }

            if (!$anyUploaded) {
                return redirect()->route('admin.dashboard')->with('error', 'Пожалуйста, выберите хотя бы один файл изображения для загрузки!');
            }

            return redirect()->route('admin.dashboard')->with('success', 'Баннеры на главной странице успешно обновлены!');
        } catch (\Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Ошибка при обновлении баннеров: ' . $e->getMessage());
        }
    }
}
