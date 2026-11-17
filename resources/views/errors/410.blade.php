@extends('layouts.app')

@php
  $type = $type ?? 'listing';
  $featuredListings = $featuredListings ?? collect();
  $recentPosts = $recentPosts ?? collect();

  // Загружаем резервные премиум-объявления, если коллекция пуста
  if ($type === 'listing' && $featuredListings->isEmpty()) {
      try {
          $featuredListings = \App\Models\Listing::where('status', 'active')
              ->whereHas('userTariffs', function($query) {
                  $query->where('tariff_id', 4)
                        ->where('status', 'active')
                        ->where('expires_at', '>', now());
              })
              ->inRandomOrder()
              ->take(5)
              ->get();
          
          if ($featuredListings->isEmpty()) {
              $featuredListings = \App\Models\Listing::where('status', 'active')
                  ->inRandomOrder()
                  ->take(5)
                  ->get();
          }
      } catch (\Exception $e) {
          $featuredListings = collect();
      }
  }

  if ($type === 'blog' && $recentPosts->isEmpty()) {
      try {
          $recentPosts = \App\Models\BlogPost::where('source_name', '!=', '34travel.me')
              ->orWhereNull('source_name')
              ->orderBy('created_at', 'desc')
              ->take(3)
              ->get();
      } catch (\Exception $e) {
          $recentPosts = collect();
      }
  }
@endphp

@section('title', $type === 'blog' ? 'Статья удалена или больше не существует — Flatly.by' : 'Объявление больше неактуально — Flatly.by')

