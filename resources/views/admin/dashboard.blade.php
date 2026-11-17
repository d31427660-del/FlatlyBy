<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="/favicon.ico" sizes="any">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <title>Панель управления модератора — Flatly.by</title>
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --bg: #F8FAFC;
      --card-bg: #FFFFFF;
      --card-subtle: #F1F5F9;
      --border: #E2E8F0;
      --border-hover: #CBD5E1;
      --text-main: #0F172A;
      --text-muted: #64748B;
      
      --primary: #4F46E5;
      --primary-hover: #4338CA;
      --primary-light: #EEF2FF;
      --primary-border: #C7D2FE;
      
      --emerald: #059669;
      --emerald-light: #ECFDF5;
      --emerald-border: #A7F3D0;
      
      --amber: #D97706;
      --amber-light: #FFFBEB;
      --amber-border: #FDE68A;
      
      --rose: #E11D48;
      --rose-light: #FFF1F2;
      --rose-border: #FECDD3;
      
      --sky: #0284C7;
      --sky-light: #F0F9FF;
      --sky-border: #BAE6FD;
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
      min-height: 100vh;
      overflow-x: hidden;
      padding-bottom: 60px;
    }

    /* Ambient Background Lights */
    .glow-sphere {
      position: absolute;
      width: 600px;
      height: 600px;
      border-radius: 50%;
      pointer-events: none;
      z-index: 1;
    }
    .glow-1 {
      top: -200px;
      right: -100px;
      background: radial-gradient(circle, rgba(99, 102, 241, 0.07) 0%, rgba(99, 102, 241, 0) 70%);
      filter: blur(80px);
    }
    .glow-2 {
      bottom: -200px;
      left: -100px;
      background: radial-gradient(circle, rgba(59, 130, 246, 0.07) 0%, rgba(59, 130, 246, 0) 70%);
      filter: blur(80px);
    }

    /* Header Nav */
    header {
      background: rgba(255, 255, 255, 0.85);
      border-bottom: 1px solid var(--border);
      backdrop-filter: blur(16px);
      padding: 14px 40px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
    }

    .logo-group {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
    }

    .logo-badge {
      background: linear-gradient(135deg, var(--primary) 0%, #3730A3 100%);
      color: white;
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 18px;
      width: 40px;
      height: 40px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }

    .logo-text {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 22px;
      color: var(--text-main);
      letter-spacing: -0.5px;
    }

    .badge-moderator {
      background: var(--primary-light);
      color: var(--primary);
      border: 1px solid var(--primary-border);
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      padding: 4px 10px;
      border-radius: 8px;
      letter-spacing: 0.5px;
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .btn-logout {
      background: var(--card-bg);
      border: 1px solid var(--border);
      color: var(--text-muted);
      border-radius: 10px;
      padding: 8px 16px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .btn-logout:hover {
      background: var(--rose-light);
      color: var(--rose);
      border-color: var(--rose-border);
    }

    /* Main Content Layout */
    .wrapper {
      max-width: 1400px;
      margin: 32px auto 0;
      padding: 0 40px;
      position: relative;
      z-index: 10;
    }

    /* Dashboard Title */
    .title-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
    }

    .title-row h1 {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 26px;
      color: var(--text-main);
      letter-spacing: -0.5px;
    }

    .title-row p {
      font-size: 14px;
      color: var(--text-muted);
      margin-top: 2px;
    }

    /* Metrics Grid */
    .metrics-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 16px;
      margin-bottom: 32px;
    }

    .metric-card {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -2px rgba(0, 0, 0, 0.02);
      transition: all 0.25s ease;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }

    .metric-card:hover {
      transform: translateY(-2px);
      border-color: var(--border-hover);
      box-shadow: 0 12px 20px -4px rgba(15, 23, 42, 0.06);
    }

    .metric-card.active-filter {
      border-color: var(--primary);
      box-shadow: 0 0 0 2px var(--primary-border);
    }

    .metric-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
    }

    .metric-label {
      font-size: 12px;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .metric-icon-wrap {
      width: 34px;
      height: 34px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: var(--card-subtle);
      color: var(--text-muted);
    }

    .metric-val {
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 30px;
      color: var(--text-main);
      line-height: 1;
    }

    .metric-card.amber-card .metric-icon-wrap { background: var(--amber-light); color: var(--amber); }
    .metric-card.amber-card .metric-val { color: var(--amber); }

    .metric-card.emerald-card .metric-icon-wrap { background: var(--emerald-light); color: var(--emerald); }
    .metric-card.emerald-card .metric-val { color: var(--emerald); }

    .metric-card.sky-card .metric-icon-wrap { background: var(--sky-light); color: var(--sky); }
    .metric-card.sky-card .metric-val { color: var(--sky); }

    .metric-card.primary-card .metric-icon-wrap { background: var(--primary-light); color: var(--primary); }

    /* Tabs Layout & Navigation */
    .tabs-nav-container {
      margin-bottom: 24px;
      border-bottom: 1px solid var(--border);
      padding-bottom: 12px;
    }

    .tabs-nav {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .tab-btn {
      background: var(--card-bg);
      border: 1px solid var(--border);
      color: var(--text-muted);
      border-radius: 12px;
      padding: 10px 18px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s ease;
    }

    .tab-btn:hover {
      background: var(--card-subtle);
      color: var(--text-main);
      border-color: var(--border-hover);
    }

    .tab-btn.active {
      background: var(--primary);
      border-color: var(--primary);
      color: white;
      box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);
    }

    .tab-icon {
      width: 18px;
      height: 18px;
      stroke: currentColor;
      fill: none;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
      animation: fadeIn 0.25s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(6px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Tariff Badges */
    .badge-tariff {
      display: inline-flex;
      align-items: center;
      padding: 2px 8px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 11px;
    }
    .badge-tariff.active {
      background: var(--emerald-light);
      color: var(--emerald);
      border: 1px solid var(--emerald-border);
    }
    .badge-tariff.inactive {
      background: var(--rose-light);
      color: var(--rose);
      border: 1px solid var(--rose-border);
    }
    .badge-tariff.active.promo {
      background: var(--primary-light);
      color: var(--primary);
      border: 1px solid var(--primary-border);
    }
    .badge-tariff.active.home {
      background: var(--amber-light);
      color: var(--amber);
      border: 1px solid var(--amber-border);
    }

    /* Search & Filter bar */
    .filter-bar {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 12px 16px;
      display: flex;
      align-items: center;
      gap: 14px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.01);
    }

    .search-input-wrapper {
      position: relative;
      flex: 1;
    }

    .search-input {
      width: 100%;
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: 10px;
      padding: 10px 16px 10px 40px;
      color: var(--text-main);
      font-size: 14px;
      outline: none;
      transition: all 0.2s ease;
    }

    .search-input:focus {
      border-color: var(--primary);
      background: #FFFFFF;
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .search-icon {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      width: 18px;
      height: 18px;
      color: var(--text-muted);
      stroke: currentColor;
      fill: none;
      stroke-width: 2;
    }

    /* Grid Table Wrapper */
    .table-container {
      background: var(--card-bg);
      border: 1px solid var(--border);
      border-radius: 16px;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
      overflow: hidden;
    }

    .admin-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
    }

    .admin-table th {
      background: #F8FAFC;
      border-bottom: 1px solid var(--border);
      padding: 14px 20px;
      font-size: 11px;
      font-weight: 700;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .admin-table th.sortable {
      cursor: pointer;
      user-select: none;
      transition: background 0.15s;
    }

    .admin-table th.sortable:hover {
      background: #F1F5F9;
      color: var(--text-main);
    }

    .sort-icon {
      display: inline-block;
      margin-left: 4px;
      font-size: 11px;
    }

    .admin-table td {
      padding: 14px 20px;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
      font-size: 14px;
    }

    .admin-table tr:last-child td {
      border-bottom: none;
    }

    .admin-table tr {
      transition: background 0.15s ease;
    }

    .admin-table tr:hover {
      background: #F8FAFC;
    }

    /* Custom rows styles */
    .col-img-wrap {
      width: 54px;
      height: 40px;
      border-radius: 8px;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      flex-shrink: 0;
    }

    .col-listing-info h4 {
      font-size: 14px;
      font-weight: 600;
      color: var(--text-main);
      margin-bottom: 2px;
      max-width: 320px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .col-listing-info p {
      font-size: 12px;
      color: var(--text-muted);
    }

    .col-host-info {
      font-weight: 600;
      color: var(--text-main);
      font-size: 13px;
    }

    .col-host-info span {
      display: block;
      font-size: 12px;
      font-weight: 500;
      color: var(--text-muted);
      margin-top: 2px;
    }

    .badge-status {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
      font-weight: 600;
      padding: 4px 10px;
      border-radius: 8px;
      white-space: nowrap;
    }

    .badge-status.active { background: var(--emerald-light); color: var(--emerald); border: 1px solid var(--emerald-border); }
    .badge-status.completed { background: var(--emerald-light); color: var(--emerald); border: 1px solid var(--emerald-border); }
    .badge-status.pending { background: var(--amber-light); color: var(--amber); border: 1px solid var(--amber-border); }
    .badge-status.pending_payment { background: var(--amber-light); color: var(--amber); border: 1px solid var(--amber-border); }
    .badge-status.rejected { background: var(--rose-light); color: var(--rose); border: 1px solid var(--rose-border); }
    .badge-status.failed { background: var(--rose-light); color: var(--rose); border: 1px solid var(--rose-border); }
    .badge-status.hidden { background: var(--sky-light); color: var(--sky); border: 1px solid var(--sky-border); }

    .badge-unp {
      background: var(--bg);
      border: 1px solid var(--border);
      font-size: 11px;
      font-weight: 700;
      padding: 3px 8px;
      border-radius: 6px;
      color: var(--text-muted);
      display: inline-block;
    }
    .badge-unp.verified {
      border-color: var(--emerald-border);
      background: var(--emerald-light);
      color: var(--emerald);
    }
    .badge-unp.invalid {
      border-color: var(--rose-border);
      background: var(--rose-light);
      color: var(--rose);
    }

    .banner-upload-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      margin-bottom: 20px;
    }

    /* ===== RESPONSIVE MEDIA QUERIES ===== */
    @media (max-width: 1200px) {
      .metrics-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }
    @media (max-width: 768px) {
      header {
        padding: 14px 20px;
      }
      .wrapper {
        padding: 0 20px;
        margin-top: 20px;
      }
      .metrics-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-bottom: 20px;
      }
      .title-row h1 {
        font-size: 22px;
      }
      .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }
      .admin-table th, .admin-table td {
        padding: 10px 14px;
      }
    }
    @media (max-width: 576px) {
      header {
        flex-direction: column;
        gap: 10px;
        padding: 14px 16px;
      }
      .logo-group {
        justify-content: center;
      }
      .header-actions {
        width: 100%;
        display: flex;
        justify-content: center;
      }
      .btn-logout {
        width: 100%;
        justify-content: center;
      }
      .metrics-grid {
        grid-template-columns: 1fr;
      }
      .filter-bar {
        padding: 10px 12px;
      }
    }
    @media (max-width: 900px) {
      .banner-upload-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <!-- Soft Ambient Lights -->
  <div class="glow-sphere glow-1"></div>
  <div class="glow-sphere glow-2"></div>

  <!-- Header Nav -->
  <header>
    <a href="/" class="logo-group">
      <div class="logo-badge">F</div>
      <div class="logo-text">Flatly.by</div>
      <div class="badge-moderator">Модератор</div>
    </a>
    <div class="header-actions">
      <form action="{{ route('admin.logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn-logout">
          <x-solar-logout-2-linear width="16" height="16" />
          Выйти из панели
        </button>
      </form>
    </div>
  </header>

  <!-- Main Wrapper -->
  <div class="wrapper">
    <div class="title-row">
      <div>
        <h1>Панель управления администратора</h1>
        <p>Управление объявлениями жилья, пользователями, транзакциями и баннерами</p>
      </div>
    </div>

    <!-- Metrics Cards Grid -->
    <div class="metrics-grid">
      <div class="metric-card primary-card" onclick="filterByMetric('all')">
        <div class="metric-header">
          <span class="metric-label">Всего жилья</span>
          <div class="metric-icon-wrap">
            <x-solar-home-2-linear width="18" height="18" />
          </div>
        </div>
        <div class="metric-val" id="count-total">{{ $metrics['total'] }}</div>
      </div>

      <div class="metric-card amber-card" id="card-pending-payment" onclick="filterByMetric('pending_payment')">
        <div class="metric-header">
          <span class="metric-label">Ожидают оплаты</span>
          <div class="metric-icon-wrap">
            <x-solar-card-linear width="18" height="18" />
          </div>
        </div>
        <div class="metric-val" id="count-pending-payment">{{ $metrics['pending_payment'] }}</div>
      </div>

      <div class="metric-card emerald-card" onclick="filterByMetric('active')">
        <div class="metric-header">
          <span class="metric-label">Опубликовано</span>
          <div class="metric-icon-wrap">
            <x-solar-check-circle-linear width="18" height="18" />
          </div>
        </div>
        <div class="metric-val" id="count-active">{{ $metrics['active'] }}</div>
      </div>

      <div class="metric-card sky-card" onclick="filterByMetric('hidden')">
        <div class="metric-header">
          <span class="metric-label">Скрыто</span>
          <div class="metric-icon-wrap">
            <x-solar-eye-closed-linear width="18" height="18" />
          </div>
        </div>
        <div class="metric-val" id="count-hidden">{{ $metrics['hidden'] }}</div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <span class="metric-label">Всего хозяев</span>
          <div class="metric-icon-wrap">
            <x-solar-users-group-two-rounded-linear width="18" height="18" />
          </div>
        </div>
        <div class="metric-val">{{ $metrics['hosts'] }}</div>
      </div>

      <div class="metric-card primary-card">
        <div class="metric-header">
          <span class="metric-label">Гостей сегодня</span>
          <div class="metric-icon-wrap">
            <x-solar-user-rounded-linear width="18" height="18" />
          </div>
        </div>
        <div class="metric-val" style="color: var(--primary);">{{ $visitorStats['unique_today'] }}</div>
      </div>

      <div class="metric-card sky-card">
        <div class="metric-header">
          <span class="metric-label">Просмотров за день</span>
          <div class="metric-icon-wrap">
            <x-solar-eye-linear width="18" height="18" />
          </div>
        </div>
        <div class="metric-val">{{ $visitorStats['views_today'] }}</div>
      </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="tabs-nav-container">
      <div class="tabs-nav">
        <button class="tab-btn active" data-tab="listings">
          <x-solar-home-2-linear class="tab-icon" width="16" height="16" />
          Объявления
        </button>
        <button class="tab-btn" data-tab="agency">
          <x-solar-buildings-2-linear class="tab-icon" width="16" height="16" />
          Агентство
        </button>
        <button class="tab-btn" data-tab="users">
          <x-solar-users-group-two-rounded-linear class="tab-icon" width="16" height="16" />
          Пользователи
        </button>
        <button class="tab-btn" data-tab="transactions">
          <x-solar-card-linear class="tab-icon" width="16" height="16" />
          Транзакции
        </button>
        <button class="tab-btn" data-tab="analytics">
          <x-solar-chart-2-linear class="tab-icon" width="16" height="16" />
          📊 Посещаемость
        </button>
        <button class="tab-btn" data-tab="banners">
          <x-solar-gallery-linear class="tab-icon" width="16" height="16" />
          Баннеры
        </button>
      </div>
    </div>

    <!-- Tab Listings -->
    <div id="tab-listings" class="tab-content active">
      <!-- Search & Status filter input -->
      <div class="filter-bar">
        <div class="search-input-wrapper">
          <input type="text" id="search-input" class="search-input" placeholder="Поиск объявлений по названию, адресу, имени хозяина или УНП...">
          <x-solar-magnifer-linear class="search-icon" width="18" height="18" />
        </div>
        <div style="min-width: 180px;">
          <select id="listing-status-filter" class="search-input" style="padding-left: 14px; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2216%22 height=%2216%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2364748B%22 stroke-width=%221.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M6 9l6 6 6-6%22/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
            <option value="">Все статусы</option>
            <option value="active">Опубликовано</option>
            <option value="pending_payment">Ожидает оплаты</option>
            <option value="pending">Ожидает</option>
            <option value="rejected">Отклонено</option>
            <option value="hidden">Скрыто</option>
          </select>
        </div>
      </div>

      <!-- Grid Listings Table Container -->
      <div class="table-container">
        <table class="admin-table" id="listings-table">
          <thead>
            <tr>
              <th class="sortable" onclick="sortTableByDate('listings-table', 0, 'data-date')">
                Дата создания <span class="sort-icon" id="sort-listings-table-0">▼</span>
              </th>
              <th>Жильё</th>
              <th>Хозяин / Контакты</th>
              <th>УНП</th>
              <th>Статус</th>
              <th>Тарифы</th>
            </tr>
          </thead>
          <tbody id="listings-tbody">
            @forelse ($listings as $listing)
              @php
                // Host UNP
                $hostUnp = $listing->host?->unp;
                $unpFormatStatus = 'missing';
                if (!empty($hostUnp)) {
                    if (preg_match('/^(\d{9}|[a-zA-Zа-яА-ЯёЁ]{2}\d{7})$/u', $hostUnp)) {
                        $unpFormatStatus = 'valid';
                    } else {
                        $unpFormatStatus = 'invalid';
                    }
                }
                
                // Cover image
                $imageContent = '';
                $bgStyle = 'background: #E2E8F0; color: #475569;';
                if ($listing->image && strlen($listing->image) > 4) {
                    $imageContent = '<img src="' . $listing->image . '" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">';
                    $bgStyle = 'background: none';
                } else {
                    $emoji = $listing->type === 'cottage' ? '🏡' : ($listing->id === 5 ? '🏠' : '🏢');
                    $imageContent = $emoji;
                    if ($listing->type === 'cottage') {
                        $bgStyle = 'background: #E0F2FE; color: #0284C7;';
                    } else if ($listing->id === 5) {
                        $bgStyle = 'background: #F3E8FF; color: #7E22CE;';
                    }
                }

                // Compute exact status key
                $effectiveStatus = $listing->status;
                if (($listing->status === 'pending_payment' || !$listing->is_paid) && $listing->status !== 'hidden' && $listing->status !== 'rejected') {
                    $effectiveStatus = 'pending_payment';
                }
              @endphp
              <tr id="listing-row-{{ $listing->id }}" class="listing-table-row" 
                  data-date="{{ $listing->created_at?->timestamp ?? 0 }}"
                  data-status="{{ $effectiveStatus }}"
                  data-search-term="{{ strtolower($listing->title . ' ' . $listing->address . ' ' . ($listing->host->name ?? '') . ' ' . ($listing->host->unp ?? '')) }}">
                <td>
                  <div style="font-weight: 600; color: var(--text-main); font-size: 13px;">
                    {{ $listing->created_at?->format('d.m.Y') }}
                  </div>
                  <div style="font-size: 11px; color: var(--text-muted);">
                    {{ $listing->created_at?->format('H:i') }}
                  </div>
                </td>
                <td>
                  <div style="display: flex; align-items: center; gap: 14px;">
                    <div class="col-img-wrap" style="{{ $bgStyle }}">
                      {!! $imageContent !!}
                    </div>
                    <div class="col-listing-info">
                      <h4>
                        <a href="{{ route('listings.show', ['id' => $listing->id, 'slug' => $listing->slug]) }}" target="_blank" style="color: inherit; text-decoration: none;" title="Посмотреть на сайте">
                          {{ $listing->title }} ↗
                        </a>
                      </h4>
                      <p>
                        @if(!empty($listing->address))
                          {{ $listing->address }} • 
                        @endif
                        <strong>{{ round($listing->price) }} Б̶ / ночь</strong>
                      </p>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="col-host-info">
                    {{ $listing->host->name ?? 'Неизвестный' }}
                    <span>{{ $listing->phone ?? ($listing->host->phone ?? 'Нет телефона') }}</span>
                  </div>
                </td>
                <td>
                  @if ($unpFormatStatus === 'valid')
                    <span class="badge-unp verified" title="Валидный формат УНП РБ">
                      {{ $hostUnp }} (✓)
                    </span>
                  @elseif ($unpFormatStatus === 'invalid')
                    <span class="badge-unp invalid" title="Неверный формат УНП">
                      {{ $hostUnp }} (✕)
                    </span>
                  @else
                    <span class="badge-unp">
                      Отсутствует
                    </span>
                  @endif
                </td>
                <td>
                  <span class="badge-status {{ $effectiveStatus }}" id="badge-{{ $listing->id }}">
                    @if ($effectiveStatus === 'active')
                      ✓ Опубликовано
                    @elseif ($effectiveStatus === 'pending_payment')
                      💳 Ожидает оплаты
                    @elseif ($effectiveStatus === 'pending')
                      ⏳ Ожидает
                    @elseif ($effectiveStatus === 'rejected')
                      ✕ Отклонено
                    @elseif ($effectiveStatus === 'hidden')
                      🔇 Скрыто
                    @endif
                  </span>
                </td>
                <td>
                  <div class="tariff-info-list" style="display: flex; flex-direction: column; gap: 4px;">
                    <!-- Основной тариф -->
                    <div>
                      @if ($listing->is_paid && $listing->expires_at)
                        <span class="badge-tariff active">Основной: до {{ $listing->expires_at }}</span>
                      @else
                        <span class="badge-tariff inactive">Основной: не оплачен</span>
                      @endif
                    </div>
                    <!-- Продвижение -->
                    @if ($listing->featured_expires_at)
                      <div>
                        <span class="badge-tariff active promo">Продвижение: до {{ $listing->featured_expires_at }}</span>
                      </div>
                    @endif
                    <!-- Главная страница -->
                    @if ($listing->has_homepage_tariff && $listing->homepage_expires_at)
                      <div>
                        <span class="badge-tariff active home">Главная: до {{ $listing->homepage_expires_at }}</span>
                      </div>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">
                  В базе данных нет добавленных объявлений.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tab Agency -->
    <div id="tab-agency" class="tab-content">
      <!-- Search & Status filter input -->
      <div class="filter-bar">
        <div class="search-input-wrapper">
          <input type="text" id="agency-search-input" class="search-input" placeholder="Поиск по агентским объявлениям (название, адрес, имя хозяина или УНП)...">
          <x-solar-magnifer-linear class="search-icon" width="18" height="18" />
        </div>
        <div style="min-width: 180px;">
          <select id="agency-status-filter" class="search-input" style="padding-left: 14px; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2216%22 height=%2216%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2364748B%22 stroke-width=%221.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M6 9l6 6 6-6%22/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
            <option value="">Все статусы</option>
            <option value="active">Опубликовано</option>
            <option value="pending_payment">Ожидает оплаты</option>
            <option value="pending">Ожидает</option>
            <option value="rejected">Отклонено</option>
            <option value="hidden">Скрыто</option>
          </select>
        </div>
      </div>

      <!-- Agency Listings Table Container -->
      <div class="table-container">
        <table class="admin-table" id="agency-table">
          <thead>
            <tr>
              <th class="sortable" onclick="sortTableByDate('agency-table', 0, 'data-date')">
                Дата создания <span class="sort-icon" id="sort-agency-table-0">▼</span>
              </th>
              <th>Жильё</th>
              <th>Хозяин / Контакты</th>
              <th>УНП</th>
              <th>Статус</th>
              <th>Тарифы</th>
            </tr>
          </thead>
          <tbody id="agency-tbody">
            @forelse ($agencyListings as $listing)
              @php
                // Host UNP
                $hostUnp = $listing->host?->unp;
                $unpFormatStatus = 'missing';
                if (!empty($hostUnp)) {
                    if (preg_match('/^(\d{9}|[a-zA-Zа-яА-ЯёЁ]{2}\d{7})$/u', $hostUnp)) {
                        $unpFormatStatus = 'valid';
                    } else {
                        $unpFormatStatus = 'invalid';
                    }
                }
                
                // Cover image
                $imageContent = '';
                $bgStyle = 'background: #E2E8F0; color: #475569;';
                if ($listing->image && strlen($listing->image) > 4) {
                    $imageContent = '<img src="' . $listing->image . '" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">';
                    $bgStyle = 'background: none';
                } else {
                    $emoji = $listing->type === 'cottage' ? '🏡' : ($listing->id === 5 ? '🏠' : '🏢');
                    $imageContent = $emoji;
                    if ($listing->type === 'cottage') {
                        $bgStyle = 'background: #E0F2FE; color: #0284C7;';
                    } else if ($listing->id === 5) {
                        $bgStyle = 'background: #F3E8FF; color: #7E22CE;';
                    }
                }

                // Compute exact status key
                $effectiveStatus = $listing->status;
                if (($listing->status === 'pending_payment' || !$listing->is_paid) && $listing->status !== 'hidden' && $listing->status !== 'rejected') {
                    $effectiveStatus = 'pending_payment';
                }
              @endphp
              <tr id="agency-listing-row-{{ $listing->id }}" class="agency-table-row" 
                  data-date="{{ $listing->created_at?->timestamp ?? 0 }}"
                  data-status="{{ $effectiveStatus }}"
                  data-search-term="{{ strtolower($listing->title . ' ' . $listing->address . ' ' . ($listing->host->name ?? '') . ' ' . ($listing->host->unp ?? '')) }}">
                <td>
                  <div style="font-weight: 600; color: var(--text-main); font-size: 13px;">
                    {{ $listing->created_at?->format('d.m.Y') }}
                  </div>
                  <div style="font-size: 11px; color: var(--text-muted);">
                    {{ $listing->created_at?->format('H:i') }}
                  </div>
                </td>
                <td>
                  <div style="display: flex; align-items: center; gap: 14px;">
                    <div class="col-img-wrap" style="{{ $bgStyle }}">
                      {!! $imageContent !!}
                    </div>
                    <div class="col-listing-info">
                      <h4>
                        <a href="{{ route('listings.show', ['id' => $listing->id, 'slug' => $listing->slug]) }}" target="_blank" style="color: inherit; text-decoration: none;" title="Посмотреть на сайте">
                          {{ $listing->title }} ↗
                        </a>
                      </h4>
                      <p>
                        @if(!empty($listing->address))
                          {{ $listing->address }} • 
                        @endif
                        <strong>{{ round($listing->price) }} Б̶ / ночь</strong>
                      </p>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="col-host-info">
                    {{ $listing->host->name ?? 'Неизвестный' }}
                    <span>{{ $listing->phone ?? ($listing->host->phone ?? 'Нет телефона') }}</span>
                  </div>
                </td>
                <td>
                  @if ($unpFormatStatus === 'valid')
                    <span class="badge-unp verified" title="Валидный формат УНП РБ">
                      {{ $hostUnp }} (✓)
                    </span>
                  @elseif ($unpFormatStatus === 'invalid')
                    <span class="badge-unp invalid" title="Неверный формат УНП">
                      {{ $hostUnp }} (✕)
                    </span>
                  @else
                    <span class="badge-unp">
                      Отсутствует
                    </span>
                  @endif
                </td>
                <td>
                  <span class="badge-status {{ $effectiveStatus }}" id="agency-badge-{{ $listing->id }}">
                    @if ($effectiveStatus === 'active')
                      ✓ Опубликовано
                    @elseif ($effectiveStatus === 'pending_payment')
                      💳 Ожидает оплаты
                    @elseif ($effectiveStatus === 'pending')
                      ⏳ Ожидает
                    @elseif ($effectiveStatus === 'rejected')
                      ✕ Отклонено
                    @elseif ($effectiveStatus === 'hidden')
                      🔇 Скрыто
                    @endif
                  </span>
                </td>
                <td>
                  <div class="tariff-info-list" style="display: flex; flex-direction: column; gap: 4px;">
                    <!-- Основной тариф -->
                    <div>
                      @if ($listing->is_paid && $listing->expires_at)
                        <span class="badge-tariff active">Основной: до {{ $listing->expires_at }}</span>
                      @else
                        <span class="badge-tariff inactive">Основной: не оплачен</span>
                      @endif
                    </div>
                    <!-- Продвижение -->
                    @if ($listing->featured_expires_at)
                      <div>
                        <span class="badge-tariff active promo">Продвижение: до {{ $listing->featured_expires_at }}</span>
                      </div>
                    @endif
                    <!-- Главная страница -->
                    @if ($listing->has_homepage_tariff && $listing->homepage_expires_at)
                      <div>
                        <span class="badge-tariff active home">Главная: до {{ $listing->homepage_expires_at }}</span>
                      </div>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">
                  Объявлений агентства не найдено.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tab Users -->
    <div id="tab-users" class="tab-content">
      <!-- Search input -->
      <div class="filter-bar">
        <div class="search-input-wrapper">
          <input type="text" id="user-search-input" class="search-input" placeholder="Поиск пользователей по имени, email, телефону или УНП...">
          <x-solar-magnifer-linear class="search-icon" width="18" height="18" />
        </div>
      </div>

      <!-- Users Table Container -->
      <div class="table-container">
        <table class="admin-table" id="users-table">
          <thead>
            <tr>
              <th class="sortable" onclick="sortTableByDate('users-table', 0, 'data-date')">
                Дата регистрации <span class="sort-icon" id="sort-users-table-0">▼</span>
              </th>
              <th>Пользователь</th>
              <th>Контакты</th>
              <th>Мессенджеры</th>
              <th>УНП</th>
              <th>Детальная статистика</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($users as $user)
              @php
                $unp = $user->unp;
                $unpStatus = 'missing';
                if (!empty($unp)) {
                    if (preg_match('/^(\d{9}|[a-zA-Zа-яА-ЯёЁ]{2}\d{7})$/u', $unp)) {
                        $unpStatus = 'valid';
                    } else {
                        $unpStatus = 'invalid';
                    }
                }
                
                // Format messenger links
                $tgLink = '';
                if ($user->telegram) {
                    $cleanTg = ltrim($user->telegram, '@');
                    $tgLink = 'https://t.me/' . $cleanTg;
                }
                $waLink = '';
                if ($user->whatsapp) {
                    $cleanWa = preg_replace('/\D/', '', $user->whatsapp);
                    $waLink = 'https://wa.me/' . $cleanWa;
                }
              @endphp
              <tr class="user-table-row" data-date="{{ $user->created_at?->timestamp ?? 0 }}" data-search-term="{{ strtolower($user->name . ' ' . $user->email . ' ' . ($user->phone ?? '') . ' ' . ($user->unp ?? '')) }}">
                <td>
                  <div style="font-weight: 600; color: var(--text-main); font-size: 13px;">
                    {{ $user->created_at?->format('d.m.Y') }}
                  </div>
                  <div style="font-size: 11px; color: var(--text-muted);">
                    {{ $user->created_at?->format('H:i') }}
                  </div>
                </td>
                <td>
                  <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 38px; height: 38px; border-radius: 50%; overflow: hidden; background: var(--bg); display: flex; align-items: center; justify-content: center; font-size: 16px; border: 1px solid var(--border); color: var(--text-muted); flex-shrink: 0;">
                      @if($user->avatar)
                        <img src="{{ $user->avatar }}" style="width: 100%; height: 100%; object-fit: cover;">
                      @else
                        👤
                      @endif
                    </div>
                    <div>
                      <div style="font-weight: 600; color: var(--text-main);">{{ $user->name }}</div>
                      <div style="font-size: 11px; color: var(--text-muted);">ID: {{ $user->id }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <div>
                    <a href="mailto:{{ $user->email }}" style="color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 500;">{{ $user->email }}</a>
                    @if($user->phone)
                      <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">
                        <a href="tel:{{ $user->phone }}" style="color: inherit; text-decoration: none;">{{ $user->phone }}</a>
                      </div>
                    @endif
                  </div>
                </td>
                <td>
                  <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    @if($user->telegram)
                      <a href="{{ $tgLink }}" target="_blank" class="badge-status active" style="font-size: 11px; text-decoration: none; padding: 3px 8px; border-radius: 6px;">
                        Telegram: {{ $user->telegram }}
                      </a>
                    @endif
                    @if($user->viber)
                      <span class="badge-status hidden" style="font-size: 11px; padding: 3px 8px; border-radius: 6px;">
                        Viber: {{ $user->viber }}
                      </span>
                    @endif
                    @if($user->whatsapp)
                      <a href="{{ $waLink }}" target="_blank" class="badge-status active" style="font-size: 11px; text-decoration: none; padding: 3px 8px; border-radius: 6px;">
                        WhatsApp: {{ $user->whatsapp }}
                      </a>
                    @endif
                    @if(!$user->telegram && !$user->viber && !$user->whatsapp)
                      <span style="color: var(--text-muted); font-size: 12px;">—</span>
                    @endif
                  </div>
                </td>
                <td>
                  @if ($unpStatus === 'valid')
                    <span class="badge-status active" title="Валидный формат УНП РБ">
                      ✓ Валидный ({{ $unp }})
                    </span>
                  @elseif ($unpStatus === 'invalid')
                    <span class="badge-status rejected" title="Неверный формат УНП РБ">
                      ✕ Неверный формат ({{ $unp }})
                    </span>
                  @else
                    <span class="badge-status hidden">Отсутствует</span>
                  @endif
                </td>
                <td>
                  @if($user->has_detailed_stats)
                    <span class="badge-status active">✓ Активна</span>
                  @else
                    <span class="badge-status hidden">Отсутствует</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 40px;">
                  Нет зарегистрированных пользователей.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tab Transactions -->
    <div id="tab-transactions" class="tab-content">
      <!-- Search and Filter input -->
      <div class="filter-bar">
        <div class="search-input-wrapper">
          <input type="text" id="tx-search-input" class="search-input" placeholder="Поиск транзакций по хозяину, объявлению или промокоду...">
          <x-solar-magnifer-linear class="search-icon" width="18" height="18" />
        </div>
        <div style="min-width: 180px;">
          <select id="tx-status-filter" class="search-input" style="padding-left: 14px; cursor: pointer; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2216%22 height=%2216%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2364748B%22 stroke-width=%221.5%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M6 9l6 6 6-6%22/></svg>'); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px;">
            <option value="">Все статусы</option>
            <option value="completed">Успешно</option>
            <option value="pending">Ожидает</option>
            <option value="failed">Ошибка</option>
          </select>
        </div>
      </div>

      <!-- Transactions Table Container -->
      <div class="table-container">
        <table class="admin-table" id="transactions-table">
          <thead>
            <tr>
              <th class="sortable" onclick="sortTableByDate('transactions-table', 0, 'data-date')">
                ID / Дата <span class="sort-icon" id="sort-transactions-table-0">▼</span>
              </th>
              <th>Хозяин</th>
              <th>Объявление</th>
              <th>Тариф</th>
              <th>Сумма</th>
              <th>Скидка / Промокод</th>
              <th>Статус</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($payments as $payment)
              @php
                $txStatusText = 'Ожидает';
                if ($payment->status === 'completed') {
                    $txStatusText = 'Успешно';
                } elseif ($payment->status === 'failed') {
                    $txStatusText = 'Ошибка';
                }
              @endphp
              <tr class="tx-table-row" data-date="{{ $payment->created_at?->timestamp ?? 0 }}" data-status="{{ $payment->status }}" data-search-term="{{ strtolower(($payment->user?->name ?? '') . ' ' . ($payment->listing?->title ?? '') . ' ' . ($payment->promocode?->code ?? '')) }}">
                <td>
                  <div style="font-weight: 600; color: var(--text-main);">#{{ $payment->id }}</div>
                  <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">{{ $payment->created_at?->format('d.m.Y H:i') }}</div>
                </td>
                <td>
                  <div style="font-weight: 600; color: var(--text-main);">{{ $payment->user?->name ?? 'Неизвестный' }}</div>
                  <div style="font-size: 11px; color: var(--text-muted);">{{ $payment->user?->email }}</div>
                </td>
                <td>
                  @if($payment->listing)
                    <a href="{{ route('listings.show', ['id' => $payment->listing->id, 'slug' => $payment->listing->slug]) }}" target="_blank" style="color: var(--primary); text-decoration: none; font-weight: 500; font-size: 13px;">
                      {{ $payment->listing->title }}
                    </a>
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">ID: {{ $payment->listing->id }}</div>
                  @else
                    <span style="color: var(--text-muted); font-size: 13px;">Удалено</span>
                  @endif
                </td>
                <td>
                  <div style="font-weight: 500; font-size: 13px; color: var(--text-main);">
                    {{ $payment->tariff?->name ?? 'Неизвестный тариф' }}
                  </div>
                </td>
                <td>
                  <div style="font-weight: 700; color: var(--text-main);">{{ number_format($payment->amount, 2) }} {{ $payment->currency ?: 'BYN' }}</div>
                </td>
                <td>
                  @if ($payment->promocode)
                    <div style="font-size: 13px; color: var(--emerald); font-weight: 600;">
                      {{ $payment->promocode->code }}
                    </div>
                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                      Скидка: {{ number_format($payment->discount_amount, 2) }} {{ $payment->currency ?: 'BYN' }}
                    </div>
                  @else
                    <span style="color: var(--text-muted); font-size: 13px;">—</span>
                  @endif
                </td>
                <td>
                  <span class="badge-status {{ $payment->status }}">
                    @if ($payment->status === 'completed')
                      ✓ Успешно
                    @elseif ($payment->status === 'pending')
                      ⏳ Ожидает
                    @elseif ($payment->status === 'failed')
                      ✕ Ошибка
                    @endif
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">
                  Нет зафиксированных транзакций.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tab Analytics -->
    <div id="tab-analytics" class="tab-content">
      <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
        <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; padding: 20px;">
          <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Сегодня (Уникальные)</div>
          <div style="font-family: 'Outfit', sans-serif; font-size: 32px; font-weight: 800; color: var(--primary);">{{ $visitorStats['unique_today'] }}</div>
          <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">{{ $visitorStats['views_today'] }} просмотров страниц</div>
        </div>

        <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; padding: 20px;">
          <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">За 7 дней</div>
          <div style="font-family: 'Outfit', sans-serif; font-size: 32px; font-weight: 800; color: var(--emerald);">{{ $visitorStats['unique_7days'] }}</div>
          <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">уникальных гостей</div>
        </div>

        <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; padding: 20px;">
          <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">За 30 дней</div>
          <div style="font-family: 'Outfit', sans-serif; font-size: 32px; font-weight: 800; color: var(--amber);">{{ $visitorStats['unique_30days'] }}</div>
          <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">уникальных гостей</div>
        </div>

        @php
          $totalDevices = array_sum($visitorStats['devices']) ?: 1;
          $mobilePct = round(($visitorStats['devices']['mobile'] / $totalDevices) * 100);
          $desktopPct = round(($visitorStats['devices']['desktop'] / $totalDevices) * 100);
          $tabletPct = round(($visitorStats['devices']['tablet'] / $totalDevices) * 100);
        @endphp
        <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; padding: 20px;">
          <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px;">Устройства (за 7 дней)</div>
          <div style="display: flex; flex-direction: column; gap: 6px; font-size: 13px; margin-top: 8px;">
            <div style="display: flex; justify-content: space-between;">
              <span>📱 Мобильные:</span> <strong>{{ $mobilePct }}%</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
              <span>💻 Компьютеры:</span> <strong>{{ $desktopPct }}%</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
              <span>📑 Планшеты:</span> <strong>{{ $tabletPct }}%</strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Listings by City stats -->
      <div class="table-container" style="padding: 24px; margin-bottom: 24px;">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 700; margin-bottom: 20px; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
          🏙️ Количество объявлений по городам
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
          @php
            $totalActiveListings = 0;
            foreach ($cityStats as $stats) {
                $totalActiveListings += $stats['active'];
            }
            if ($totalActiveListings === 0) {
                $totalActiveListings = 1;
            }
          @endphp
          @forelse($cityStats as $cityName => $stats)
            @php
              $activeCount = $stats['active'];
              $totalCount = $stats['total'];
              
              $activePercentage = round(($activeCount / $totalActiveListings) * 100, 1);
              
              // Pluralization logic for total
              $totalPlural = 'объявлений';
              $mod10Total = $totalCount % 10;
              $mod100Total = $totalCount % 100;
              if ($mod10Total == 1 && $mod100Total != 11) {
                  $totalPlural = 'объявление';
              } elseif (($mod10Total >= 2 && $mod10Total <= 4) && ($mod100Total < 12 || $mod100Total > 14)) {
                  $totalPlural = 'объявления';
              }
            @endphp
            <div style="background: rgba(248, 250, 252, 0.5); border: 1px solid var(--border); border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 10px;">
              <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span style="font-weight: 700; color: var(--text-main); font-size: 15px; margin-top: 2px;">{{ $cityName }}</span>
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                  <span style="background: var(--emerald-light, #ECFDF5); color: var(--emerald); padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                    <span style="display: inline-block; width: 6px; height: 6px; background: var(--emerald); border-radius: 50%;"></span>
                    {{ $activeCount }} {{ $activeCount === 1 ? 'активное' : 'активных' }}
                  </span>
                  <span style="font-size: 11px; color: var(--text-muted); font-weight: 600; padding-right: 4px;">
                    всего {{ $totalCount }} {{ $totalPlural }}
                  </span>
                </div>
              </div>
              <div style="display: flex; align-items: center; gap: 8px; margin-top: auto;">
                <div style="flex: 1; background: var(--border); height: 8px; border-radius: 10px; overflow: hidden;">
                  <div style="background: var(--emerald); width: {{ $activePercentage }}%; height: 100%; border-radius: 10px;"></div>
                </div>
                <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); min-width: 36px; text-align: right;">{{ $activePercentage }}%</span>
              </div>
            </div>
          @empty
            <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 20px;">
              Нет объявлений.
            </div>
          @endforelse
        </div>
      </div>

      <!-- Chart / Visits by Day Table -->
      <div class="table-container" style="padding: 24px; margin-bottom: 24px;">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 700; margin-bottom: 20px; color: var(--text-main);">
          📊 Посещаемость по дням за последние 14 дней
        </h3>

        <div style="display: flex; align-items: flex-end; gap: 12px; height: 180px; padding: 20px 10px 10px; border-bottom: 1px solid var(--border); margin-bottom: 20px; overflow-x: auto;">
          @php
            $maxVisitors = 1;
            foreach ($visitorStats['chart'] as $day) {
                if ($day['visitors'] > $maxVisitors) $maxVisitors = $day['visitors'];
            }
          @endphp
          @foreach($visitorStats['chart'] as $day)
            @php
              $heightPct = max(8, round(($day['visitors'] / $maxVisitors) * 100));
            @endphp
            <div style="flex: 1; min-width: 40px; display: flex; flex-direction: column; align-items: center; gap: 6px; height: 100%; justify-content: flex-end;">
              <div style="font-size: 11px; font-weight: 700; color: var(--primary);">{{ $day['visitors'] }}</div>
              <div style="width: 100%; background: linear-gradient(180deg, var(--primary) 0%, #818CF8 100%); height: {{ $heightPct }}%; border-radius: 6px 6px 0 0; transition: height 0.3s ease;" title="{{ $day['label'] }}: {{ $day['visitors'] }} уникальных ({{ $day['pageviews'] }} просмотров)"></div>
              <div style="font-size: 11px; color: var(--text-muted); font-weight: 600;">{{ $day['label'] }}</div>
            </div>
          @endforeach
        </div>

        <table class="admin-table">
          <thead>
            <tr>
              <th>Дата</th>
              <th>Уникальные посетители (IP)</th>
              <th>Всего просмотров страниц</th>
            </tr>
          </thead>
          <tbody>
            @foreach(array_reverse($visitorStats['chart']) as $day)
              <tr>
                <td style="font-weight: 600;">{{ $day['label'] }} ({{ $day['date'] }})</td>
                <td><strong style="color: var(--primary);">{{ $day['visitors'] }}</strong> чел.</td>
                <td>{{ $day['pageviews'] }} просм.</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tab Banners -->
    <div id="tab-banners" class="tab-content">
      <!-- Banner Management Section -->
      <div style="background: var(--card-bg); border: 1px solid var(--border); border-radius: 16px; padding: 28px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
        <h2 style="font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 20px; color: var(--text-main); margin-bottom: 6px;">Управление баннерами на главной</h2>
        <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 24px;">Загрузите новые изображения для обновления слайдов карусели на главной странице (рекомендуемое разрешение: 1920x480, форматы: JPG, PNG, WEBP, макс. 5МБ).</p>
        
        @if(session('success'))
          <div style="background: var(--emerald-light); border: 1px solid var(--emerald-border); color: var(--emerald); padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 600;">
            {{ session('success') }}
          </div>
        @endif
        @if(session('error'))
          <div style="background: var(--rose-light); border: 1px solid var(--rose-border); color: var(--rose); padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; font-weight: 600;">
            {{ session('error') }}
          </div>
        @endif
        @if($errors->any())
          <div style="background: var(--rose-light); border: 1px solid var(--rose-border); color: var(--rose); padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 14px;">
            <ul style="margin: 0; padding-left: 20px;">
              @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form action="{{ route('admin.banners.update') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="banner-upload-grid">
            <!-- Slide 1 -->
            <div style="background: var(--bg); border: 1px solid var(--border); border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 12px;">
              <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Слайд 1: Тарифы</span>
              <div style="width: 100%; aspect-ratio: 2.5; border-radius: 8px; overflow: hidden; background: #E2E8F0; border: 1px solid var(--border);">
                <img src="/images/banners/flatly_tariffs_banner.png?v={{ time() }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Слайд 1">
              </div>
              <input type="file" name="banner1" accept="image/*" style="font-size: 12px; color: var(--text-muted); width: 100%;">
            </div>

            <!-- Slide 2 -->
            <div style="background: var(--bg); border: 1px solid var(--border); border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 12px;">
              <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Слайд 2: Размещение за 0 Б̶</span>
              <div style="width: 100%; aspect-ratio: 2.5; border-radius: 8px; overflow: hidden; background: #E2E8F0; border: 1px solid var(--border);">
                <img src="/images/banners/flatly_free_placement_banner.png?v={{ time() }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Слайд 2">
              </div>
              <input type="file" name="banner2" accept="image/*" style="font-size: 12px; color: var(--text-muted); width: 100%;">
            </div>

            <!-- Slide 3 -->
            <div style="background: var(--bg); border: 1px solid var(--border); border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 12px;">
              <span style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Слайд 3: Минск до 150 Б̶</span>
              <div style="width: 100%; aspect-ratio: 2.5; border-radius: 8px; overflow: hidden; background: #E2E8F0; border: 1px solid var(--border);">
                <img src="/images/banners/flatly_minsk_apartments_banner.png?v={{ time() }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Слайд 3">
              </div>
              <input type="file" name="banner3" accept="image/*" style="font-size: 12px; color: var(--text-muted); width: 100%;">
            </div>
          </div>

          <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
            <button type="submit" class="tab-btn active" style="padding: 10px 24px; border-radius: 10px; font-size: 14px; display: inline-flex; align-items: center; gap: 8px;">
              <x-solar-diskette-linear width="16" height="16" style="flex-shrink:0;" />
              Сохранить изменения баннеров
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Toast system -->
  <div id="toast" class="toast">📢 Данные успешно обновлены!</div>

  <script>
    const sortStates = {};

    // Universal Date Sorting for Tables
    function sortTableByDate(tableId, colIndex, attrName = 'data-date') {
      const table = document.getElementById(tableId);
      if (!table) return;
      const tbody = table.querySelector('tbody');
      if (!tbody) return;

      const rows = Array.from(tbody.querySelectorAll('tr'));
      if (rows.length === 0) return;

      // Toggle sort direction (default to descending on first click if not set)
      const currentDir = sortStates[tableId] || 'desc';
      const newDir = currentDir === 'desc' ? 'asc' : 'desc';
      sortStates[tableId] = newDir;

      // Update sort icon indicator
      const icon = document.getElementById(`sort-${tableId}-${colIndex}`);
      if (icon) {
        icon.textContent = newDir === 'desc' ? '▼' : '▲';
      }

      rows.sort((a, b) => {
        const valA = parseInt(a.getAttribute(attrName) || 0, 10);
        const valB = parseInt(b.getAttribute(attrName) || 0, 10);

        return newDir === 'desc' ? valB - valA : valA - valB;
      });

      rows.forEach(row => tbody.appendChild(row));
    }
    
    // Tab switching functionality
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        this.classList.add('active');
        const tabName = this.getAttribute('data-tab');
        document.getElementById(`tab-${tabName}`).classList.add('active');
      });
    });

    // Auto-switch to Banners tab if session status exists
    @if(session('success') || session('error') || $errors->any())
      document.addEventListener('DOMContentLoaded', function() {
        const bannerBtn = document.querySelector('.tab-btn[data-tab="banners"]');
        if (bannerBtn) bannerBtn.click();
      });
    @endif

    // Metric Cards Filter Function
    function filterByMetric(status) {
      // Ensure we are on listings tab
      const listingTabBtn = document.querySelector('.tab-btn[data-tab="listings"]');
      if (listingTabBtn) listingTabBtn.click();
      
      const statusSelect = document.getElementById('listing-status-filter');
      if (statusSelect) {
        statusSelect.value = status === 'all' ? '' : status;
        filterListings();
      }

      // Highlight metric card active state
      document.querySelectorAll('.metric-card').forEach(card => card.classList.remove('active-filter'));
      if (event && event.currentTarget) {
        event.currentTarget.classList.add('active-filter');
      }
    }

    // Live Search & Status Filter for Listings
    const searchInput = document.getElementById('search-input');
    const listingStatusFilter = document.getElementById('listing-status-filter');

    function filterListings() {
      const val = searchInput ? searchInput.value.toLowerCase().trim() : '';
      const status = listingStatusFilter ? listingStatusFilter.value : '';

      document.querySelectorAll('.listing-table-row').forEach(row => {
        const term = row.getAttribute('data-search-term');
        const rowStatus = row.getAttribute('data-status');

        const matchesSearch = term.includes(val);
        const matchesStatus = status === '' || rowStatus === status;

        if (matchesSearch && matchesStatus) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    if (searchInput) searchInput.addEventListener('input', filterListings);
    if (listingStatusFilter) listingStatusFilter.addEventListener('change', filterListings);

    // Live Search & Status Filter for Agency Listings
    const agencySearchInput = document.getElementById('agency-search-input');
    const agencyStatusFilter = document.getElementById('agency-status-filter');

    function filterAgencyListings() {
      const val = agencySearchInput ? agencySearchInput.value.toLowerCase().trim() : '';
      const status = agencyStatusFilter ? agencyStatusFilter.value : '';

      document.querySelectorAll('.agency-table-row').forEach(row => {
        const term = row.getAttribute('data-search-term');
        const rowStatus = row.getAttribute('data-status');

        const matchesSearch = term.includes(val);
        const matchesStatus = status === '' || rowStatus === status;

        if (matchesSearch && matchesStatus) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }

    if (agencySearchInput) agencySearchInput.addEventListener('input', filterAgencyListings);
    if (agencyStatusFilter) agencyStatusFilter.addEventListener('change', filterAgencyListings);

    // Live Search for Users
    const userSearchInput = document.getElementById('user-search-input');
    if (userSearchInput) {
      userSearchInput.addEventListener('input', function() {
        const val = this.value.toLowerCase().trim();
        document.querySelectorAll('.user-table-row').forEach(row => {
          const term = row.getAttribute('data-search-term');
          if (term.includes(val)) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });
      });
    }

    // Live Search and Filtering for Transactions
    const txSearchInput = document.getElementById('tx-search-input');
    const txStatusFilter = document.getElementById('tx-status-filter');
    
    function filterTransactions() {
      const val = txSearchInput ? txSearchInput.value.toLowerCase().trim() : '';
      const status = txStatusFilter ? txStatusFilter.value : '';
      
      document.querySelectorAll('.tx-table-row').forEach(row => {
        const term = row.getAttribute('data-search-term');
        const rowStatus = row.getAttribute('data-status');
        
        const matchesSearch = term.includes(val);
        const matchesStatus = status === '' || rowStatus === status;
        
        if (matchesSearch && matchesStatus) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    }
    
    if (txSearchInput) txSearchInput.addEventListener('input', filterTransactions);
    if (txStatusFilter) txStatusFilter.addEventListener('change', filterTransactions);
  </script>
</body>
</html>
