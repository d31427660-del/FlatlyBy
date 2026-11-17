@extends('layouts.app')

@section('title', 'Как работает сервис — Flatly.by')

@section('head')
<style>
.hiw-container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 40px 24px 80px;
  color: var(--slate-900);
}
.hiw-header {
  text-align: center;
  margin-bottom: 56px;
}
.hiw-header h1 {
  font-family: 'Syne', sans-serif;
  font-size: 38px;
  font-weight: 800;
  color: var(--slate-900);
  margin-bottom: 16px;
  letter-spacing: -0.5px;
}
.hiw-header p {
  font-size: 17px;
  color: var(--gray-text);
  max-width: 650px;
  margin: 0 auto;
  line-height: 1.6;
}
.hiw-badge {
  display: inline-block;
  background: hsla(38, 92%, 50%, 0.1);
  color: #D97706;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  padding: 6px 16px;
  border-radius: 99px;
  margin-bottom: 16px;
}
.hiw-tabs {
  display: flex;
  justify-content: center;
  gap: 16px;
  margin-bottom: 48px;
}
.hiw-tab-btn {
  background: var(--white);
  border: 1px solid var(--border);
  padding: 14px 28px;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 700;
  color: var(--slate-700);
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 10px;
}
.hiw-tab-btn:hover {
  background: var(--slate-50);
  color: var(--slate-900);
}
.hiw-tab-btn.active {
  background: var(--slate-900);
  color: var(--white);
  border-color: var(--slate-900);
  box-shadow: 0 4px 12px rgba(26,26,46,0.15);
}
.hiw-section {
  display: none;
}
.hiw-section.active {
  display: block;
}
.steps-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  margin-bottom: 56px;
}
.step-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 32px;
  position: relative;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  box-shadow: 0 4px 20px rgba(0,0,0,0.01);
}
.step-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 30px rgba(0,0,0,0.03);
}
.step-num {
  position: absolute;
  top: -16px;
  left: 32px;
  background: var(--amber);
  color: var(--slate-900);
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  font-weight: 800;
  box-shadow: 0 4px 10px rgba(245, 166, 35, 0.3);
}
.step-icon {
  margin-bottom: 24px;
  color: var(--slate-900);
}
.step-card h3 {
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 12px;
  color: var(--slate-900);
}
.step-card p {
  font-size: 14px;
  line-height: 1.7;
  color: #4B5563;
  margin-bottom: 0;
}
.no-commission-banner {
  background: linear-gradient(135deg, #1E1B4B 0%, #0F172A 100%);
  border-radius: 20px;
  padding: 48px;
  color: var(--white);
  display: flex;
  align-items: center;
  gap: 40px;
  margin-bottom: 48px;
  position: relative;
  overflow: hidden;
}
.no-commission-banner::before {
  content: "";
  position: absolute;
  top: -50%;
  right: -20%;
  width: 300px;
  height: 300px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(245, 166, 35, 0.1) 0%, transparent 70%);
}
.nc-content {
  flex: 1;
}
.nc-content h2 {
  font-size: 26px;
  font-weight: 800;
  margin-bottom: 16px;
  color: var(--white);
}
.nc-content p {
  font-size: 15px;
  line-height: 1.8;
  color: rgba(255,255,255,0.8);
  margin-bottom: 0;
}
.nc-illustration {
  width: 140px;
  height: 140px;
  flex-shrink: 0;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.nc-illustration svg {
  color: var(--amber);
}

@media (max-width: 900px) {
  .steps-grid {
    grid-template-columns: 1fr;
    gap: 32px;
  }
  .no-commission-banner {
    flex-direction: column;
    padding: 32px;
    text-align: center;
  }
  .nc-illustration {
    margin-top: 16px;
  }
}
</style>
@endsection

@section('content')
<div class="hiw-container">
  
  <header class="hiw-header">
    <span class="hiw-badge">Без посредников</span>
    <h1>Как работает Flatly</h1>
    <p>Flatly.by — это современный каталог недвижимости в Беларуси. Мы связываем гостей и арендодателей напрямую без наценок и скрытых комиссий.</p>
  </header>

  <!-- Switch Tabs -->
  <div class="hiw-tabs">
    <button class="hiw-tab-btn active" onclick="switchTab('guest')">
      <x-solar-user-linear width="18" height="18" />
      Для гостей
    </button>
    <button class="hiw-tab-btn" onclick="switchTab('host')">
      <x-solar-home-2-linear width="18" height="18" />
      Для хозяев
    </button>
  </div>

  <!-- Guest Section -->
  <div id="section-guest" class="hiw-section active">
    <div class="steps-grid">
      
      <!-- Step 1 -->
      <div class="step-card">
        <span class="step-num">1</span>
        <div class="step-icon">
          <x-solar-magnifer-linear width="32" height="32" />
        </div>
        <h3>Найдите жильё</h3>
        <p>Используйте удобный поиск и систему фильтров на главной странице. Выбирайте город, количество комнат, спальные места, цену, наличие джакузи, бани или бассейна.</p>
      </div>

      <!-- Step 2 -->
      <div class="step-card">
        <span class="step-num">2</span>
        <div class="step-icon">
          <x-solar-book-linear width="32" height="32" />
        </div>
        <h3>Смотрите детали</h3>
        <p>Изучите подробное описание квартиры или коттеджа, точную карту расположения, реальные фотографии, правила заезда и список доступных удобств.</p>
      </div>

      <!-- Step 3 -->
      <div class="step-card">
        <span class="step-num">3</span>
        <div class="step-icon">
          <x-solar-phone-calling-linear width="32" height="32" />
        </div>
        <h3>Свяжитесь напрямую</h3>
        <p>Нажмите кнопку показа контактов, чтобы увидеть телефон хозяина, или напишите ему в мессенджеры (Telegram, Viber) прямо с нашего сайта. Никаких броней через посредников!</p>
      </div>

    </div>
  </div>

  <!-- Host Section -->
  <div id="section-host" class="hiw-section">
    <div class="steps-grid">
      
      <!-- Step 1 -->
      <div class="step-card">
        <span class="step-num">1</span>
        <div class="step-icon">
          <x-solar-user-plus-linear width="32" height="32" />
        </div>
        <h3>Зарегистрируйтесь</h3>
        <p>Создайте аккаунт арендодателя на сайте за несколько секунд. Это позволит вам размещать свои объекты недвижимости и управлять ими в Личном кабинете.</p>
      </div>

      <!-- Step 2 -->
      <div class="step-card">
        <span class="step-num">2</span>
        <div class="step-icon">
          <x-solar-pen-2-linear width="32" height="32" />
        </div>
        <h3>Заполните форму</h3>
        <p>Добавьте адрес, загрузите качественные фотографии, укажите цену за сутки, количество комнат и другие характеристики вашего объекта.</p>
      </div>

      <!-- Step 3 -->
      <div class="step-card">
        <span class="step-num">3</span>
        <div class="step-icon">
          <x-solar-chat-round-line-linear width="32" height="32" />
        </div>
        <h3>Принимайте звонки</h3>
        <p>Ваше объявление нужно активировать в личном кабинете, чтобы оно мгновенно появилось в каталоге. Гости будут видеть ваши прямые контактные данные и звонить вам напрямую без комиссий.</p>
      </div>

    </div>
  </div>

  <!-- Zero Commission Explanation -->
  <div class="no-commission-banner">
    <div class="nc-content">
      <h2>Почему Flatly работает без комиссий?</h2>
      <p>Обычные сервисы бронирования удерживают от 15% до 25% комиссии с каждого заказа, что вынуждает хозяев завышать цены. Flatly.by работает по принципу доски объявлений. Мы не участвуем в процессе оплаты и не берем комиссию со сделок. Вы платите только за дополнительные платные услуги (поднятие в топ, выделение цветом), если хотите ускорить поиск гостей. Для гостей сервис абсолютно бесплатен.</p>
    </div>
    <div class="nc-illustration">
      <x-solar-wallet-linear width="64" height="64" />
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
function switchTab(role) {
  // Toggle buttons
  document.querySelectorAll('.hiw-tab-btn').forEach(btn => {
    btn.classList.remove('active');
  });
  const activeBtn = document.querySelector(`.hiw-tab-btn[onclick*="${role}"]`);
  if (activeBtn) activeBtn.classList.add('active');

  // Toggle sections
  document.querySelectorAll('.hiw-section').forEach(sec => {
    sec.classList.remove('active');
  });
  const activeSec = document.getElementById(`section-${role}`);
  if (activeSec) activeSec.classList.add('active');
}
</script>
@endsection