@section('head')
<style>
  .error-410-container {
    max-width: 1400px;
    margin: 40px auto 80px;
    padding: 0 32px;
    text-align: center;
  }

  .error-card {
    background: var(--slate-50);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 56px 32px;
    max-width: 800px;
    margin: 0 auto 56px;
    box-shadow: 0 8px 32px rgba(26, 26, 46, 0.02);
  }

  .error-icon {
    font-size: 64px;
    margin-bottom: 24px;
    display: inline-block;
    animation: pulse 2s infinite ease-in-out;
  }

  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.08); }
    100% { transform: scale(1); }
  }

  .error-title {
    font-family: 'Syne', 'Inter', sans-serif;
    font-weight: 800;
    font-size: 32px;
    color: var(--slate-900);
    letter-spacing: -0.5px;
    margin-bottom: 16px;
    line-height: 1.2;
  }

  .error-desc {
    font-size: 16px;
    color: var(--gray-text);
    line-height: 1.6;
    margin-bottom: 32px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
  }

  .error-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--slate-900);
    color: var(--white);
    padding: 12px 28px;
    border-radius: 30px;
    font-weight: 700;
    font-size: 14px;
    transition: all 0.2s ease;
    text-decoration: none;
  }

  .error-btn:hover {
    background: var(--amber);
    color: var(--slate-900);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245, 166, 35, 0.2);
  }

  .error-suggestions-title {
    font-family: 'Syne', sans-serif;
    font-size: 24px;
    font-weight: 800;
    color: var(--slate-900);
    margin-bottom: 32px;
    text-align: left;
    border-bottom: 2px solid var(--border);
    padding-bottom: 12px;
  }

  /* ===== CARD STYLES COPIED FROM INDEX.BLADE.PHP ===== */
  .listings-home-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 20px;
    width: 100%;
    box-sizing: border-box;
    text-align: left;
  }

  .card {
    cursor: pointer;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
    display: flex;
    flex-direction: column;
    width: 100%;
    box-sizing: border-box;
    height: 100%;
  }
  .card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    border-color: var(--slate-300);
  }
  .card-img {
    border-radius: 12px; overflow: hidden;
    aspect-ratio: 1.1; position: relative;
    background: var(--slate-100);
    width: 100%;
  }
  .card-photo {
    width: 100%; height: 100%;
    object-fit: cover; display: block;
  }
  .card-photo-fake {
    width: 100%; height: 100%;
    display: flex; align-items: center;
    justify-content: center;
    font-size: 72px;
  }
  .fav-btn {
    position: absolute; top: 12px; right: 12px;
    background: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 12;
    transition: transform 0.15s, background-color 0.15s;
  }
  .fav-btn:hover {
    transform: scale(1.08);
    background: rgba(0, 0, 0, 0.4);
  }
  .fav-btn svg {
    width: 16px;
    height: 16px;
    stroke: #ffffff;
    fill: none;
    stroke-width: 2.5;
    transition: fill 0.15s, stroke 0.15s;
  }
  .fav-btn.active svg {
    fill: var(--red);
    stroke: var(--red);
  }

  .card.highlighted {
    background: #FFF9E6;
    border-color: #F5A623;
    box-shadow: 0 4px 16px rgba(245, 166, 35, 0.06);
  }
  .card.highlighted:hover {
    border-color: #D4890A;
    box-shadow: 0 10px 24px rgba(245, 166, 35, 0.12);
  }

  .card-badge-container {
    position: absolute;
    top: 12px;
    right: 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    z-index: 12;
  }
  .card-badge {
    border-radius: 20px;
    font-size: 10px;
    font-weight: 800;
    padding: 4px 10px;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 3px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.12);
  }
  .card-badge.hit {
    background: linear-gradient(135deg, #FF385C, #FF7B00);
  }
  .card-badge.best {
    background: linear-gradient(135deg, #F5A623, #D4890A);
  }
  .card-badge.sale {
    background: linear-gradient(135deg, #10B981, #059669);
  }

  .card-carousel {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
    border-radius: 14px;
  }
  .card-carousel-track {
    display: flex;
    width: 100%;
    height: 100%;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .card-carousel-slide {
    flex: 0 0 100%;
    width: 100%;
    height: 100%;
    position: relative;
  }
  .carousel-arrow {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.9);
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.2s, background 0.2s, transform 0.15s;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    z-index: 10;
  }
  .carousel-arrow:hover {
    background: #ffffff;
    transform: translateY(-50%) scale(1.05);
  }
  .card-img:hover .carousel-arrow {
    opacity: 1;
  }
  .carousel-arrow.prev {
    left: 10px;
  }
  .carousel-arrow.next {
    right: 10px;
  }
  .carousel-arrow svg {
    width: 16px;
    height: 16px;
    stroke: var(--slate-900);
    stroke-width: 2.5;
    fill: none;
    stroke-linecap: round;
    stroke-linejoin: round;
  }
  .carousel-dots {
    position: absolute;
    bottom: 12px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 6px;
    z-index: 10;
    background: rgba(0, 0, 0, 0.2);
    padding: 4px 8px;
    border-radius: 10px;
    backdrop-filter: blur(4px);
  }
  .carousel-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.45);
    transition: all 0.2s ease;
    cursor: pointer;
  }
  .carousel-dot.active {
    background: #ffffff;
    transform: scale(1.2);
  }

  .card-body {
    margin-top: 11px;
    display: flex;
    flex-direction: column;
    flex: 1;
  }
  .cb-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
  .cb-title { font-size: 15px; font-weight: 600; color: var(--slate-900); line-height: 1.35; flex: 1; text-align: left; }
  .cb-rating { display: flex; align-items: center; gap: 3px; font-size: 14px; font-weight: 600; white-space: nowrap; color: var(--slate-900); }
  .cb-rating svg { width: 13px; height: 13px; fill: var(--slate-900); }
  .cb-rating span { color: var(--gray-text); font-weight: 400; }
  .cb-sub { font-size: 13px; color: var(--gray-text); margin-top: 3px; text-align: left; }
  .cb-dates { font-size: 13px; color: var(--gray-text); margin-top: 2px; text-align: left; }

  .card-rating-pill {
    display: inline-flex;
    align-items: center;
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 3px 3px 3px 10px;
    background: var(--white);
    flex-shrink: 0;
    gap: 8px;
  }
  .rating-text {
    font-size: 11px;
    color: #4a5568;
    font-weight: 700;
  }
  .rating-badge {
    background: var(--amber);
    color: var(--slate-900);
    font-size: 11px;
    font-weight: 700;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .card-address-row, .card-metro-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    color: #4A5568;
    margin-top: 6px;
    font-weight: 500;
    text-align: left;
  }
  .card-address-row svg, .card-metro-row svg {
    width: 15px;
    height: 15px;
    flex-shrink: 0;
  }
  .address-icon {
    stroke: #4A5568;
    fill: none;
    stroke-width: 2.2;
  }

  .card-price-box {
    display: flex;
    background: #f7fafc;
    border: 1px solid rgba(0,0,0,0.02);
    border-radius: 12px;
    padding: 8px 12px;
    margin-top: auto;
    align-items: center;
    width: 100%;
    box-sizing: border-box;
  }
  .price-section {
    flex: 1;
    display: flex;
    flex-direction: column;
  }
  .price-section.left {
    padding-right: 6px;
    align-items: flex-start;
  }
  .price-section.right {
    padding-left: 12px;
    align-items: flex-end;
    text-align: right;
  }
  .price-divider {
    width: 1px;
    height: 32px;
    background: #e2e8f0;
  }
  .price-label {
    display: flex;
    align-items: baseline;
    gap: 3px;
  }
  .price-prefix {
    font-size: 12px;
    color: #4A5568;
    font-weight: 700;
  }
  .price-amount {
    font-size: 15px;
    font-weight: 800;
    color: var(--amber-dark);
  }
  .price-section.right .price-amount {
    color: #2d3748;
  }
  .price-amount.no-hourly {
    color: #4A5568;
    font-size: 12px;
    font-weight: 700;
  }
  .price-period {
    font-size: 11px;
    color: #4A5568;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 3px;
    margin-top: 1px;
  }
  .info-icon {
    font-size: 11px;
    color: #4A5568;
    cursor: help;
    display: inline-block;
  }

  .card-badges-row {
    display: flex;
    gap: 6px;
    margin-top: 10px;
    overflow-x: auto;
    scrollbar-width: none;
    align-items: center;
    width: 100%;
    -webkit-overflow-scrolling: touch;
  }
  .card-badges-row::-webkit-scrollbar { display: none; }
  .badge-pill {
    border: 1.5px solid #e2e8f0;
    border-radius: 20px;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 600;
    color: #4a5568;
    background: var(--white);
    white-space: nowrap;
    flex-shrink: 0;
  }
  .badge-pill.map-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    transition: border-color 0.15s, background-color 0.15s;
  }
  .badge-pill.map-pill:hover {
    border-color: var(--amber);
    background: #fffbeb;
  }
  .badge-pill.map-pill svg {
    width: 12px;
    height: 12px;
    fill: var(--amber-dark);
  }

  .contact-block {
    margin-top: 10px;
    min-height: 38px;
    display: flex;
    align-items: center;
    width: 100%;
  }
  .card-messenger-row {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--slate-50);
    border: 1px solid var(--border);
    padding: 6px 14px;
    border-radius: 30px;
    width: 100%;
    animation: slideInContact .25s cubic-bezier(0.19, 1, 0.22, 1) forwards;
  }
  @keyframes slideInContact {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .revealed-phone {
    font-size: 13px;
    font-weight: 700;
    color: var(--slate-900);
    flex: 1;
    text-align: left;
  }
  .messenger-icons-small {
    display: flex;
    gap: 6px;
    flex-shrink: 0;
  }
  .messenger-icons-small a {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    transition: opacity .15s, transform .15s;
    flex-shrink: 0;
  }
  .messenger-icons-small a:hover {
    opacity: 0.85;
    transform: scale(1.05);
  }
  .wa-s { background: #25D366; }
  .tg-s { background: #0088cc; }
  .vb-s { background: #7360F2; }
  .messenger-icons-small a svg {
    width: 13px;
    height: 13px;
    fill: currentColor;
    flex-shrink: 0;
  }

  .primary-action-btn {
    width: 100%;
    background: var(--amber);
    color: var(--slate-900);
    font-size: 13.5px;
    font-weight: 700;
    padding: 9.5px 16px;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    transition: background-color 0.15s, transform 0.15s;
    text-align: center;
    box-sizing: border-box;
  }
  .primary-action-btn:hover {
    background: var(--amber-dark);
    color: var(--white);
    transform: translateY(-1px);
  }

  @media (max-width: 1200px) {
    .listings-home-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  }
  @media (max-width: 900px) {
    .listings-home-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
  }
  @media (max-width: 600px) {
    .listings-home-grid { grid-template-columns: 1fr; }
    .error-410-container { padding: 0 16px; }
    .error-card { padding: 40px 16px; }
    .error-title { font-size: 24px; }
    .error-desc { font-size: 14px; }
  }

  /* Стили блога */
  .blog-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 32px;
    text-align: left;
  }

  .blog-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 20px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, border-color 0.2s ease;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.01);
  }
  .blog-card:hover {
    transform: translateY(-5px);
    border-color: var(--amber);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.05);
  }
  .blog-img-wrap {
    width: 100%;
    aspect-ratio: 16/10;
    overflow: hidden;
    background: #f7fafc;
  }
  .blog-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .blog-card-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    flex: 1;
  }
  .blog-meta {
    font-size: 11px;
    color: var(--gray-text);
    margin-bottom: 8px;
  }
  .blog-card-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--slate-900);
    line-height: 1.4;
    margin-bottom: 8px;
  }
  .blog-card-desc {
    font-size: 13px;
    color: var(--gray-text);
    line-height: 1.5;
    margin-bottom: 16px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
  }
  .blog-card-footer {
    padding-top: 12px;
    border-top: 1px solid var(--border);
    font-size: 12px;
    font-weight: 700;
    color: var(--amber-dark);
  }

  @media (max-width: 900px) {
    .blog-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
  }
  @media (max-width: 600px) {
    .blog-grid { grid-template-columns: 1fr; }
  }
