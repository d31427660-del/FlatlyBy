<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\Tariff;
use App\Models\Payment;
use App\Models\UserTariff;
use App\Models\Promocode;
use App\Models\PromocodeUsage;
use App\Services\BePaidService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected BePaidService $bePaid;

    public function __construct(BePaidService $bePaid)
    {
        $this->bePaid = $bePaid;
    }

    /**
     * Helper to verify logged-in user in controllers.
     */
    protected function ensureLoggedUser()
    {
        if (!session('logged_in') || !session('user_id')) {
            return null;
        }
        return \App\Models\User::find(session('user_id'));
    }

    /**
     * AJAX action to validate a promo code.
     */
    public function checkPromo(Request $request)
    {
        $user = $this->ensureLoggedUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Авторизация не найдена.'], 401);
        }

        $code = trim($request->input('code', ''));
        $type = $request->input('type'); // 'stats' or default/tariff
        $listingId = intval($request->input('listing_id', 0));
        $months = intval($request->input('months', 1));

        if (empty($code)) {
            return response()->json(['success' => false, 'message' => 'Введите промокод.'], 422);
        }

        if ($type === 'featured') {
            $basePrice = 15.00;
            $totalRaw = $basePrice * $months;
            $multiplier = 1.0;
            if ($months === 3) {
                $multiplier = 0.9;
            } elseif ($months === 6) {
                $multiplier = 0.8;
            } elseif ($months === 12) {
                $multiplier = 0.7;
            }
            $priceWithPeriodDiscount = round($totalRaw * $multiplier, 2);
        } elseif ($type === 'homepage') {
            $basePrice = 50.00;
            $totalRaw = $basePrice * $months;
            $multiplier = 1.0;
            if ($months === 3) {
                $multiplier = 0.9;
            } elseif ($months === 6) {
                $multiplier = 0.8;
            } elseif ($months === 12) {
                $multiplier = 0.7;
            }
            $priceWithPeriodDiscount = round($totalRaw * $multiplier, 2);
        } elseif ($type === 'stats') {
            $priceWithPeriodDiscount = 30.00;
        } else {
            $listing = Listing::where('user_id', $user->id)->find($listingId);
            if (!$listing) {
                return response()->json(['success' => false, 'message' => 'Объявление не найдено.'], 404);
            }

            // Determine base price based on property type
            $propertyType = in_array($listing->type, ['cottage', 'agrotourism']) ? 'cottage' : 'apartment';
            $tariff = Tariff::where('property_type', $propertyType)->first();
            if (!$tariff) {
                return response()->json(['success' => false, 'message' => 'Базовый тариф не найден.'], 500);
            }

            $basePrice = $tariff->price;
            $totalRaw = $months === 1 ? 0 : ($basePrice * $months);

            // Apply duration discounts
            $multiplier = 1.0;
            if ($months === 3) {
                $multiplier = 0.9;
            } elseif ($months === 6) {
                $multiplier = 0.8;
            } elseif ($months === 12) {
                $multiplier = 0.7;
            }
            $priceWithPeriodDiscount = round($totalRaw * $multiplier, 2);
        }

        // Fetch promo code
        $promocode = $this->getPromocodeByCode($code);
        if (!$promocode) {
            return response()->json(['success' => false, 'message' => 'Промокод не существует.'], 422);
        }

        // Restrict stats codes from being used for listings, and vice-versa
        if ($type === 'stats' && $promocode->type !== 'free_stats' && $promocode->code !== 'STATS100') {
            return response()->json(['success' => false, 'message' => 'Этот промокод не применим к детальной статистике.'], 422);
        }
        if ($type !== 'stats' && $promocode->type === 'free_stats') {
            return response()->json(['success' => false, 'message' => 'Этот промокод применим только для детальной статистики.'], 422);
        }

        // Restrict FREE100HOME code to homepage checkout
        if ($promocode->code === 'FREE100HOME' && $type !== 'homepage') {
            return response()->json(['success' => false, 'message' => 'Этот промокод применим только для тарифа Главная страница.'], 422);
        }

        if (!$promocode->isValidForUser($user, $priceWithPeriodDiscount)) {
            return response()->json(['success' => false, 'message' => 'Промокод неактивен, просрочен или не соответствует минимальной сумме.'], 422);
        }

        // Calculate discount
        $discount = 0.00;
        if ($promocode->type === 'percentage') {
            $discount = round($priceWithPeriodDiscount * ($promocode->value / 100), 2);
        } elseif ($promocode->type === 'fixed') {
            $discount = min($promocode->value, $priceWithPeriodDiscount);
        } elseif ($promocode->type === 'free' || $promocode->type === 'free_stats') {
            $discount = $priceWithPeriodDiscount;
        }

        $finalAmount = max(0.00, $priceWithPeriodDiscount - $discount);

        return response()->json([
            'success' => true,
            'code' => $promocode->code,
            'type' => $promocode->type,
            'value' => $promocode->value,
            'discount' => $discount,
            'final_amount' => $finalAmount,
            'description' => $promocode->description ?: 'Промокод успешно применен!'
        ]);
    }

    /**
     * Start the stats checkout session.
     */
    public function checkoutStats(Request $request)
    {
        $user = $this->ensureLoggedUser();
        if (!$user) {
            return redirect()->route('login')->with('warning', 'Для совершения оплаты требуется авторизация.');
        }

        if ($user->has_detailed_stats) {
            return redirect()->route('dashboard')->with('warning', 'Детальная статистика уже разблокирована.');
        }

        $basePrice = 30.00;

        // Apply promo code discount
        $promoCodeStr = trim($request->query('promo', ''));
        $promocode = null;
        $discount = 0.00;
        $finalAmount = $basePrice;

        if (!empty($promoCodeStr)) {
            $promocode = $this->getPromocodeByCode($promoCodeStr);
            if ($promocode && ($promocode->type === 'free_stats' || $promocode->code === 'STATS100') && $promocode->isValidForUser($user, $basePrice)) {
                if ($promocode->type === 'percentage') {
                    $discount = round($basePrice * ($promocode->value / 100), 2);
                } elseif ($promocode->type === 'fixed') {
                    $discount = min($promocode->value, $basePrice);
                } elseif ($promocode->type === 'free' || $promocode->type === 'free_stats') {
                    $discount = $basePrice;
                }
                $finalAmount = max(0.00, $basePrice - $discount);
            } else {
                return redirect()->route('dashboard')->with('error', 'Указанный промокод невалиден для детальной статистики.');
            }
        }

        // Case A: 100% discount / Free activation -> Bypass payment gateway completely
        if ($finalAmount <= 0.00) {
            $payment = Payment::create([
                'user_id' => $user->id,
                'listing_id' => null,
                'tariff_id' => Tariff::first()->id ?? 1,
                'amount' => 0.00,
                'currency' => 'BYN',
                'status' => 'pending',
                'payment_provider_id' => null,
                'checkout_token' => 'FREE_STATS_' . uniqid(),
                'duration_months' => 1,
                'promocode_id' => $promocode ? $promocode->id : null,
                'discount_amount' => $discount,
                'metadata' => ['type' => 'stats_checkout', 'promo' => $promoCodeStr]
            ]);

            $this->completePayment($payment, 'FREE_STATS_' . strtoupper(uniqid()));

            return redirect()->route('payments.success', ['test_payment_id' => $payment->id]);
        }

        // Case B: Requires credit card payment via bePaid gateway
        $payment = Payment::create([
            'user_id' => $user->id,
            'listing_id' => null,
            'tariff_id' => Tariff::first()->id ?? 1,
            'amount' => $finalAmount,
            'currency' => 'BYN',
            'status' => 'pending',
            'duration_months' => 1,
            'promocode_id' => $promocode ? $promocode->id : null,
            'discount_amount' => $discount,
            'metadata' => ['type' => 'stats_checkout', 'promo' => $promoCodeStr]
        ]);

        $successUrl = route('payments.success') . '?test_payment_id=' . $payment->id;
        $cancelUrl = route('payments.cancel');
        $notificationUrl = route('payments.webhook');

        $session = $this->bePaid->createCheckoutSession($payment, $successUrl, $cancelUrl, $notificationUrl);

        if ($session['success']) {
            $payment->checkout_token = $session['token'];
            $payment->save();

            return redirect($session['redirect_url']);
        }

        return redirect()->route('dashboard')->with('error', 'Не удалось инициировать оплату статистики: ' . ($session['message'] ?? 'ошибка платежной сессии'));
    }

    /**
     * Start the featured checkout session.
     */
    public function checkoutFeatured(Request $request, $id)
    {
        $user = $this->ensureLoggedUser();
        if (!$user) {
            return redirect()->route('login')->with('warning', 'Для совершения оплаты требуется авторизация.');
        }

        $listing = Listing::where('user_id', $user->id)->findOrFail($id);
        
        $months = intval($request->query('months', 1));
        if (!in_array($months, [1, 3, 6, 12])) {
            $months = 1;
        }

        $basePrice = 15.00;
        $totalRaw = $basePrice * $months;
        $multiplier = 1.0;
        if ($months === 3) {
            $multiplier = 0.9;
        } elseif ($months === 6) {
            $multiplier = 0.8;
        } elseif ($months === 12) {
            $multiplier = 0.7;
        }
        $priceWithPeriodDiscount = round($totalRaw * $multiplier, 2);

        // Apply promo code discount
        $promoCodeStr = trim($request->query('promo', ''));
        $promocode = null;
        $discount = 0.00;
        $finalAmount = $priceWithPeriodDiscount;

        if (!empty($promoCodeStr)) {
            $promocode = $this->getPromocodeByCode($promoCodeStr);
            if ($promocode && $promocode->type !== 'free_stats' && $promocode->isValidForUser($user, $priceWithPeriodDiscount)) {
                if ($promocode->type === 'percentage') {
                    $discount = round($priceWithPeriodDiscount * ($promocode->value / 100), 2);
                } elseif ($promocode->type === 'fixed') {
                    $discount = min($promocode->value, $priceWithPeriodDiscount);
                } elseif ($promocode->type === 'free') {
                    $discount = $priceWithPeriodDiscount;
                }
                $finalAmount = max(0.00, $priceWithPeriodDiscount - $discount);
            } else {
                return redirect()->route('dashboard')->with('error', 'Указанный промокод невалиден.');
            }
        }

        // Ensure promotion tariff exists
        $promoTariff = Tariff::find(3);
        if ($promoTariff && $promoTariff->price != 15.00) {
            $promoTariff->price = 15.00;
            $promoTariff->save();
        } elseif (!$promoTariff) {
            try {
                Tariff::create([
                    'id' => 3,
                    'name' => 'Тариф Продвижение',
                    'property_type' => 'featured',
                    'price' => 15.00,
                    'duration_days' => 30,
                ]);
            } catch (\Exception $e) {
                Log::warning('Promo tariff creation failed/already exists: ' . $e->getMessage());
            }
        }

        // Case A: 100% discount / Free activation -> Bypass payment gateway completely
        if ($finalAmount <= 0.00) {
            $payment = Payment::create([
                'user_id' => $user->id,
                'listing_id' => $listing->id,
                'tariff_id' => 3,
                'amount' => 0.00,
                'currency' => 'BYN',
                'status' => 'pending',
                'payment_provider_id' => null,
                'checkout_token' => 'FREE_FEATURED_' . uniqid(),
                'duration_months' => $months,
                'promocode_id' => $promocode ? $promocode->id : null,
                'discount_amount' => $discount,
                'metadata' => ['type' => 'featured_checkout', 'promo' => $promoCodeStr]
            ]);

            $this->completePayment($payment, 'FREE_FEAT_' . strtoupper(uniqid()));

            return redirect()->route('payments.success', ['test_payment_id' => $payment->id]);
        }

        // Case B: Requires credit card payment via bePaid gateway
        $payment = Payment::create([
            'user_id' => $user->id,
            'listing_id' => $listing->id,
            'tariff_id' => 3,
            'amount' => $finalAmount,
            'currency' => 'BYN',
            'status' => 'pending',
            'duration_months' => $months,
            'promocode_id' => $promocode ? $promocode->id : null,
            'discount_amount' => $discount,
            'metadata' => ['type' => 'featured_checkout', 'promo' => $promoCodeStr]
        ]);

        $successUrl = route('payments.success') . '?test_payment_id=' . $payment->id;
        $cancelUrl = route('payments.cancel');
        $notificationUrl = route('payments.webhook');

        $session = $this->bePaid->createCheckoutSession($payment, $successUrl, $cancelUrl, $notificationUrl);

        if ($session['success']) {
            $payment->checkout_token = $session['token'];
            $payment->save();

            return redirect($session['redirect_url']);
        }

        return redirect()->route('dashboard')->with('error', 'Не удалось инициировать оплату продвижения: ' . ($session['message'] ?? 'ошибка платежной сессии'));
    }

    /**
     * Start the checkout session.
     */
    public function checkout(Request $request, $listing_id)
    {
        $user = $this->ensureLoggedUser();
        if (!$user) {
            return redirect()->route('login')->with('warning', 'Для совершения оплаты требуется авторизация.');
        }

        $listing = Listing::where('user_id', $user->id)->findOrFail($listing_id);
        
        $months = intval($request->query('months', 1));
        if (!in_array($months, [1, 3, 6, 12])) {
            $months = 1;
        }

        // Fetch corresponding tariff
        $propertyType = in_array($listing->type, ['cottage', 'agrotourism']) ? 'cottage' : 'apartment';
        $tariff = Tariff::where('property_type', $propertyType)->first();
        if (!$tariff) {
            return redirect()->route('dashboard')->with('error', 'Ошибка: Базовый тариф не найден в базе данных.');
        }

        // Calculate basic duration pricing
        $basePrice = $tariff->price;
        $totalRaw = $months === 1 ? 0 : ($basePrice * $months);
        $multiplier = 1.0;
        if ($months === 3) {
            $multiplier = 0.9;
        } elseif ($months === 6) {
            $multiplier = 0.8;
        } elseif ($months === 12) {
            $multiplier = 0.7;
        }
        $priceWithPeriodDiscount = round($totalRaw * $multiplier, 2);

        // Apply promo code discount
        $promoCodeStr = trim($request->query('promo', ''));
        $promocode = null;
        $discount = 0.00;
        $finalAmount = $priceWithPeriodDiscount;

        if (!empty($promoCodeStr)) {
            $promocode = $this->getPromocodeByCode($promoCodeStr);
            if ($promocode && $promocode->isValidForUser($user, $priceWithPeriodDiscount)) {
                if ($promocode->type === 'percentage') {
                    $discount = round($priceWithPeriodDiscount * ($promocode->value / 100), 2);
                } elseif ($promocode->type === 'fixed') {
                    $discount = min($promocode->value, $priceWithPeriodDiscount);
                } elseif ($promocode->type === 'free') {
                    $discount = $priceWithPeriodDiscount;
                }
                $finalAmount = max(0.00, $priceWithPeriodDiscount - $discount);
            } else {
                return redirect()->route('dashboard')->with('error', 'Указанный промокод невалиден.');
            }
        }

        // Case A: 100% discount / Free activation -> Bypass payment gateway completely
        if ($finalAmount <= 0.00) {
            $payment = Payment::create([
                'user_id' => $user->id,
                'listing_id' => $listing->id,
                'tariff_id' => $tariff->id,
                'amount' => 0.00,
                'currency' => 'BYN',
                'status' => 'pending', // Will be completed by helper below
                'payment_provider_id' => null,
                'checkout_token' => 'FREE_TOKEN_' . uniqid(),
                'duration_months' => $months,
                'promocode_id' => $promocode ? $promocode->id : null,
                'discount_amount' => $discount,
                'metadata' => ['type' => 'free_activation', 'promo' => $promoCodeStr]
            ]);

            $this->completePayment($payment, 'FREE_' . strtoupper(uniqid()));

            return redirect()->route('payments.success', ['test_payment_id' => $payment->id]);
        }

        // Case B: Requires credit card payment via bePaid gateway
        $payment = Payment::create([
            'user_id' => $user->id,
            'listing_id' => $listing->id,
            'tariff_id' => $tariff->id,
            'amount' => $finalAmount,
            'currency' => 'BYN',
            'status' => 'pending',
            'duration_months' => $months,
            'promocode_id' => $promocode ? $promocode->id : null,
            'discount_amount' => $discount,
            'metadata' => ['type' => 'tariff_checkout', 'promo' => $promoCodeStr]
        ]);

        $successUrl = route('payments.success') . '?test_payment_id=' . $payment->id;
        $cancelUrl = route('payments.cancel');
        $notificationUrl = route('payments.webhook');

        $session = $this->bePaid->createCheckoutSession($payment, $successUrl, $cancelUrl, $notificationUrl);

        if ($session['success']) {
            $payment->checkout_token = $session['token'];
            $payment->save();

            return redirect($session['redirect_url']);
        }

        return redirect()->route('dashboard')->with('error', 'Не удалось инициировать оплату: ' . ($session['message'] ?? 'ошибка платежной сессии'));
    }

    /**
     * bePaid Server Notification Webhook (Server-to-Server)
     */
    public function webhook(Request $request)
    {
        // 1. Verify signatures
        if (!$this->bePaid->verifyWebhookSignature($request)) {
            return response()->json(['success' => false, 'message' => 'Неверная цифровая подпись вебхука.'], 401);
        }

        $transaction = $request->input('transaction');
        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Неполные данные транзакции.'], 400);
        }

        $paymentId = $transaction['tracking_id'] ?? null;
        $uid = $transaction['uid'] ?? null;
        $status = $transaction['status'] ?? null;

        if (!$paymentId) {
            return response()->json(['success' => false, 'message' => 'Tracking ID платежа не найден.'], 400);
        }

        $payment = Payment::find($paymentId);
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Платеж не найден в базе.'], 404);
        }

        if ($payment->status === 'completed') {
            return response()->json(['success' => true, 'message' => 'Платеж уже был успешно обработан ранее.']);
        }

        if ($status === 'successful') {
            $this->completePayment($payment, $uid);
            Log::info("bePaid Webhook: Payment ID " . $payment->id . " successfully processed for listing ID " . $payment->listing_id);
            return response()->json(['success' => true, 'message' => 'Платеж успешно проведен.']);
        }

        if (in_array($status, ['failed', 'declined', 'expired'])) {
            $payment->status = 'failed';
            $payment->save();
            Log::warning("bePaid Webhook: Payment ID " . $payment->id . " failed with status " . $status);
            return response()->json(['success' => true, 'message' => 'Транзакция отмечена как неудавшаяся.']);
        }

        return response()->json(['success' => true, 'message' => 'Платеж в неопределенном статусе, действие пропущено.']);
    }

    /**
     * Landing Success Page
     */
    public function success(Request $request)
    {
        $paymentId = $request->query('test_payment_id');
        $payment = null;

        if ($paymentId) {
            $payment = Payment::find($paymentId);

            if ($payment && $payment->status === 'pending') {
                // Scenario A: Simulation mode is active -> instantly complete
                if (filter_var(env('BEPAID_TEST_MODE', false), FILTER_VALIDATE_BOOLEAN)) {
                    $this->completePayment($payment, 'SIM_WEB_' . strtoupper(uniqid()));
                    Log::info("bePaid Simulation: Simulated checkout successful for payment ID: " . $payment->id);
                } 
                // Scenario B: Real mode, check bePaid API as fallback to webhook
                elseif ($payment->checkout_token) {
                    $sessionData = $this->bePaid->getCheckoutSession($payment->checkout_token);
                    if ($sessionData && isset($sessionData['checkout'])) {
                        $checkoutStatus = $sessionData['checkout']['status'] ?? '';
                        $gatewayStatus = $sessionData['checkout']['gateway_response']['status'] ?? '';
                        $uid = $sessionData['checkout']['gateway_response']['uid'] ?? null;
                        
                        if ($checkoutStatus === 'successful' || $gatewayStatus === 'successful') {
                            $this->completePayment($payment, $uid ?: ('CTP_' . strtoupper(uniqid())));
                            Log::info("bePaid Pull: Successfully completed payment ID: " . $payment->id . " via pull check.");
                        }
                    }
                }
            }
        }

        return view('payments.success', compact('payment'));
    }

    /**
     * Landing Cancel/Decline Page
     */
    public function cancel()
    {
        return view('payments.cancel');
    }

    /**
     * Fetch promocode by code and dynamically create STATS100 if it is missing from the database.
     */
    protected function getPromocodeByCode(string $code)
    {
        $code = trim($code);
        $promocode = Promocode::where('code', $code)->first();

        if (!$promocode && strtoupper($code) === 'STATS100') {
            try {
                $promocode = Promocode::create([
                    'code' => 'STATS100',
                    'type' => 'free_stats',
                    'value' => 100.00,
                    'max_uses_per_user' => 1,
                    'is_active' => true,
                    'description' => 'Бесплатная детальная статистика',
                ]);
            } catch (\Exception $e) {
                // Handle concurrent creation/race condition
                $promocode = Promocode::where('code', 'STATS100')->first();
            }
        }

        if (!$promocode && strtoupper($code) === 'PRODVIZH200') {
            try {
                $promocode = Promocode::create([
                    'code' => 'PRODVIZH200',
                    'type' => 'free',
                    'value' => 100.00,
                    'max_uses_per_user' => 1,
                    'is_active' => true,
                    'description' => 'Бесплатное продвижение объявления',
                ]);
            } catch (\Exception $e) {
                // Handle concurrent creation/race condition
                $promocode = Promocode::where('code', 'PRODVIZH200')->first();
            }
        }

        if (!$promocode && strtoupper($code) === 'FREE100HOME') {
            try {
                $promocode = Promocode::create([
                    'code' => 'FREE100HOME',
                    'type' => 'free',
                    'value' => 100.00,
                    'max_uses_per_user' => 1,
                    'is_active' => true,
                    'description' => 'Бесплатное размещение на Главной странице',
                ]);
            } catch (\Exception $e) {
                // Handle concurrent creation/race condition
                $promocode = Promocode::where('code', 'FREE100HOME')->first();
            }
        }

        return $promocode;
    }

    /**
     * Start the homepage checkout session.
     */
    public function checkoutHomepage(Request $request, $id)
    {
        $user = $this->ensureLoggedUser();
        if (!$user) {
            return redirect()->route('login')->with('warning', 'Для совершения оплаты требуется авторизация.');
        }

        $listing = Listing::where('user_id', $user->id)->findOrFail($id);
        
        // Limit active homepage listings to 10 (excluding current listing in case of extension)
        $activeHomepageCount = Listing::where('status', 'active')
            ->where('id', '!=', $id)
            ->whereHas('userTariffs', function($query) {
                $query->where('tariff_id', 4)
                      ->where('status', 'active')
                      ->where('expires_at', '>', now());
            })
            ->count();

        if ($activeHomepageCount >= 10) {
            return redirect()->route('dashboard')->with('error', 'Все места на главной странице заняты (максимум 10 объявлений). Пожалуйста, попробуйте позже.');
        }

        $months = intval($request->query('months', 1));
        if (!in_array($months, [1, 3, 6, 12])) {
            $months = 1;
        }

        $basePrice = 50.00;
        $totalRaw = $basePrice * $months;
        $multiplier = 1.0;
        if ($months === 3) {
            $multiplier = 0.9;
        } elseif ($months === 6) {
            $multiplier = 0.8;
        } elseif ($months === 12) {
            $multiplier = 0.7;
        }
        $priceWithPeriodDiscount = round($totalRaw * $multiplier, 2);

        // Apply promo code discount
        $promoCodeStr = trim($request->query('promo', ''));
        $promocode = null;
        $discount = 0.00;
        $finalAmount = $priceWithPeriodDiscount;

        if (!empty($promoCodeStr)) {
            $promocode = $this->getPromocodeByCode($promoCodeStr);
            if ($promocode && $promocode->type !== 'free_stats' && $promocode->isValidForUser($user, $priceWithPeriodDiscount)) {
                if ($promocode->type === 'percentage') {
                    $discount = round($priceWithPeriodDiscount * ($promocode->value / 100), 2);
                } elseif ($promocode->type === 'fixed') {
                    $discount = min($promocode->value, $priceWithPeriodDiscount);
                } elseif ($promocode->type === 'free') {
                    $discount = $priceWithPeriodDiscount;
                }
                $finalAmount = max(0.00, $priceWithPeriodDiscount - $discount);
            } else {
                return redirect()->route('dashboard')->with('error', 'Указанный промокод невалиден.');
            }
        }

        // Ensure homepage tariff exists in database (safety check)
        $homepageTariff = Tariff::find(4);
        if ($homepageTariff && $homepageTariff->price != 50.00) {
            $homepageTariff->price = 50.00;
            $homepageTariff->save();
        } elseif (!$homepageTariff) {
            try {
                Tariff::create([
                    'id' => 4,
                    'name' => 'Тариф Главная страница',
                    'property_type' => 'homepage',
                    'price' => 50.00,
                    'duration_days' => 30,
                ]);
            } catch (\Exception $e) {
                Log::warning('Homepage tariff creation failed/already exists: ' . $e->getMessage());
            }
        }

        // Case A: 100% discount / Free activation -> Bypass payment gateway completely
        if ($finalAmount <= 0.00) {
            $payment = Payment::create([
                'user_id' => $user->id,
                'listing_id' => $listing->id,
                'tariff_id' => 4,
                'amount' => 0.00,
                'currency' => 'BYN',
                'status' => 'pending',
                'payment_provider_id' => null,
                'checkout_token' => 'FREE_HOMEPAGE_' . uniqid(),
                'duration_months' => $months,
                'promocode_id' => $promocode ? $promocode->id : null,
                'discount_amount' => $discount,
                'metadata' => ['type' => 'homepage_checkout', 'promo' => $promoCodeStr]
            ]);

            $this->completePayment($payment, 'FREE_HOME_' . strtoupper(uniqid()));

            return redirect()->route('payments.success', ['test_payment_id' => $payment->id]);
        }

        // Case B: Requires credit card payment via bePaid gateway
        $payment = Payment::create([
            'user_id' => $user->id,
            'listing_id' => $listing->id,
            'tariff_id' => 4,
            'amount' => $finalAmount,
            'currency' => 'BYN',
            'status' => 'pending',
            'duration_months' => $months,
            'promocode_id' => $promocode ? $promocode->id : null,
            'discount_amount' => $discount,
            'metadata' => ['type' => 'homepage_checkout', 'promo' => $promoCodeStr]
        ]);

        $successUrl = route('payments.success') . '?test_payment_id=' . $payment->id;
        $cancelUrl = route('payments.cancel');
        $notificationUrl = route('payments.webhook');

        $session = $this->bePaid->createCheckoutSession($payment, $successUrl, $cancelUrl, $notificationUrl);

        if ($session['success']) {
            $payment->checkout_token = $session['token'];
            $payment->save();

            return redirect($session['redirect_url']);
        }

        return redirect()->route('dashboard')->with('error', 'Не удалось инициировать оплату тарифа Главная страница: ' . ($session['message'] ?? 'ошибка платежной сессии'));
    }

    private function sendTelegramAdminNotification(string $text)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $adminChatIdString = env('TELEGRAM_ADMIN_CHAT_ID');

        if (!$botToken || !$adminChatIdString) {
            return;
        }

        $chatIds = array_filter(array_map('trim', explode(',', $adminChatIdString)));
        foreach ($chatIds as $chatId) {
            try {
                \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to send Telegram admin notification: ' . $e->getMessage());
            }
        }
    }

    /**
     * Consolidate payment completion, subscription creation/extension, and listing publication.
     */
    protected function completePayment(Payment $payment, string $uid)
    {
        if ($payment->status === 'completed') {
            return;
        }

        try {
            DB::transaction(function () use ($payment, $uid) {
                $payment->status = 'completed';
                $payment->payment_provider_id = $uid;
                $payment->save();

                // Track promo usage if present
                if ($payment->promocode_id) {
                    $alreadyLogged = PromocodeUsage::where('payment_id', $payment->id)->exists();
                    if (!$alreadyLogged) {
                        PromocodeUsage::create([
                            'promocode_id' => $payment->promocode_id,
                            'user_id' => $payment->user_id,
                            'payment_id' => $payment->id,
                            'discount_amount' => $payment->discount_amount,
                            'used_at' => now()
                        ]);
                    }
                }

                // If stats checkout, unlock detailed stats and finish
                if (isset($payment->metadata['type']) && $payment->metadata['type'] === 'stats_checkout') {
                    $user = $payment->user;
                    if ($user) {
                        $user->has_detailed_stats = true;
                        $user->save();
                    }
                    return;
                }

                // If featured checkout, mark listing as featured, create/extend promotion tariff, and finish
                if (isset($payment->metadata['type']) && $payment->metadata['type'] === 'featured_checkout') {
                    $listing = $payment->listing;
                    if ($listing) {
                        $listing->featured = true;
                        if ($listing->status === 'pending_payment' || $listing->status === 'hidden') {
                            $listing->status = 'active';
                        }
                        $listing->save();

                        // Ensure promotion tariff exists
                        $promoTariff = Tariff::find(3);
                        if ($promoTariff && $promoTariff->price != 15.00) {
                            $promoTariff->price = 15.00;
                            $promoTariff->save();
                        } elseif (!$promoTariff) {
                            try {
                                Tariff::create([
                                    'id' => 3,
                                    'name' => 'Тариф Продвижение',
                                    'property_type' => 'featured',
                                    'price' => 15.00,
                                    'duration_days' => 30,
                                ]);
                            } catch (\Exception $e) {
                                Log::warning('Promo tariff creation failed/already exists: ' . $e->getMessage());
                            }
                        }

                        // Create or extend promotion tariff
                        $activePromo = UserTariff::where('listing_id', $payment->listing_id)
                            ->where('tariff_id', 3)
                            ->where('status', 'active')
                            ->where('expires_at', '>', now())
                            ->first();

                        $months = intval($payment->duration_months ?: 1);

                        if ($activePromo) {
                            $activePromo->expires_at = $activePromo->expires_at->addMonths($months);
                            $activePromo->save();
                        } else {
                            UserTariff::create([
                                'user_id' => $payment->user_id,
                                'listing_id' => $payment->listing_id,
                                'tariff_id' => 3,
                                'started_at' => now(),
                                'expires_at' => now()->addMonths($months),
                                'status' => 'active'
                            ]);
                        }
                    }
                    return;
                }

                // If homepage checkout, create/extend homepage tariff (tariff_id 4)
                if (isset($payment->metadata['type']) && $payment->metadata['type'] === 'homepage_checkout') {
                    $listing = $payment->listing;
                    if ($listing) {
                        if ($listing->status === 'pending_payment' || $listing->status === 'hidden') {
                            $listing->status = 'active';
                        }
                        $listing->save();

                        // Ensure homepage tariff exists
                        $homepageTariff = Tariff::find(4);
                        if ($homepageTariff && $homepageTariff->price != 50.00) {
                            $homepageTariff->price = 50.00;
                            $homepageTariff->save();
                        } elseif (!$homepageTariff) {
                            try {
                                Tariff::create([
                                    'id' => 4,
                                    'name' => 'Тариф Главная страница',
                                    'property_type' => 'homepage',
                                    'price' => 50.00,
                                    'duration_days' => 30,
                                ]);
                            } catch (\Exception $e) {
                                Log::warning('Homepage tariff creation failed/already exists: ' . $e->getMessage());
                            }
                        }

                        // Create or extend homepage tariff
                        $activePromo = UserTariff::where('listing_id', $payment->listing_id)
                            ->where('tariff_id', 4)
                            ->where('status', 'active')
                            ->where('expires_at', '>', now())
                            ->first();

                        $months = intval($payment->duration_months ?: 1);

                        if ($activePromo) {
                            $activePromo->expires_at = $activePromo->expires_at->addMonths($months);
                            $activePromo->save();
                        } else {
                            UserTariff::create([
                                'user_id' => $payment->user_id,
                                'listing_id' => $payment->listing_id,
                                'tariff_id' => 4,
                                'started_at' => now(),
                                'expires_at' => now()->addMonths($months),
                                'status' => 'active'
                            ]);
                        }

                        // Send Telegram Notification
                        $user = $payment->user;
                        $monthsText = $months . ' ';
                        if ($months % 10 === 1 && $months % 100 !== 11) {
                            $monthsText .= 'месяц';
                        } elseif (in_array($months % 10, [2, 3, 4]) && !in_array($months % 100, [12, 13, 14])) {
                            $monthsText .= 'месяца';
                        } else {
                            $monthsText .= 'месяцев';
                        }

                        $notificationText = "<b>🏠 Куплен тариф «Главная страница»!</b>\n\n"
                            . "<b>Пользователь:</b> " . htmlspecialchars($user ? $user->name : 'Неизвестно') . " (" . htmlspecialchars($user ? $user->email : '') . ")\n"
                            . "<b>Объявление:</b> <a href=\"" . route('listings.show', ['id' => $listing->id, 'slug' => $listing->slug]) . "\">" . htmlspecialchars($listing->title) . "</a>\n"
                            . "<b>Срок:</b> {$monthsText}\n"
                            . "<b>Сумма:</b> {$payment->amount} BYN" . ($payment->discount_amount > 0 ? " (Скидка: {$payment->discount_amount} BYN)" : "");

                        $this->sendTelegramAdminNotification($notificationText);
                    }
                    return;
                }


                // Create or extend listing active subscription
                $activeTariff = UserTariff::where('listing_id', $payment->listing_id)
                    ->where('status', 'active')
                    ->where('tariff_id', $payment->tariff_id)
                    ->where('expires_at', '>', now())
                    ->first();

                $months = intval($payment->duration_months ?: 1);

                if ($activeTariff) {
                    $activeTariff->expires_at = $activeTariff->expires_at->addMonths($months);
                    $activeTariff->save();
                } else {
                    UserTariff::create([
                        'user_id' => $payment->user_id,
                        'listing_id' => $payment->listing_id,
                        'tariff_id' => $payment->tariff_id,
                        'started_at' => now(),
                        'expires_at' => now()->addMonths($months),
                        'status' => 'active'
                    ]);
                }

                // Set listing to active status if it was pending payment or hidden
                $listing = $payment->listing;
                if ($listing && ($listing->status === 'pending_payment' || $listing->status === 'hidden')) {
                    $listing->status = 'active';
                    $listing->save();
                }
            });

            Log::info("Payment complete action: Listing #{$payment->listing_id} is now active/extended.");
        } catch (\Exception $e) {
            Log::error("Failed to complete payment ID {$payment->id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e;
        }
    }
}
