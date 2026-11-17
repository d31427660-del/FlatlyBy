@extends('layouts.app')

@section('title', 'Вход и регистрация хозяев — Flatly.by')
@section('meta_robots', 'noindex, follow')

@section('head')
<style>
.login-container {
  max-width: 480px;
  margin: 60px auto 100px;
  padding: 0 20px;
}
.login-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 40px;
  box-shadow: 0 10px 40px rgba(26,26,46,0.03);
  position: relative;
  overflow: hidden;
}
.login-header {
  text-align: center;
  margin-bottom: 28px;
}
.login-header h1 {
  font-family: 'Syne', sans-serif;
  font-size: 26px;
  font-weight: 800;
  color: var(--slate-900);
  letter-spacing: -0.5px;
}
.login-header p {
  font-size: 13.5px;
  color: var(--gray-text);
  margin-top: 6px;
}

/* Sliding Tabs */
.auth-tabs {
  display: flex;
  background: var(--slate-50);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 4px;
  margin-bottom: 28px;
}
.auth-tab {
  flex: 1;
  text-align: center;
  padding: 10px;
  font-size: 13.5px;
  font-weight: 700;
  color: var(--gray-text);
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.2s ease-in-out;
}
.auth-tab.active {
  color: var(--slate-900);
  background: var(--white);
  box-shadow: 0 4px 12px rgba(26, 26, 46, 0.05);
}

/* Sliding Sections */
.form-section {
  display: none;
  animation: formFadeIn 0.3s ease-in-out forwards;
}
.form-section.active {
  display: block;
}
@keyframes formFadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Form Styles */
.form-field {
  margin-bottom: 18px;
}
.form-field label {
  display: block;
  font-size: 13px;
  font-weight: 600;
  color: var(--slate-900);
  margin-bottom: 6px;
}
.form-field label span {
  color: var(--gray-text);
  font-weight: 400;
}
.form-field input {
  width: 100%;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 12px 14px;
  font-size: 14px;
  color: var(--slate-900);
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.form-field input:focus {
  border-color: var(--amber);
  box-shadow: 0 0 0 3px rgba(245, 166, 35, 0.12);
}
.form-field-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}
.phone-input-group {
  display: flex;
  gap: 8px;
}
.phone-prefix {
  background: var(--slate-50);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 12px 14px;
  font-size: 14px;
  font-weight: 600;
  color: var(--slate-900);
  display: flex;
  align-items: center;
  user-select: none;
}
.phone-input-group input {
  flex: 1;
}

.submit-btn {
  background: var(--amber);
  color: var(--slate-900);
  border: none;
  width: 100%;
  padding: 13px;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 700;
  transition: all 0.15s;
  margin-top: 10px;
  display: block;
  text-align: center;
}
.submit-btn:hover {
  background: var(--amber-dark);
  color: #FFF;
}
.divider {
  display: flex;
  align-items: center;
  text-align: center;
  margin: 26px 0;
  color: #B0B0C0;
  font-size: 12px;
  font-weight: 500;
}
.divider::before, .divider::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid var(--border);
}
.divider:not(:empty)::before {
  margin-right: .75em;
}
.divider:not(:empty)::after {
  margin-left: .75em;
}
.social-btns {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  width: 100%;
  padding: 12px;
  border: 1px solid var(--border);
  background: var(--white);
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  color: var(--slate-900);
  transition: background 0.15s, border-color 0.15s, transform 0.15s;
  text-decoration: none;
}
.social-btn:hover {
  background: var(--slate-50);
  border-color: var(--amber-light);
  transform: translateY(-1px);
}
.social-btn svg {
  width: 18px;
  height: 18px;
  fill: currentColor;
}

.hint {
  font-size: 12px;
  color: var(--gray-text);
  margin-top: 4px;
  line-height: 1.4;
}

/* ===== RESPONSIVE MEDIA QUERIES ===== */
@media (max-width: 576px) {
  .login-container {
    margin: 24px auto 60px;
    padding: 0 12px;
  }
  .login-card {
    padding: 28px 20px;
    border-radius: 16px;
  }
  .login-header h1 {
    font-size: 22px;
  }
  .form-field-row {
    grid-template-columns: 1fr;
    gap: 0;
  }
}
@media (max-width: 360px) {
  .login-card {
    padding: 24px 14px;
    border-radius: 12px;
  }
  .login-header h1 {
    font-size: 19px;
  }
  .login-header p {
    font-size: 12.5px;
  }
  .submit-btn {
    padding: 11px;
    font-size: 14px;
  }
}
</style>
@endsection