</style>
@endsection

@section('content')
<div class="error-410-container">
  <div class="error-card">
    @if($type === 'blog')
      <span class="error-icon" aria-hidden="true">📖</span>
      <h1 class="error-title">Статья удалена или перенесена</h1>
      <p class="error-desc">Похоже, данный материал блога больше неактуален или был удален редакцией Flatly.by. Приносим извинения за неудобства.</p>
      <a href="{{ route('blog.index') }}" class="error-btn">Перейти в блог</a>
    @else
      <span class="error-icon" aria-hidden="true">🏠</span>
      <h1 class="error-title">Объявление больше неактивно</h1>
      <p class="error-desc">Жилье по этой ссылке больше не сдается посуточно или объявление было снято с публикации владельцем.</p>
      <a href="{{ route('home') }}" class="error-btn">На главную</a>
    @endif
  </div>

  @if($type === 'listing' && $featuredListings && $featuredListings->count() > 0)
    <h2 class="error-suggestions-title">Рекомендуемые предложения:</h2>
    <div class="listings-home-grid">
      @foreach($featuredListings as $item)
        @include('listings.partials.card', ['item' => $item])
      @endforeach
    </div>
  @elseif($type === 'blog' && $recentPosts && $recentPosts->count() > 0)
    <h2 class="error-suggestions-title">Другие полезные статьи в нашем гиде:</h2>
    <div class="blog-grid">
      @foreach($recentPosts as $post)
        <div class="blog-card" onclick="window.location.href='{{ route('blog.show', $post->slug) }}'">
          <div class="blog-img-wrap">
            <img class="blog-img" src="{{ $post->image ?: '/images/news_apartment_appeal.png' }}" alt="{{ $post->title }}" loading="lazy">
          </div>
          <div class="blog-card-body">
            <div class="blog-meta">
              <span>{{ \Carbon\Carbon::parse($post->created_at)->format('d.m.Y') }}</span>
            </div>
            <h3 class="blog-card-title">{{ $post->title }}</h3>
            <p class="blog-card-desc">{{ $post->summary }}</p>
            <div class="blog-card-footer">
              <span>Читать далее ›</span>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection

