@extends('layouts.app')

@section('title', 'Правила размещения объявлений — Flatly.by')

@section('head')
<style>
.rules-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 40px 24px 80px;
  color: var(--slate-900);
}
.rules-header {
  text-align: center;
  margin-bottom: 48px;
}
.rules-header h1 {
  font-family: 'Syne', sans-serif;
  font-size: 36px;
  font-weight: 800;
  color: var(--slate-900);
  margin-bottom: 12px;
  letter-spacing: -0.5px;
}
.rules-header p {
  font-size: 16px;
  color: var(--gray-text);
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
}
.rules-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 40px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
  margin-bottom: 32px;
}
.rules-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 28px;
}
.rules-section-title {
  font-size: 20px;
  font-weight: 700;
  color: var(--slate-900);
  margin-bottom: 16px;
  display: flex;
  align-items: center;
  gap: 12px;
}
.rules-section-title svg {
  color: var(--amber);
  flex-shrink: 0;
}
.rules-card p {
  font-size: 14.5px;
  line-height: 1.8;
  color: #33334B;
  margin-bottom: 16px;
}
.rules-list {
  list-style: none;
  padding-left: 0;
  margin-bottom: 24px;
}
.rules-list li {
  font-size: 14px;
  line-height: 1.8;
  color: #33334B;
  margin-bottom: 12px;
  position: relative;
  padding-left: 28px;
}
.rules-list li::before {
  content: "✓";
  position: absolute;
  left: 0;
  top: 0;
  color: #10B981;
  font-weight: 700;
  font-size: 16px;
}
.rules-list.prohibited li::before {
  content: "×";
  color: #EF4444;
  font-size: 20px;
  top: -2px;
}
.info-banner {
  background: hsla(210, 100%, 96%, 1);
  border: 1px solid hsla(210, 100%, 80%, 1);
  color: hsla(210, 100%, 25%, 1);
  border-radius: 12px;
  padding: 20px;
  display: flex;
  gap: 16px;
  margin: 32px 0;
}
.info-banner-icon {
  font-size: 24px;
  line-height: 1;
}
.info-banner-content h4 {
  font-size: 15px;
  font-weight: 700;
  margin-bottom: 6px;
}
.info-banner-content p {
  font-size: 13.5px;
  line-height: 1.6;
  margin-bottom: 0;
  color: hsla(210, 100%, 30%, 1);
}
.action-box {
  text-align: center;
  background: var(--slate-900);
  color: var(--white);
  border-radius: 16px;
  padding: 40px;
  box-shadow: 0 8px 30px rgba(26,26,46,0.15);
}
.action-box h2 {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 12px;
  color: var(--white);
}
.action-box p {
  font-size: 14.5px;
  color: rgba(255,255,255,0.7);
  margin-bottom: 24px;
}
.action-btn {
  display: inline-block;
  background: var(--white);
  color: var(--slate-900);
  font-weight: 700;
  padding: 14px 28px;
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(255,255,255,0.1);
}
.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255,255,255,0.2);
  background: var(--slate-50);
}

@media (max-width: 768px) {
  .rules-container {
    padding: 24px 16px;
  }
  .rules-header h1 {
    font-size: 28px;
  }
  .rules-card {
    padding: 24px;
  }
}
</style>
@endsection

@section('content')
<div class="rules-container">
  
  <header class="rules-header">
    <h1>Правила размещения</h1>
    <p>Мы заботимся о качестве объявлений на Flatly.by. Ознакомьтесь с правилами, чтобы ваши объявления привлекали больше клиентов и соответствовали стандартам платформы.</p>
  </header>

  <div class="rules-grid">
    
    <!-- 1. Требования к объявлениям -->
    <section class="rules-card">
      <h2 class="rules-section-title">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        1. Основные требования к объявлениям
      </h2>
      <p>Каждое объявление на Flatly.by должно содержать исключительно достоверную и актуальную информацию о сдаваемом объекте:</p>
      <ul class="rules-list">
        <li><strong>Реальные фотографии:</strong> Фотографии должны соответствовать конкретному объекту недвижимости. Использование чужих фото, 3D-визуализаций или картинок из интернета запрещено.</li>
        <li><strong>Точный адрес:</strong> Указывайте фактический адрес объекта (город, улица, дом). Встроенная карта должна корректно отображать геопозицию.</li>
        <li><strong>Актуальные цены:</strong> Указывайте реальную стоимость проживания за сутки (или за час при почасовой аренде). Изменение цены в зависимости от дней недели или количества гостей должно быть четко описано в описании.</li>
        <li><strong>Достоверное описание:</strong> Описывайте фактическое состояние жилья, количество спальных мест, наличие бытовой техники, интернета и дополнительных услуг.</li>
      </ul>
    </section>

    <!-- 2. Запрещенные действия -->
    <section class="rules-card">
      <h2 class="rules-section-title">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        2. Запрещенные действия и спам
      </h2>
      <p>Для поддержания честной конкуренции и удобства поиска на платформе строго запрещается:</p>
      <ul class="rules-list prohibited">
        <li><strong>Дублирование объявлений:</strong> Создание нескольких одинаковых объявлений для одного и того же объекта недвижимости (в том числе под разными аккаунтами).</li>
        <li><strong>Фотографии с логотипами:</strong> Размещение снимков с водяными знаками или логотипами сторонних сайтов и каталогов недвижимости.</li>
        <li><strong>Несуществующие контакты:</strong> Указание чужих или некорректных номеров телефонов, а также ссылок на сторонние ресурсы в тексте объявлений.</li>
        <li><strong>Размещение без прав аренды:</strong> Размещение объектов лицами, не имеющими законного права сдавать данную недвижимость в аренду.</li>
      </ul>
    </section>

    <!-- Информационный баннер о публикации -->
    <div class="info-banner">
      <span class="info-banner-icon">⚡</span>
      <div class="info-banner-content">
        <h4>Как публикуются объявления?</h4>
        <p>Все новые и отредактированные объявления публикуются в каталоге мгновенно и без задержек. Однако администрация сайта оставляет за собой право выборочной проверки объявлений после их публикации на предмет соответствия правилам и может временно скрыть объявление до устранения нарушений.</p>
      </div>
    </div>

    <!-- 3. Обязанности собственника -->
    <section class="rules-card">
      <h2 class="rules-section-title">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        3. Ответственность и требования закона
      </h2>
      <p>Арендодатели на Flatly.by обязаны соблюдать действующее законодательство Республики Беларусь:</p>
      <ul class="rules-list">
        <li><strong>Учетный номер плательщика (УНП):</strong> Индивидуальные предприниматели, самозанятые лица и юридические лица обязаны указать свой УНП в настройках Личного кабинета для верификации.</li>
        <li><strong>Налоги:</strong> Сдача жилья в посуточную аренду облагается налогом. Вы несете полную ответственность за своевременную уплату налогов и подачу отчетности в соответствии с законодательством Республики Беларусь.</li>
        <li><strong>Безопасность гостей:</strong> Вы обязуетесь содержать жильё в надлежащем санитарном и техническом состоянии, обеспечивая пожарную и экологическую безопасность.</li>
      </ul>
    </section>

    <!-- Призыв к действию -->
    <section class="action-box">
      <h2>Готовы сдать жильё?</h2>
      <p>Разместите объявление прямо сейчас. Это абсолютно бесплатно, без посредников и комиссий!</p>
      <a href="{{ route('listings.create') }}" class="action-btn">Разместить объявление</a>
    </section>

  </div>
</div>
@endsection
