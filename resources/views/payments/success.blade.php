@extends('layouts.app')

@section('title', 'Оплата успешно завершена — Flatly.by')

@section('head')
<style>
.success-page-container {
    max-width: 600px;
    margin: 80px auto;
    padding: 20px;
    text-align: center;
}
.glass-success-card {
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.5);
    border-radius: 24px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    padding: 50px 40px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 24px;
}
.success-icon-ring {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--amber-pale);
    color: var(--amber-dark);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(245, 158, 11, 0.15);
    animation: pulseIcon 2s infinite;
}
.success-icon-ring svg {
    width: 40px;
    height: 40px;
    stroke: currentColor;
    stroke-width: 3;
    fill: none;
}
.success-title {
    font-size: 26px;
    font-weight: 800;
    color: var(--slate-900);
}
.success-desc {
    font-size: 15px;
    color: var(--gray-text);
    line-height: 1.6;
}
.payment-details {
    width: 100%;
    background: var(--slate-50);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    text-align: left;
    margin: 10px 0;
}
.detail-item {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
}
.detail-label {
    color: var(--gray-text);
    font-weight: 500;
}
.detail-value {
    color: var(--slate-900);
    font-weight: 700;
}
.redirect-countdown {
    font-size: 13px;
    color: var(--gray-text);
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 10px;
}
.dashboard-btn {
    background: var(--amber);
    color: var(--slate-900);
    border: none;
    padding: 14px 36px;
    border-radius: 30px;
    font-size: 15px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.15s ease;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.2);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.dashboard-btn:hover {
    background: var(--amber-dark);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.3);
}

@keyframes pulseIcon {
    0% { transform: scale(1); box-shadow: 0 8px 24px rgba(245, 158, 11, 0.15); }
    50% { transform: scale(1.05); box-shadow: 0 8px 32px rgba(245, 158, 11, 0.3); }
    100% { transform: scale(1); box-shadow: 0 8px 24px rgba(245, 158, 11, 0.15); }
}

