@extends('layouts.app')

@section('title', 'Оплата отменена или отклонена — Flatly.by')

@section('head')
<style>
.cancel-page-container {
    max-width: 600px;
    margin: 80px auto;
    padding: 20px;
    text-align: center;
}
.glass-cancel-card {
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
.cancel-icon-ring {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #FCEBEB;
    color: #A32D2D;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(163, 45, 45, 0.15);
}
.cancel-icon-ring svg {
    width: 40px;
    height: 40px;
    stroke: currentColor;
    stroke-width: 2.5;
    fill: none;
}
.cancel-title {
    font-size: 26px;
    font-weight: 800;
    color: var(--slate-900);
}
.cancel-desc {
    font-size: 15px;
    color: var(--gray-text);
    line-height: 1.6;
}
.troubleshoot-box {
    width: 100%;
    background: var(--slate-50);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 20px;
    text-align: left;
    margin: 10px 0;
}
.troubleshoot-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--slate-900);
    margin-bottom: 8px;
}
.troubleshoot-list {
    margin: 0;
    padding-left: 20px;
    font-size: 13px;
    color: var(--gray-text);
    line-height: 1.7;
}
.action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 10px;
}
.dashboard-btn {
    background: var(--amber);
    color: var(--slate-900);
    border: none;
    padding: 14px 30px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.15s ease;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.2);
}
.dashboard-btn:hover {
    background: var(--amber-dark);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.3);
}
.retry-btn {
    background: var(--slate-900);
    color: #fff;
    border: none;
    padding: 14px 30px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.15s ease;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.15);
}
.retry-btn:hover {
    background: #252538;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.25);
}

/* ===== RESPONSIVE MEDIA QUERIES ===== */
@media (max-width: 576px) {
    .cancel-page-container {
        margin: 24px auto;
        padding: 12px;
    }
    .glass-cancel-card {
        padding: 32px 16px;
        border-radius: 18px;
        gap: 16px;
    }
    .cancel-title {
        font-size: 20px;
    }
    .cancel-desc {
        font-size: 13.5px;
    }
    .troubleshoot-box {
        padding: 14px;
    }
    .troubleshoot-list {
        padding-left: 16px;
        font-size: 12.5px;
    }
    .action-buttons {
        flex-direction: column;
        width: 100%;
        gap: 10px;
    }
    .dashboard-btn, .retry-btn {
        width: 100%;
        text-align: center;
        padding: 12px 16px;
    }
}
</style>
@endsection

@section('content')
<div class="cancel-page-container">
    <div class="glass-cancel-card">
        <div class="cancel-icon-ring">
            <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </div>

        <h1 class="cancel-title">Оплата не завершена</h1>

        <p class="cancel-desc">
            Процесс оплаты был прерван, отклонен банком или отменен вами. Средства с вашей карты не были списаны, и подписка на объявление не была активирована.
        </p>

        <div class="troubleshoot-box">
            <div class="troubleshoot-title">Возможные причины:</div>
            <ul class="troubleshoot-list">
                <li>Недостаточно средств на банковской карте.</li>
                <li>Ошибка ввода реквизитов (номер карты, срок действия, CVC).</li>
                <li>Отклонение транзакции банком-эмитентом из-за лимитов на онлайн-платежи.</li>
                <li>Вы самостоятельно отменили операцию на защищенной платежной странице.</li>
            </ul>
        </div>

        <div class="action-buttons">
            <a href="{{ route('dashboard') }}" class="retry-btn">Вернуться в кабинет</a>
            <a href="javascript:history.back()" class="dashboard-btn">Попробовать снова</a>
        </div>
    </div>
</div>
@endsection