@section('content')
<div class="login-container">
  <div class="login-card">
    
    <!-- Production Feedback Alerts -->
    @if(session('error'))
      <div style="background:#FFF0F2; border:1px solid #FFCCD3; color:#D9383A; border-radius:10px; padding:12px 14px; font-size:13px; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:8px; line-height:1.4;">
        <x-solar-danger-circle-linear width="16" height="16" style="flex-shrink:0;" />
        <span>{{ session('error') }}</span>
      </div>
    @endif
    @if(session('success'))
      <div style="background:#EAF3DE; border:1px solid #C2E299; color:#3B6D11; border-radius:10px; padding:12px 14px; font-size:13px; font-weight:600; margin-bottom:20px; display:flex; align-items:center; gap:8px; line-height:1.4;">
        <x-solar-check-circle-linear width="16" height="16" style="flex-shrink:0;" />
        <span>{{ session('success') }}</span>
      </div>
    @endif

    <div class="login-header">
      <h1 id="auth-title">Вход для хозяев</h1>
      <p id="auth-subtitle">Разместите свои квартиры на Flatly.by</p>
    </div>

    <!-- Switchable Tabs -->
    <div class="auth-tabs">
      <div class="auth-tab active" id="tab-login" onclick="switchTab('login')">Вход</div>
      <div class="auth-tab" id="tab-register" onclick="switchTab('register')">Регистрация</div>
    </div>

    <!-- FORM 1: LOGIN -->
    <div class="form-section active" id="section-login">
      <form onsubmit="handleLogin(event)">
        <div class="form-field">
          <label for="login-email">Электронная почта</label>
          <input type="email" id="login-email" name="email" autocomplete="username" required placeholder="example@mail.ru">
        </div>
        <div class="form-field">
          <label for="login-password">Пароль</label>
          <input type="password" id="login-password" name="password" autocomplete="current-password" required placeholder="Введите ваш пароль">
        </div>
        <button type="submit" class="submit-btn">Войти в личный кабинет</button>
      </form>
    </div>

    <!-- FORM 2: REGISTRATION -->
    <div class="form-section" id="section-register">
      <form onsubmit="handleRegister(event)">
        <div class="form-field">
          <label for="reg-name">Ваше имя <span class="req">*</span></label>
          <input type="text" id="reg-name" name="name" autocomplete="name" required placeholder="Как к вам обращаться">
        </div>
        <div class="form-field">
          <label for="reg-email">Электронная почта <span class="req">*</span></label>
          <input type="email" id="reg-email" name="email" autocomplete="email" required placeholder="example@mail.ru">
        </div>
        <div class="form-field">
          <label for="reg-password">Пароль <span class="req">*</span></label>
          <input type="password" id="reg-password" name="password" autocomplete="new-password" required placeholder="Минимум 4 символа">
        </div>
        <div class="form-field">
          <label for="reg-phone">Номер телефона</label>
          <div class="phone-input-group">
            <div class="phone-prefix">+375</div>
            <input type="tel" id="reg-phone" name="phone" autocomplete="tel-national" placeholder="XX XXX XX XX" maxlength="9">
          </div>
          <div class="hint">Будет отображаться в объявлениях по умолчанию</div>
        </div>
        <div class="form-field-row">
          <div class="form-field">
            <label for="reg-telegram">Telegram <span>(опц.)</span></label>
            <input type="text" id="reg-telegram" name="telegram" autocomplete="off" placeholder="@username">
          </div>
          <div class="form-field">
            <label for="reg-viber">Viber <span>(опц.)</span></label>
            <input type="text" id="reg-viber" name="viber" autocomplete="off" placeholder="+375...">
          </div>
        </div>
        <div class="form-field">
          <label for="reg-unp">УНП (для ИП/Юрлиц/Самозанятых) <span>(опц.)</span></label>
          <input type="text" id="reg-unp" name="unp" autocomplete="off" placeholder="9 символов (например, AB1234567)" maxlength="9">
          <div class="hint">УНП автоматически подтверждает ваш статус арендодателя</div>
        </div>
        <button type="submit" class="submit-btn">Зарегистрироваться</button>
      </form>
    </div>

    <!-- SOCIAL BUTTONS SECTION -->
    <div class="divider">или войти через</div>

    <div class="social-btns">
      <!-- Real Google Redirect Link -->
      <a href="{{ route('auth.google') }}" class="social-btn">
        <svg viewBox="0 0 24 24">
          <path d="M12.24 10.285V14.4h6.887c-.648 2.41-2.519 4.114-5.136 4.114A5.626 5.626 0 0 1 8.35 12.89a5.626 5.626 0 0 1 5.64-5.63 5.568 5.568 0 0 1 3.96 1.636l3.155-3.155A9.92 9.92 0 0 0 13.99 3c-5.52 0-10 4.48-10 10s4.48 10 10 10c5.73 0 10.2-4.03 10.2-9.71 0-.64-.06-1.29-.18-1.92H12.24z"/>
        </svg>
        Войти через Google
      </a>
    </div>

  </div>
