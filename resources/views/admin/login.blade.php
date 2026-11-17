<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="/favicon.ico" sizes="any">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <title>Вход в панель администратора — Flatly.by</title>
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --bg: #F8FAFC;
      --card-bg: #FFFFFF;
      --border: #E2E8F0;
      --border-focus: #818CF8;
      --text-main: #0F172A;
      --text-muted: #64748B;
      
      --primary: #4F46E5;
      --primary-hover: #4338CA;
      --primary-light: #EEF2FF;
      
      --rose: #E11D48;
      --rose-light: #FFF1F2;
      --emerald: #059669;
      --emerald-light: #ECFDF5;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    body {
      background-color: var(--bg);
      color: var(--text-main);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      overflow: hidden;
      position: relative;
    }

    /* Soft Ambient Lights for Light Theme */
    .glow-sphere {
      position: absolute;
      width: 500px;
      height: 500px;
      border-radius: 50%;
      pointer-events: none;
      z-index: 1;
    }
    .glow-1 {
      top: -150px;
      left: -150px;
      background: radial-gradient(circle, rgba(99, 102, 241, 0.12) 0%, rgba(99, 102, 241, 0) 70%);
      filter: blur(60px);
    }
    .glow-2 {
      bottom: -150px;
      right: -150px;
      background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, rgba(59, 130, 246, 0) 70%);
      filter: blur(60px);
    }

    .container {
      width: 100%;
      max-width: 440px;
      padding: 24px;
      z-index: 10;
    }

    .login-card {
      background: var(--card-bg);
      border: 1px solid var(--border);
      box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.07), 0 0 0 1px rgba(226, 232, 240, 0.6);
      border-radius: 24px;
      padding: 44px 36px;
      transition: all 0.3s ease;
    }
    
    .login-card:hover {
      box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.1);
    }

    .header {
      text-align: center;
      margin-bottom: 32px;
    }

    .logo-wrap {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      margin-bottom: 16px;
      text-decoration: none;
    }

    .logo-badge {
      background: linear-gradient(135deg, var(--primary) 0%, #3730A3 100%);
      color: white;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 22px;
      width: 46px;
      height: 46px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 6px 16px rgba(79, 70, 229, 0.25);
    }

    .logo-text {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 26px;
      color: var(--text-main);
      letter-spacing: -0.5px;
    }

    .title {
      font-size: 15px;
      font-weight: 600;
      color: var(--text-muted);
      margin-top: 4px;
    }

    .form-group {
      margin-bottom: 24px;
      position: relative;
    }

    .form-label {
      font-size: 12px;
      font-weight: 700;
      color: var(--text-main);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      display: block;
      margin-bottom: 8px;
    }

    .input-wrapper {
      position: relative;
    }

    .input-field {
      width: 100%;
      background: #F8FAFC;
      border: 1.5px solid var(--border);
      border-radius: 14px;
      padding: 14px 44px 14px 44px;
      color: var(--text-main);
      font-size: 15px;
      font-weight: 500;
      transition: all 0.2s ease;
      outline: none;
    }

    .input-field:focus {
      border-color: var(--primary);
      background: #FFFFFF;
      box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
    }

    .input-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      width: 20px;
      height: 20px;
      color: var(--text-muted);
      stroke: currentColor;
      fill: none;
      stroke-width: 2;
      transition: all 0.2s ease;
    }

    .input-field:focus + .input-icon {
      color: var(--primary);
    }

    .toggle-pwd {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--text-muted);
      cursor: pointer;
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 6px;
      transition: color 0.2s;
    }

    .toggle-pwd:hover {
      color: var(--text-main);
    }

    .btn-submit {
      width: 100%;
      background: linear-gradient(135deg, var(--primary) 0%, #4338CA 100%);
      color: white;
      border: none;
      border-radius: 14px;
      padding: 14px 20px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      transition: all 0.25s ease;
      box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
    }

    .btn-submit:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
      background: linear-gradient(135deg, #4338CA 0%, #3730A3 100%);
    }

    .btn-submit:active {
      transform: translateY(1px);
    }

    .btn-submit svg {
      width: 18px;
      height: 18px;
      stroke: currentColor;
      fill: none;
      stroke-width: 2.2;
    }

    .toast {
      position: fixed;
      top: 24px;
      right: 24px;
      background: #FFFFFF;
      border: 1px solid var(--border);
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
      border-radius: 14px;
      padding: 14px 20px;
      color: var(--text-main);
      font-size: 14px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
      transform: translateY(-20px);
      opacity: 0;
      transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      z-index: 1000;
      pointer-events: none;
    }

    .toast.show {
      transform: translateY(0);
      opacity: 1;
    }

    .toast.error {
      border-color: rgba(225, 29, 72, 0.3);
      background: #FFF1F2;
      color: var(--rose);
    }

    .toast.success {
      border-color: rgba(5, 150, 105, 0.3);
      background: #ECFDF5;
      color: var(--emerald);
    }

    /* Spinner Animation */
    .spinner {
      animation: spin 1s linear infinite;
      display: none;
    }

    @keyframes spin {
      100% { transform: rotate(360deg); }
    }

    .back-home {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      margin-top: 24px;
      color: var(--text-muted);
      font-size: 14px;
      font-weight: 500;
      text-decoration: none;
      transition: color 0.2s;
    }

    .back-home:hover {
      color: var(--primary);
    }

    @media (max-width: 480px) {
      .login-card {
        padding: 32px 24px;
        border-radius: 20px;
      }
      .container {
        padding: 16px;
      }
      .logo-text {
        font-size: 22px;
      }
      .title {
        font-size: 14px;
      }
    }
  </style>
</head>
<body>

  <!-- Soft Ambient Glows -->
  <div class="glow-sphere glow-1"></div>
  <div class="glow-sphere glow-2"></div>

  <div class="container">
    <div class="login-card">
      <div class="header">
        <a href="/" class="logo-wrap">
          <div class="logo-badge">F</div>
          <div class="logo-text">Flatly.by</div>
        </a>
        <div class="title">Панель модератора</div>
      </div>

      <form id="login-form">
        <div class="form-group">
          <label class="form-label" for="password">Пароль доступа</label>
          <div class="input-wrapper">
            <input type="password" id="password" class="input-field" placeholder="Введите пароль" required autocomplete="current-password">
            <svg class="input-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <button type="button" class="toggle-pwd" id="toggle-pwd-btn" title="Показать/скрыть пароль">
              <svg id="eye-icon" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>

        <button type="submit" id="btn-submit" class="btn-submit">
          <svg class="spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.3)" fill="none"/><path d="M12 2a10 10 0 0 1 10 10" stroke="white"/></svg>
          <span id="btn-text">Войти в панель</span>
          <svg id="arrow-icon" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>
      </form>

      <a href="/" class="back-home">
        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Вернуться на сайт
      </a>
    </div>
  </div>

  <div id="toast" class="toast">⚠️ Ошибка авторизации</div>

  <script>
    const form = document.getElementById('login-form');
    const passwordInput = document.getElementById('password');
    const togglePwdBtn = document.getElementById('toggle-pwd-btn');
    const btnSubmit = document.getElementById('btn-submit');
    const spinner = btnSubmit.querySelector('.spinner');
    const arrowIcon = document.getElementById('arrow-icon');
    const btnText = document.getElementById('btn-text');
    const toast = document.getElementById('toast');

    // Toggle password visibility
    togglePwdBtn.addEventListener('click', function() {
      const isPwd = passwordInput.type === 'password';
      passwordInput.type = isPwd ? 'text' : 'password';
    });

    function showToast(message, type = 'error') {
      toast.textContent = message;
      toast.className = `toast ${type} show`;
      setTimeout(() => {
        toast.classList.remove('show');
      }, 4000);
    }

    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const password = passwordInput.value;
      if (!password) return;

      // Enter Loading State
      btnSubmit.disabled = true;
      spinner.style.display = 'block';
      arrowIcon.style.display = 'none';
      btnText.textContent = 'Проверка пароля...';

      fetch("{{ route('admin.login.post') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ password })
      })
      .then(async res => {
        const data = await res.json();
        if (res.ok && data.success) {
          showToast('🔑 Авторизация успешна! Вход...', 'success');
          setTimeout(() => {
            window.location.href = "{{ route('admin.dashboard') }}";
          }, 800);
        } else {
          throw new Error(data.message || 'Неверный пароль администратора.');
        }
      })
      .catch(err => {
        // Exit Loading State
        btnSubmit.disabled = false;
        spinner.style.display = 'none';
        arrowIcon.style.display = 'block';
        btnText.textContent = 'Войти в панель';
        
        showToast('✕ ' + err.message, 'error');
        passwordInput.value = '';
        passwordInput.focus();
      });
    });
  </script>
</body>
</html>