@section('scripts')
<script>
// === Listing Card Photo Carousel ===
function updateCardCarousel(carousel, index) {
  const track = carousel.querySelector('.card-carousel-track');
  const slides = carousel.querySelectorAll('.card-carousel-slide');
  const total = slides.length;
  if (total <= 1) return;
  
  let targetIndex = index;
  if (targetIndex < 0) {
    targetIndex = total - 1;
  } else if (targetIndex >= total) {
    targetIndex = 0;
  }
  
  carousel.setAttribute('data-current-index', targetIndex);
  track.style.transform = 'translateX(-' + (targetIndex * 100) + '%)';
  
  const dots = carousel.querySelectorAll('.carousel-dot');
  dots.forEach(function(dot, idx) {
    if (idx === targetIndex) {
      dot.classList.add('active');
    } else {
      dot.classList.remove('active');
    }
  });
}

function prevCardPhoto(event, btn) {
  event.stopPropagation();
  const carousel = btn.closest('.card-carousel');
  const currentIndex = parseInt(carousel.getAttribute('data-current-index') || '0', 10);
  updateCardCarousel(carousel, currentIndex - 1);
}

function nextCardPhoto(event, btn) {
  event.stopPropagation();
  const carousel = btn.closest('.card-carousel');
  const currentIndex = parseInt(carousel.getAttribute('data-current-index') || '0', 10);
  updateCardCarousel(carousel, currentIndex + 1);
}

