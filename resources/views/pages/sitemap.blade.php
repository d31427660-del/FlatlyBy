@extends('layouts.app')

@section('title', 'Карта сайта — Flatly.by')

@section('head')
<style>
.sitemap-container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 40px 24px 80px;
  color: var(--slate-900);
}
.sitemap-header {
  text-align: center;
  margin-bottom: 48px;
}
.sitemap-header h1 {
  font-family: 'Syne', sans-serif;
  font-size: 36px;
  font-weight: 800;
  color: var(--slate-900);
  margin-bottom: 12px;
  letter-spacing: -0.5px;
}
.sitemap-header p {
  font-size: 16px;
  color: var(--gray-text);
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.6;
}
.sitemap-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}
.sitemap-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
}
.sitemap-card.full-width {
  grid-column: span 3;
}
.sitemap-card h2 {
  font-size: 18px;
  font-weight: 700;
  color: var(--slate-900);
  margin-bottom: 20px;
  border-bottom: 1px solid var(--slate-50);
  padding-bottom: 10px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.sitemap-card h2 svg {
  color: var(--amber);
}
.sitemap-links {
  list-style: none;
  padding-left: 0;
  margin-bottom: 0;
}
.sitemap-links li {
  margin-bottom: 12px;
  font-size: 14px;
}
.sitemap-links li:last-child {
  margin-bottom: 0;
}
.sitemap-links a {
  color: var(--slate-700);
  text-decoration: none;
  transition: color 0.2s ease;
  display: inline-block;
}
.sitemap-links a:hover {
  color: var(--slate-900);
  text-decoration: underline;
}
.cities-subgrid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-top: 8px;
}
.blog-subgrid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}
.blog-sitemap-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.blog-sitemap-item span {
  font-size: 12px;
  color: var(--gray-text);
}

@media (max-width: 991px) {
  .sitemap-grid {
    grid-template-columns: 1fr 1fr;
  }
  .sitemap-card.full-width {
    grid-column: span 2;
  }
  .cities-subgrid {
    grid-template-columns: repeat(3, 1fr);
  }
}
@media (max-width: 768px) {
  .sitemap-grid {
    grid-template-columns: 1fr;
  }
  .sitemap-card.full-width {
    grid-column: span 1;
  }
  .cities-subgrid {
    grid-template-columns: repeat(2, 1fr);
  }
  .blog-subgrid {
    grid-template-columns: 1fr;
  }
  .sitemap-card {
    padding: 24px;
  }
}
</style>
@endsection

@section('content')
<div class="sitemap-container">
  
  <header class="sitemap-header">
    <h1>Карта сайта</h1>
    <p>Навигация по разделам, городам и статьям блога Flatly.by. Найдите нужную информацию в один клик.</p>
  </header>

  <div class="sitemap-grid">
    
    <!-- 1. Основные разделы -->
    <div class="sitemap-card">
      <h2>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Основные разделы
      </h2>
      <ul class="sitemap-links">
        <li><a href="{{ route('home') }}">Главная страница</a></li>
        <li><a href="{{ route('listings.apartments') }}">Квартиры посуточно</a></li>
        <li><a href="{{ route('listings.cottages') }}">Коттеджи и усадьбы</a></li>
        <li><a href="{{ route('listings.hourly') }}">Почасовая аренда</a></li>
        <li><a href="{{ route('listings.favorites') }}">Избранные объявления</a></li>
        <li><a href="{{ route('listings.create') }}">Подать объявление (бесплатно)</a></li>
        <li><a href="{{ route('login') }}">Личный кабинет / Авторизация</a></li>
        <li><a href="{{ route('blog.index') }}">Блог и гид по Беларуси</a></li>
      </ul>
    </div>

    <!-- 2. Помощь и информация -->
    <div class="sitemap-card">
      <h2>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Помощь и инфо
      </h2>
      <ul class="sitemap-links">
        <li><a href="{{ route('how-it-works') }}">Как работает сервис</a></li>
        <li><a href="{{ route('rules') }}">Правила размещения объявлений</a></li>
        <li><a href="{{ route('contacts') }}">Контакты и поддержка</a></li>
        <li><a href="{{ route('privacy') }}">Политика конфиденциальности</a></li>
        <li><a href="{{ route('privacy') }}#offer">Договор публичной оферты</a></li>
        <li><a href="{{ route('privacy') }}#cookies">Политика файлов Cookie</a></li>
        <li><a href="/sitemap.xml" target="_blank">XML Sitemap (для поисковиков)</a></li>
      </ul>
    </div>

    <!-- 3. Популярные типы -->
    <div class="sitemap-card">
      <h2>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
        Поиск по категориям
      </h2>
      <ul class="sitemap-links">
        <li><a href="/apartments">Квартиры посуточно во всех городах</a></li>
        <li><a href="/cottages">Коттеджи посуточно во всех городах</a></li>
        <li><a href="/hourly">Почасовая аренда во всех городах</a></li>
        <li><a href="/?rooms=1">1-комнатные квартиры</a></li>
        <li><a href="/?rooms=2">2-комнатные квартиры</a></li>
        <li><a href="/?rooms=3">3-комнатные квартиры</a></li>
        <li><a href="/?rooms=4">4+ комнатные квартиры</a></li>
      </ul>
    </div>

    <!-- 4. Города (Широкая карточка) -->
    <div class="sitemap-card full-width">
      <h2>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        Города с объявлениями
      </h2>
      <p style="font-size: 13.5px; color: var(--gray-text); margin-bottom: 16px;">Выберите интересующий вас город для поиска прямых контактов арендодателей:</p>
      <div class="cities-subgrid">
        @php
          $bigCities = ['Минск', 'Брест', 'Гродно', 'Витебск', 'Гомель', 'Могилев'];
        @endphp
        
        <!-- Крупные областные центры -->
        @foreach($bigCities as $c)
          <div style="font-weight: 700;">
            <a href="/{{ \App\Helpers\CityHelper::slugify($c) }}" style="color: var(--slate-900); text-decoration: none;">{{ $c }}</a>
          </div>
        @endforeach

        <!-- Другие города, где есть объявления -->
        @foreach($activeCities as $city)
          @if(!in_array($city, $bigCities) && $city !== 'Вся Беларусь')
            <div>
              <a href="/{{ \App\Helpers\CityHelper::slugify($city) }}" style="color: var(--slate-700); text-decoration: none; font-size: 13.5px;">{{ $city }}</a>
            </div>
          @endif
        @endforeach
      </div>
    </div>

    <!-- 5. Статьи блога (Широкая карточка) -->
    @if($blogPosts->count() > 0)
      <div class="sitemap-card full-width">
        <h2>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
          Гид по Беларуси (Последние статьи)
        </h2>
        <div class="blog-subgrid">
          @foreach($blogPosts as $post)
            <div class="blog-sitemap-item">
              <a href="{{ route('blog.show', ['slug' => $post->slug]) }}" style="font-weight: 600; font-size: 14px; color: var(--slate-800); text-decoration: none;">
                {{ $post->title }}
              </a>
              @if($post->created_at)
                <span>{{ $post->created_at->translatedFormat('j F Y') }}</span>
              @endif
            </div>
          @endforeach
        </div>
      </div>
    @endif

  </div>
</div>
@endsection
