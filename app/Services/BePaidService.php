<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class BePaidService
{
    protected string $apiUrl;
    protected string $checkoutUrl;
    protected string $shopId;
    protected string $secretKey;
    protected string $publicKey;
    protected bool $testMode;

    public function __construct()
    {
        $this->apiUrl = env('BEPAID_API_URL', 'https://api.bepaid.by');
        $this->checkoutUrl = env('BEPAID_CHECKOUT_URL', 'https://checkout.bepaid.by');
        $this->shopId = env('BEPAID_SHOP_ID', '');
        $this->secretKey = env('BEPAID_SECRET_KEY', '');
        $this->publicKey = env('BEPAID_PUBLIC_KEY', '');
        $this->testMode = filter_var(env('BEPAID_TEST_MODE', false), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Create a checkout session (payment token) with bePaid.
     */
    public function createCheckoutSession(Payment $payment, string $successUrl, string $cancelUrl, string $notificationUrl): array
    {
        // Simulator mode: if credentials are blank
        if (empty($this->shopId) || empty($this->secretKey)) {
            Log::info("bePaid: Operating in Simulator/Test Mode (No credentials) for payment ID: " . $payment->id);
            return [
                'success' => true,
                'token' => 'TEST_TOKEN_' . uniqid(),
                'redirect_url' => route('payments.success') . '?test_payment_id=' . $payment->id
            ];
        }

        try {
            $amountInCents = (int) round($payment->amount * 100);

            $payload = [
                'checkout' => [
                    'transaction_type' => 'payment',
                    'attempts' => 1,
                    'test' => $this->testMode,
                    'order' => [
                        'amount' => $amountInCents,
                        'currency' => $payment->currency ?: 'BYN',
                        'description' => $payment->listing_id 
                            ? ('Тариф для объявления #' . $payment->listing_id . ' (' . $payment->duration_months . ' мес.)') 
                            : 'Разблокировка детальной статистики навсегда',
                        'tracking_id' => (string) $payment->id
                    ],
                    'settings' => [
                        'success_url' => $successUrl,
                        'cancel_url' => $cancelUrl,
                        'decline_url' => $cancelUrl,
                        'fail_url' => $cancelUrl,
                        'notification_url' => $notificationUrl
                    ]
                ]
            ];

            $response = Http::withBasicAuth($this->shopId, $this->secretKey)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'X-API-Version' => '2'
                ])
                ->post($this->checkoutUrl . '/ctp/api/checkouts', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'token' => $data['checkout']['token'] ?? null,
                    'redirect_url' => $data['checkout']['redirect_url'] ?? null
                ];
            }

            Log::error('bePaid API checkout error response: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Ошибка создания платежной сессии bePaid: ' . ($response->json('message') ?? 'Неизвестная ошибка')
            ];

        } catch (\Exception $e) {
            Log::error('bePaid exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Исключение платежного сервиса: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify the RSA signature of the webhook request.
     */
    public function verifyWebhookSignature(Request $request): bool
    {
        if ($this->testMode || empty($this->publicKey)) {
            return true; // Bypassed in simulator mode
        }

        $signature = $request->header('Content-Signature');
        if (empty($signature)) {
            Log::warning('bePaid webhook missing Content-Signature header');
            return false;
        }

        $publicKeyPem = $this->publicKey;
        if (!str_contains($publicKeyPem, '-----BEGIN PUBLIC KEY-----')) {
            $publicKeyPem = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($publicKeyPem, 64, "\n", true) . "\n-----END PUBLIC KEY-----";
        }

        $rawBody = $request->getContent();
        $signatureBytes = base64_decode($signature);

        $result = openssl_verify(
            $rawBody,
            $signatureBytes,
            $publicKeyPem,
            OPENSSL_ALGO_SHA256
        );

        if ($result === 1) {
            return true;
        }

        Log::warning('bePaid webhook signature verification failed');
        return false;
    }

    /**
     * Retrieve a checkout session details from bePaid.
     */
    public function getCheckoutSession(string $token): ?array
    {
        if (empty($this->shopId) || empty($this->secretKey)) {
            Log::warning('bePaid: Shop ID or Secret Key is empty, cannot query checkout session.');
            return null;
        }

        try {
            $response = Http::withBasicAuth($this->shopId, $this->secretKey)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'X-API-Version' => '2'
                ])
                ->get($this->checkoutUrl . '/ctp/api/checkouts/' . $token);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('bePaid API retrieve checkout error response: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('bePaid API retrieve checkout exception: ' . $e->getMessage());
            return null;
        }
    }
}