function setCardPhoto(event, dot, index) {
  event.stopPropagation();
  const carousel = dot.closest('.card-carousel');
  updateCardCarousel(carousel, index);
}

// Инициализация свайпов на тачскринах для каруселей объявлений
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.card-carousel').forEach(function(carousel) {
    let startX = 0;
    let currentX = 0;
    let isSwiping = false;

    carousel.addEventListener('touchstart', function(e) {
      if (e.target.closest('.fav-btn') || e.target.closest('.carousel-arrow') || e.target.closest('.carousel-dots')) {
        return;
      }
      startX = e.touches[0].clientX;
      isSwiping = true;
    }, { passive: true });

    carousel.addEventListener('touchmove', function(e) {
      if (!isSwiping) return;
      currentX = e.touches[0].clientX;
    }, { passive: true });

    carousel.addEventListener('touchend', function(e) {
      if (!isSwiping) return;
      isSwiping = false;
      const diffX = startX - currentX;
      if (Math.abs(diffX) > 40 && currentX !== 0) {
        const currentIndex = parseInt(carousel.getAttribute('data-current-index') || '0', 10);
        if (diffX > 0) {
          updateCardCarousel(carousel, currentIndex + 1);
        } else {
          updateCardCarousel(carousel, currentIndex - 1);
        }
      }
      startX = 0;
      currentX = 0;
    });
  });
});

function toggleCardContact(event, listingId) {
  if (event) event.stopPropagation();
  const phoneBtn = document.getElementById('phone-btn-' + listingId);
  const messengers = document.getElementById('messengers-' + listingId);
  if (phoneBtn && messengers) {
    if (messengers.style.display === 'none') {
      phoneBtn.style.display = 'none';
      messengers.style.display = 'flex';
    } else {
      phoneBtn.style.display = '';
      messengers.style.display = 'none';
    }
  }
}

function openDetails(listingId, slug) {
  window.location.href = '/listings/' + listingId + '-' + slug;
}
</script>
@endsection
