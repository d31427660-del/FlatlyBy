@extends('layouts.app')

@section('title', 'Избранные объявления — Flatly.by')
@section('meta_description', 'Ваш список избранного жилья на Flatly.by. Сравните цены, контакты хозяев, удобства квартир и коттеджей посуточно.')
@section('meta_robots', 'noindex, follow')
@section('canonical_url', route('listings.favorites'))

@section('head')
@if($redirect)
  <script>
    (function() {
      try {
        const favs = JSON.parse(localStorage.getItem('flatly_favorites') || '[]');
        if (Array.isArray(favs) && favs.length > 0) {
          document.documentElement.style.display = 'none';
          window.location.replace("{{ route('listings.favorites') }}?ids=" + encodeURIComponent(favs.join(',')));
        }
      } catch (e) {}
    })();
  </script>
@endif

<style>
/* ===== FAVORITES LAYOUT ===== */
.favorites-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 40px 32px;
  min-height: calc(100vh - 156px);
  box-sizing: border-box;
}
.favorites-header {
  margin-bottom: 40px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.favorites-back-link {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: var(--gray-text);
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 12px;
  transition: color 0.15s;
}
.favorites-back-link:hover {
  color: var(--slate-900);
}
.favorites-back-link svg {
  width: 16px;
  height: 16px;
  stroke: currentColor;
  fill: none;
  stroke-width: 2.5;
}
.favorites-title {
  font-family: 'Instrument Sans', sans-serif;
  font-size: 32px;
  font-weight: 800;
  color: var(--slate-900);
  margin: 0;
  letter-spacing: -0.5px;
}
.favorites-count {
  font-size: 15px;
  color: var(--gray-text);
  font-weight: 500;
}
.favorites-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
}

/* ===== EMPTY STATE ===== */
.empty-state-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 80px 24px;
  max-width: 480px;
  margin: 0 auto;
}
.empty-state-icon {
  width: 80px;
  height: 80px;
  background: var(--slate-50);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
  color: var(--slate-200);
}
.empty-state-icon svg {
  width: 36px;
  height: 36px;
  fill: none;
  stroke: var(--slate-200);
  stroke-width: 1.5;
}
.empty-state-title {
  font-size: 22px;
  font-weight: 700;
  color: var(--slate-900);
  margin: 0 0 10px 0;
  letter-spacing: -0.3px;
}
.empty-state-subtitle {
  font-size: 15px;
  color: var(--gray-text);
  margin: 0 0 28px 0;
  line-height: 1.5;
}
.empty-state-btn {
  background: var(--slate-900);
  color: var(--white);
  padding: 12px 28px;
  border-radius: 24px;
  font-size: 14px;
  font-weight: 700;
  transition: opacity 0.15s, transform 0.15s;
}
.empty-state-btn:hover {
  opacity: 0.9;
  transform: translateY(-1px);
}

/* ===== RESPONSIVE ===== */
@media (max-width: 1024px) {
  .favorites-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
  }
}
@media (max-width: 768px) {
  .favorites-container {
    padding: 24px 20px;
  }
  .favorites-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  .favorites-title {
    font-size: 26px;
  }
}