</div>
@endsection

@section('scripts')
<script>
// Switch between Login and Registration tabs
function switchTab(type) {
  document.querySelectorAll('.auth-tab').forEach(el => el.classList.remove('active'));
  document.querySelectorAll('.form-section').forEach(el => el.classList.remove('active'));

  if (type === 'login') {
    document.getElementById('tab-login').classList.add('active');
    document.getElementById('section-login').classList.add('active');
    document.getElementById('auth-title').textContent = 'Вход для хозяев';
    document.getElementById('auth-subtitle').textContent = 'Разместите свои квартиры на Flatly.by';
  } else {
    document.getElementById('tab-register').classList.add('active');
    document.getElementById('section-register').classList.add('active');
    document.getElementById('auth-title').textContent = 'Создать аккаунт';
    document.getElementById('auth-subtitle').textContent = 'Быстрая регистрация арендодателя за 1 минуту';
  }
}

// Submitting normal login
function handleLogin(event) {
  event.preventDefault();
  showToast('🔑 Выполняем вход...');

  const email = document.getElementById('login-email').value;
  const password = document.getElementById('login-password').value;

  fetch("{{ route('login.post') }}", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": "{{ csrf_token() }}"
    },
    body: JSON.stringify({ email, password })
  })
  .then(res => {
    if (res.status === 419) {
      throw new Error('Сессия устарела (ошибка 419). Пожалуйста, обновите страницу.');
    }
    const contentType = res.headers.get("content-type");
    if (!res.ok) {
      if (contentType && contentType.indexOf("application/json") !== -1) {
        return res.json().then(data => { throw new Error(data.message || 'Ошибка входа') });
      } else {
        throw new Error('Ошибка сервера (' + res.status + '). Пожалуйста, обновите страницу.');
      }
    }
    return res.json();
  })
  .then(data => {
    if (data.success) {
      showToast('✓ Успешный вход! Перенаправляем...');
      setTimeout(() => {
        window.location.href = "{{ route('dashboard') }}";
      }, 700);
    }
  })
  .catch(err => {
    showToast('❌ ' + err.message);
  });
}

// Submitting normal registration
function handleRegister(event) {
  event.preventDefault();
  showToast('📝 Создаем профиль в БД...');

  const name = document.getElementById('reg-name').value;
  const email = document.getElementById('reg-email').value;
  const password = document.getElementById('reg-password').value;
  const phone = document.getElementById('reg-phone').value;
  const telegram = document.getElementById('reg-telegram').value;
  const viber = document.getElementById('reg-viber').value;
  const unp = document.getElementById('reg-unp').value;

  fetch("{{ route('register.post') }}", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": "{{ csrf_token() }}"
    },
    body: JSON.stringify({ name, email, password, phone, telegram, viber, unp })
  })
  .then(res => {
    if (res.status === 419) {
      throw new Error('Сессия устарела (ошибка 419). Пожалуйста, обновите страницу.');
    }
    const contentType = res.headers.get("content-type");
    if (!res.ok) {
      if (contentType && contentType.indexOf("application/json") !== -1) {
        return res.json().then(data => { throw new Error(data.message || 'Ошибка регистрации') });
      } else {
        throw new Error('Ошибка сервера (' + res.status + '). Пожалуйста, обновите страницу.');
      }
    }
    return res.json();
  })
  .then(data => {
    if (data.success) {
      showToast('🎉 Регистрация завершена успешно!');
      setTimeout(() => {
        window.location.href = "{{ route('dashboard') }}";
      }, 800);
    }
  })
  .catch(err => {
    showToast('❌ ' + err.message);
  });
}
</script>
@endsection
