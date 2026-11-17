@extends('layouts.app')

@section('title', 'Контакты и поддержка — Flatly.by')

@section('head')
<style>
.contacts-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 40px 24px 80px;
  color: var(--slate-900);
}
.contacts-header {
  text-align: center;
  margin-bottom: 48px;
}
.contacts-header h1 {
  font-family: 'Syne', sans-serif;
  font-size: 36px;
  font-weight: 800;
  color: var(--slate-900);
  margin-bottom: 12px;
  letter-spacing: -0.5px;
}
.contacts-header p {
  font-size: 16px;
  color: var(--gray-text);
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
}
.contacts-grid {
  display: grid;
  grid-template-columns: 1.2fr 1fr;
  gap: 32px;
  margin-bottom: 48px;
}
.contacts-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 40px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
}
.contacts-card h2 {
  font-size: 20px;
  font-weight: 700;
  color: var(--slate-900);
  margin-bottom: 24px;
  border-bottom: 1px solid var(--slate-50);
  padding-bottom: 12px;
}
.contact-item {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  margin-bottom: 24px;
}
.contact-item:last-child {
  margin-bottom: 0;
}
.contact-item-icon {
  background: var(--slate-50);
  color: var(--slate-900);
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.contact-item-details h4 {
  font-size: 14.5px;
  font-weight: 700;
  margin-bottom: 4px;
  color: var(--slate-900);
}
.contact-item-details p {
  font-size: 14px;
  line-height: 1.5;
  color: #4B5563;
  margin-bottom: 0;
}
.contact-item-details a {
  color: var(--slate-900);
  text-decoration: none;
  font-weight: 600;
}
.contact-item-details a:hover {
  text-decoration: underline;
}
.messenger-buttons {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 16px;
}
.m-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 12px 20px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  transition: opacity 0.2s ease;
}
.m-btn:hover {
  opacity: 0.9;
}
.m-btn.tg-btn {
  background: #229ED9;
  color: var(--white);
}
.m-btn.vb-btn {
  background: #7360F2;
  color: var(--white);
}
.support-cta-box {
  background: var(--slate-50);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 32px;
  text-align: center;
  height: fit-content;
}
.support-cta-box h3 {
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 12px;
}
.support-cta-box p {
  font-size: 13.5px;
  line-height: 1.6;
  color: #4B5563;
  margin-bottom: 24px;
}
.support-cta-btn {
  display: inline-block;
  background: var(--slate-900);
  color: var(--white);
  font-weight: 700;
  padding: 12px 24px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  font-size: 14px;
  transition: transform 0.2s ease;
}
.support-cta-btn:hover {
  transform: scale(1.02);
}
.req-section {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 32px;
}
.req-section h3 {
  font-size: 16px;
  font-weight: 700;
  margin-bottom: 16px;
}
.req-list {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}
.req-list-item {
  font-size: 13px;
  line-height: 1.6;
  color: #4B5563;
}
.req-list-item strong {
  color: var(--slate-900);
}

@media (max-width: 768px) {
  .contacts-grid {
    grid-template-columns: 1fr;
    gap: 24px;
  }
  .req-list {
    grid-template-columns: 1fr;
  }
  .contacts-card {
    padding: 24px;
  }
}
</style>
@endsection

@section('content')
<div class="contacts-container">
  
  <header class="contacts-header">
    <h1>Контакты</h1>
    <p>Есть вопросы, предложения или возникли трудности с использованием Flatly.by? Свяжитесь с нами любым удобным способом.</p>
  </header>

  <div class="contacts-grid">
    
    <!-- Left: Contacts details -->
    <div class="contacts-card">
      <h2>Служба поддержки</h2>
      
      <!-- Phone -->
      <div class="contact-item">
        <div class="contact-item-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </div>
        <div class="contact-item-details">
          <h4>Телефон</h4>
          <p><a href="tel:+375296045015">+375 (29) 604-50-15</a></p>
        </div>
      </div>

      <!-- Email -->
      <div class="contact-item">
        <div class="contact-item-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </div>
        <div class="contact-item-details">
          <h4>Электронная почта</h4>
          <p><a href="mailto:loyko1012@gmail.com">loyko1012@gmail.com</a></p>
        </div>
      </div>

      <!-- Working Hours -->
      <div class="contact-item">
        <div class="contact-item-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div class="contact-item-details">
          <h4>Время работы поддержки</h4>
          <p>Ежедневно с 09:00 до 21:00</p>
        </div>
      </div>

      <!-- Messengers -->
      <div class="contact-item" style="margin-top: 12px;">
        <div class="contact-item-details" style="width: 100%;">
          <h4>Быстрая связь в мессенджерах</h4>
          <div class="messenger-buttons">
            <a href="https://t.me/byejey1" target="_blank" class="m-btn tg-btn">
              <span>Написать в Telegram</span>
            </a>
            <a href="viber://chat?number=%2B375296045015" class="m-btn vb-btn">
              <span>Написать в Viber</span>
            </a>
          </div>
        </div>
      </div>

    </div>

    <!-- Right: Support CTA -->
    <div class="support-cta-box">
      <h3>Форма обратной связи</h3>
      <p>Вы можете отправить обращение, сообщение о баге или предложение по улучшению сервиса напрямую через интерактивную форму поддержки. Мы ответим вам в кратчайшие сроки.</p>
      <button class="support-cta-btn" onclick="openSupportModal()">Открыть форму связи</button>
    </div>

  </div>

  <!-- Requisites -->
  <div class="req-section">
    <h3>Юридическая информация</h3>
    <div class="req-list">
      <div class="req-list-item"><strong>Индивидуальный предприниматель:</strong> Лойко Евгений Юрьевич</div>
      <div class="req-list-item"><strong>УНП:</strong> 693419408</div>
      <div class="req-list-item"><strong>Регистрация:</strong> Решение Минского районного исполнительного комитета</div>
      <div class="req-list-item"><strong>Юридический адрес:</strong> Минский р-н, Щомыслицкий с/с, д. Богатырёво, ул. Минская, д. 2</div>
      <div class="req-list-item"><strong>Расчетный счет:</strong> BY97ALFA30132J42370010270000 в ЗАО "Альфа-Банк"</div>
      <div class="req-list-item"><strong>Контакты для рекламаций:</strong> loyko1012@gmail.com</div>
    </div>
  </div>

</div>
@endsection