/* ===== REUSED CARD STYLES ===== */
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
.carousel-arrow:active {
  transform: translateY(-50%) scale(0.95);
}
.card-img:hover .carousel-arrow {
  opacity: 1;
}
@media (max-width: 1024px) {
  .carousel-arrow {
    opacity: 0.85;
  }
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
.cb-title { font-size: 15px; font-weight: 600; color: var(--slate-900); line-height: 1.35; flex: 1; }

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
  padding: 3px 10px;
  font-size: 12px;
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
.revealed-phone {
  font-size: 13px;
  font-weight: 700;
  color: var(--slate-900);
  flex: 1;
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

@media (max-width: 576px) {
  .card-img {
    aspect-ratio: 1.4;
  }
  .cb-title {
    font-size: 14px;
  }
  .card-rating-pill {
    padding: 2px 2px 2px 6px;
    gap: 4px;
  }
  .rating-text {
    font-size: 10px;
  }
  .rating-badge {
    width: 18px;
    height: 18px;
    font-size: 10px;
  }
  .card-address-row, .card-metro-row {
    font-size: 12.5px;
  }
  .card-price-box {
    padding: 6px 10px;
  }
  .price-amount {
    font-size: 14px;
  }
  .badge-pill {
    padding: 2.5px 8px;
  }
}
</style>
@endsection

@section('content')
<div class="favorites-container">
  <div class="favorites-header">
    <a href="{{ route('home') }}" class="favorites-back-link">
      <x-solar-alt-arrow-left-linear />
      Назад на главную
    </a>
    <h1 class="favorites-title">Избранные объявления</h1>
    <span class="favorites-count" id="favorites-count-title">
      {{ $listings->isEmpty() ? '0 объявлений' : '' }}
    </span>
  </div>

  <div class="favorites-grid" style="display: {{ $listings->isEmpty() ? 'none' : 'grid' }};">
    @foreach($listings as $index => $item)
      @include('listings.partials.card', ['item' => $item, 'isLcp' => ($index < 2)])
    @endforeach
  </div>

  <div id="favorites-empty-state" class="empty-state-section" style="display: {{ $listings->isEmpty() ? 'flex' : 'none' }};">
    <div class="empty-state-icon">
      <x-solar-heart-linear />
    </div>
    <h2 class="empty-state-title">В избранном пока ничего нет</h2>
    <p class="empty-state-subtitle">Добавляйте понравившиеся объявления в избранное, нажимая на сердечко, чтобы они сохранились здесь.</p>
    <a href="{{ route('home') }}" class="empty-state-btn">Найти жилье</a>
  </div>
</div>
@endsection

@section('scripts')
<script>
// Карточки деталей
function openDetails(id, slug = null) {
  window.location.href = '/listings/' + id + (slug ? '-' + slug : '');
}

// Избранное
function toggleFav(btn) {
  event.stopPropagation();
  const card = btn.closest('.card');
  if (!card) return;
  const id = parseInt(card.getAttribute('data-id'), 10);
  const isAdded = toggleFavoriteId(id);
  
  if (isAdded) {
    btn.classList.add('active');
    showToast('❤️ Добавлено в избранное');
  } else {
    btn.classList.remove('active');
    showToast('Удалено из избранного');
    
    // Анимация удаления карточки
    card.style.transition = 'transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s ease, margin-bottom 0.4s ease';
    card.style.opacity = '0';
    card.style.transform = 'scale(0.9) translateY(20px)';
    
    setTimeout(() => {
      card.remove();
      updateFavoritesPageCount();
    }, 400);
  }
}

function updateFavoritesPageCount() {
  const cards = document.querySelectorAll('.card');
  const count = cards.length;
  
  const countTitleEl = document.getElementById('favorites-count-title');
  if (countTitleEl) {
    countTitleEl.textContent = getPluralAnnouncements(count);
  }
  
  if (count === 0) {
    const gridEl = document.querySelector('.favorites-grid');
    if (gridEl) gridEl.style.display = 'none';
    
    const emptyStateEl = document.getElementById('favorites-empty-state');
    if (emptyStateEl) emptyStateEl.style.display = 'flex';
  }
}

function getPluralAnnouncements(n) {
  var plural = 'объявлений';
  var mod10 = n % 10;
  var mod100 = n % 100;
  if (mod10 === 1 && mod100 !== 11) {
    plural = 'объявление';
  } else if (mod10 >= 2 && mod10 <= 4 && (mod100 < 10 || mod100 >= 20)) {
    plural = 'объявления';
  }
  return n + ' ' + plural;
}

// Картинки-карусели
function prevCardPhoto(event, btn) {
  if (event) event.stopPropagation();
  const carousel = btn.closest('.card-carousel');
  const track = carousel.querySelector('.card-carousel-track');
  const slides = carousel.querySelectorAll('.card-carousel-slide');
  let index = parseInt(carousel.getAttribute('data-current-index')) || 0;
  
  index = (index > 0) ? index - 1 : slides.length - 1;
  carousel.setAttribute('data-current-index', index);
  track.style.transform = `translateX(-${index * 100}%)`;
  
  const dots = carousel.querySelectorAll('.carousel-dot');
  dots.forEach((dot, idx) => {
    if (idx === index) dot.classList.add('active');
    else dot.classList.remove('active');
  });
}

function nextCardPhoto(event, btn) {
  if (event) event.stopPropagation();
  const carousel = btn.closest('.card-carousel');
  const track = carousel.querySelector('.card-carousel-track');
  const slides = carousel.querySelectorAll('.card-carousel-slide');
  let index = parseInt(carousel.getAttribute('data-current-index')) || 0;
  
  index = (index < slides.length - 1) ? index + 1 : 0;
  carousel.setAttribute('data-current-index', index);
  track.style.transform = `translateX(-${index * 100}%)`;
  
  const dots = carousel.querySelectorAll('.carousel-dot');
  dots.forEach((dot, idx) => {
    if (idx === index) dot.classList.add('active');
    else dot.classList.remove('active');
  });
}

function setCardPhoto(event, dot, index) {
  if (event) event.stopPropagation();
  const carousel = dot.closest('.card-carousel');
  const track = carousel.querySelector('.card-carousel-track');
  
  carousel.setAttribute('data-current-index', index);
  track.style.transform = `translateX(-${index * 100}%)`;
  
  const dots = carousel.querySelectorAll('.carousel-dot');
  dots.forEach((d, idx) => {
    if (idx === index) d.classList.add('active');
    else d.classList.remove('active');
  });
}

// Раскрытие контактов
function toggleCardContact(event, id) {
  if (event) event.stopPropagation();
  const btn = document.getElementById('phone-btn-' + id);
  const mRow = document.getElementById('messengers-' + id);
  
  if (btn.style.display === 'none') {
    btn.style.display = '';
    mRow.style.display = 'none';
  } else {
    btn.style.display = 'none';
    mRow.style.display = 'flex';
  }
}

// Валюта
function updateCurrencyDisplay() {
  const currentCurrency = localStorage.getItem('flatly_currency') || 'BYN';
  const rate = EXCHANGE_RATES[currentCurrency] || 1.0;
  
  document.querySelectorAll('.price-val').forEach(el => {
    const basePrice = parseFloat(el.getAttribute('data-base-price'));
    if (!isNaN(basePrice)) {
      const converted = Math.round(basePrice * rate);
      if (currentCurrency === 'USD') {
        el.textContent = '$' + converted;
      } else if (currentCurrency === 'EUR') {
        el.textContent = '€' + converted;
      } else if (currentCurrency === 'RUB') {
        el.textContent = converted + ' ₽';
      } else {
        el.textContent = converted + ' Б̶';
      }
    }
  });
}

document.addEventListener('DOMContentLoaded', function() {
  const favs = getFavorites();
  
  // Активируем сердечки
  document.querySelectorAll('.card').forEach(function(card) {
    var id = parseInt(card.getAttribute('data-id'), 10);
    if (favs.includes(id)) {
      const favBtn = card.querySelector('.fav-btn');
      if (favBtn) favBtn.classList.add('active');
    }
  });
  
  // Устанавливаем заголовок
  const cards = document.querySelectorAll('.card');
  const countTitleEl = document.getElementById('favorites-count-title');
  if (countTitleEl) {
    countTitleEl.textContent = getPluralAnnouncements(cards.length);
  }
  
  // Устанавливаем валюты
  updateCurrencyDisplay();
});
</script>
@endsection