/* ===== RESPONSIVE MEDIA QUERIES ===== */
@media (max-width: 576px) {
    .success-page-container {
        margin: 24px auto;
        padding: 12px;
    }
    .glass-success-card {
        padding: 32px 16px;
        border-radius: 18px;
        gap: 16px;
    }
    .success-title {
        font-size: 20px;
    }
    .success-desc {
        font-size: 13.5px;
    }
    .payment-details {
        padding: 14px;
        gap: 10px;
    }
    .detail-item {
        flex-direction: column;
        gap: 2px;
    }
    .detail-value {
        font-size: 13.5px;
    }
    .dashboard-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection

@section('content')
<div class="success-page-container">
    <div class="glass-success-card">
        <div class="success-icon-ring" style="@if($payment && $payment->status === 'pending') background: var(--slate-100); color: var(--gray-text); @endif">
            @if($payment && $payment->status === 'pending')
                <!-- Processing Spinner Icon -->
                <svg class="spinner" viewBox="0 0 50 50" style="width: 40px; height: 40px; animation: spin 1s linear infinite;">
                    <circle cx="25" cy="25" r="20" fill="none" stroke="currentColor" stroke-width="5" stroke-dasharray="80 200" stroke-linecap="round"></circle>
                </svg>
            @else
                <x-solar-check-circle-linear />
            @endif
        </div>

        <h1 class="success-title">
            @if(!$payment)
                Оплата не найдена
            @elseif($payment->status === 'pending')
                Ожидаем подтверждения оплаты...
            @elseif($payment->status === 'failed')
                Ошибка оплаты
            @elseif($payment->amount <= 0)
                Размещение активировано!
            @else
                Оплата успешно завершена!
            @endif
        </h1>

        <p class="success-desc">
            @if(!$payment)
                Информация о платеже отсутствует. Пожалуйста, вернитесь в личный кабинет.
            @elseif($payment->status === 'pending')
                Мы ожидаем подтверждения от платежной системы. Как только оплата будет подтверждена, ваша услуга автоматически активируется. Пожалуйста, подождите или обновите страницу.
            @elseif($payment->status === 'failed')
                Произошла ошибка при проведении транзакции. Пожалуйста, попробуйте еще раз.
            @elseif($payment->listing)
                Ваша подписка на объявление <strong>«{{ $payment->listing->title }}»</strong> успешно создана и активирована. Объявление теперь показывается гостям на платформе.
            @else
                Ваша транзакция была успешно проведена и подтверждена платежной системой. Подписка на объект аренды успешно активирована.
            @endif
        </p>

        @if($payment)
        <div class="payment-details">
            @if($payment->listing)
            <div class="detail-item">
                <span class="detail-label">Объект размещения:</span>
                <span class="detail-value">{{ $payment->listing->title }}</span>
            </div>
            @endif
            <div class="detail-item">
                <span class="detail-label">Срок действия:</span>
                <span class="detail-value">{{ $payment->duration_months }} {{ trans_choice('месяц|месяца|месяцев', $payment->duration_months, [], 'ru') }}</span>
            </div>
            @if($payment->listing && $payment->listing->expires_at && $payment->status === 'completed')
            <div class="detail-item">
                <span class="detail-label">Подписка активна до:</span>
                <span class="detail-value" style="color: #3B6D11;">{{ $payment->listing->expires_at }}</span>
            </div>
            @endif
            <div class="detail-item">
                <span class="detail-label">Сумма оплаты:</span>
                <span class="detail-value">{{ number_format($payment->amount, 2, '.', '') }} BYN</span>
            </div>
            @if($payment->discount_amount > 0)
            <div class="detail-item">
                <span class="detail-label">Скидка по промокоду:</span>
                <span class="detail-value" style="color: #A32D2D;">-{{ number_format($payment->discount_amount, 2, '.', '') }} BYN</span>
            </div>
            @endif
            <div class="detail-item">
                <span class="detail-label">Статус платежа:</span>
                <span class="detail-value" style="color: @if($payment->status === 'completed') #3B6D11 @elseif($payment->status === 'failed') #A32D2D @else #E65100 @endif;">
                    @if($payment->status === 'completed') Успешно @elseif($payment->status === 'failed') Ошибка @else Обрабатывается @endif
                </span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Номер транзакции:</span>
                <span class="detail-value" style="font-family: monospace; font-size: 12px;">{{ $payment->payment_provider_id ?: 'Ожидание...' }}</span>
            </div>
        </div>
        @endif

        @if($payment && $payment->status === 'pending')
        <div style="display: flex; gap: 12px; width: 100%;">
            <button onclick="window.location.reload()" class="dashboard-btn" style="flex: 1; background: var(--slate-900); color: #fff;">
                Обновить статус 🔄
            </button>
            <a href="{{ route('dashboard') }}" class="dashboard-btn" style="flex: 1; justify-content: center;">
                В кабинет
            </a>
        </div>
        @else
        <a href="{{ route('dashboard') }}" class="dashboard-btn">
            Перейти в кабинет
            <x-solar-arrow-right-linear style="width: 16px; height: 16px; display:inline-block; vertical-align:middle;" />
        </a>
        @endif

        @if($payment && $payment->status === 'completed')
        <div class="redirect-countdown">
            <span id="countdown-sec">5</span> сек. до автоматического перехода в личный кабинет...
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
<script>
@if($payment && $payment->status === 'completed')
let seconds = 5;
const countdownEl = document.getElementById('countdown-sec');

const interval = setInterval(() => {
    seconds--;
    if (countdownEl) {
        countdownEl.textContent = seconds;
    }
    if (seconds <= 0) {
        clearInterval(interval);
        window.location.href = "{{ route('dashboard') }}";
    }
}, 1000);
@elseif($payment && $payment->status === 'pending')
// Auto-refresh the page to verify status with pulling bePaid
let checkCount = 0;
const maxChecks = 5;
const checkInterval = setInterval(() => {
    checkCount++;
    if (checkCount >= maxChecks) {
        clearInterval(checkInterval);
    } else {
        window.location.reload();
    }
}, 3000);
@endif
</script>
@endsection
