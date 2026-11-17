@extends('layouts.app')

@section('title', $seoTitle ?? (isset($selectedCity) && isset($inCity) ? 'Flatly.by — Аренда квартир и коттеджей ' . $inCity . ' без посредников' : 'Flatly.by — Аренда квартир и коттеджей в Беларуси без посредников'))
@section('meta_description', $seoDescription ?? (isset($selectedCity) && isset($inCity) ? 'Снять квартиру посуточно или коттедж ' . $inCity . '. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.' : 'Снять квартиру посуточно или коттедж в Минске, Бресте, Гродно и других городах Беларуси. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.'))
@section('canonical_url', $canonicalUrl ?? (request()->url() . (request()->has('page') ? '?page=' . request()->query('page') : '')))


@section('head')
<style>
/* ===== FILTER STRIP ===== */
.filters-section {
  border-bottom: 1px solid var(--border);
  padding: 0 32px;
  display: flex; align-items: center;
  height: 76px;
  background: var(--white);
}
.filters-scroll {
  display: flex; align-items: center;
  gap: 0; height: 100%;
  overflow-x: auto;
  scrollbar-width: none;
  flex: 1;
}
.filters-scroll::-webkit-scrollbar { display: none; }
.fi {
  display: flex; flex-direction: column;
  align-items: center; gap: 5px;
  padding: 8px 20px;
  cursor: pointer;
  opacity: .55;
  border-bottom: 2px solid transparent;
  flex-shrink: 0;
  transition: opacity .15s;
  white-space: nowrap;
}
.fi:hover { opacity: .85; }
.fi.active { opacity: 1; border-bottom-color: var(--slate-900); }
.fi svg { width: 24px; height: 24px; stroke: var(--slate-900); fill: none; stroke-width: 1.7; }
.fi span { font-size: 12px; font-weight: 500; color: var(--slate-900); }
.filter-btn-wrapper {
  margin-left: 16px;
  padding-left: 16px;
  border-left: 1px solid var(--border);
  display: flex;
  align-items: center;
  flex-shrink: 0;
  height: 32px;
}
.filter-btn {
  border: 1px solid var(--slate-900);
  border-radius: 8px; padding: 9px 16px;
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; font-weight: 600;
  background: var(--white);
  flex-shrink: 0;
}
.filter-btn:hover { background: var(--slate-50); }
.filter-btn svg { width: 16px; height: 16px; stroke: var(--slate-900); fill: none; stroke-width: 2; }

/* ===== MAIN LAYOUT ===== */
.main { display: flex; min-height: calc(100vh - 156px); }

/* ===== LISTINGS ===== */
.listings { flex: 1; padding: 28px 32px; min-width: 0; }
.top-info { font-size: 14px; color: var(--slate-900); margin-bottom: 24px; }
.top-info span { text-decoration: underline; cursor: pointer; }

.grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 28px; }

/* ===== CARD ===== */
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

/* ===== CARD HIGHLIGHTED (PREMIUM) ===== */
.card.highlighted {
  background: #FFF9E6;
  border-color: #F5A623;
  box-shadow: 0 4px 16px rgba(245, 166, 35, 0.06);
}
.card.highlighted:hover {
  border-color: #D4890A;
  box-shadow: 0 10px 24px rgba(245, 166, 35, 0.12);
}

/* ===== CARD BADGES ===== */
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
  background: linear-gradient(135deg, #007AFF, #0056B3);
}
.card-badge.sale {
  background: linear-gradient(135deg, #10B981, #059669);
}

/* ===== CARD CAROUSEL ===== */
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
/* Показываем стрелки на тачскринах постоянно, так как ховера нет */
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


.badge {
  position: absolute; top: 12px; left: 12px;
  background: var(--white); border-radius: 7px;
  font-size: 12px; font-weight: 700;
  padding: 5px 9px;
  display: flex; align-items: center; gap: 5px;
  color: var(--slate-900);
}
.badge svg { width: 13px; height: 13px; stroke: var(--amber-dark); fill: none; stroke-width: 2.2; }

.dots { position: absolute; bottom: 12px; left: 50%; transform: translateX(-50%); display: flex; gap: 5px; }
.dot { width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,.55); }
.dot.on { background: #fff; width: 7px; height: 7px; }

.card-body {
  margin-top: 11px;
  display: flex;
  flex-direction: column;
  flex: 1;
}
.cb-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; }
.cb-title { font-size: 15px; font-weight: 600; color: var(--slate-900); line-height: 1.35; flex: 1; }
.cb-rating { display: flex; align-items: center; gap: 3px; font-size: 14px; font-weight: 600; white-space: nowrap; color: var(--slate-900); }
.cb-rating svg { width: 13px; height: 13px; fill: var(--slate-900); }
.cb-rating span { color: var(--gray-text); font-weight: 400; }
.cb-sub { font-size: 13px; color: var(--gray-text); margin-top: 3px; }
.cb-dates { font-size: 13px; color: var(--gray-text); margin-top: 2px; }
.cb-price { margin-top: 7px; font-size: 15px; color: var(--slate-900); }
.cb-price strong { font-weight: 700; }
.cb-total { font-size: 13px; color: var(--gray-text); margin-top: 1px; text-decoration: underline; }

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

/* ===== CARD DETAILS IN MOCKUP ===== */
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
@keyframes slideInContact {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
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

/* ===== MAP ===== */
.map-col {
  width: 44%;
  position: sticky;
  top: 135px;
  height: calc(100vh - 150px);
  align-self: flex-start;
  padding: 12px 24px 12px 12px;
  box-sizing: border-box;
  background: transparent;
  flex-shrink: 0;
}

.map-inner-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  border-radius: 24px;
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.08);
  box-shadow: 0 8px 32px rgba(26, 26, 46, 0.08);
  transform: translateZ(0);
  background: #f8fafc;
}

#map {
  width: 100%;
  height: 100%;
  border-radius: inherit;
}

/* Custom light filter for Yandex Map tiles to look clean and desaturated like Airbnb/Google maps */
#map ymaps img {
  filter: grayscale(85%) brightness(104%) contrast(95%) sepia(3%);
}

.map-price {
  position: absolute;
  background: #ffffff;
  border: 1.5px solid rgba(0, 0, 0, 0.12);
  border-radius: 28px;
  padding: 5px 12px;
  font-size: 13px;
  font-weight: 800;
  color: #222222;
  cursor: pointer;
  transform: translate(-50%, -50%); /* Centered on coordinates */
  box-shadow: 0 3px 10px rgba(0,0,0,0.08);
  transition: background-color 0.15s, color 0.15s, border-color 0.15s, transform 0.15s, box-shadow 0.15s;
  white-space: nowrap;
  display: flex;
  align-items: center;
  gap: 4px;
}
.map-price.visited {
  color: #4A5568;
  background: #f7fafc;
  border-color: rgba(0, 0, 0, 0.08);
  font-weight: 600;
}
.map-price:hover, .map-price.sel {
  background: #222222 !important;
  color: #ffffff !important;
  border-color: #222222 !important;
  transform: translate(-50%, -50%) scale(1.08);
  box-shadow: 0 6px 16px rgba(0,0,0,0.16);
  z-index: 99999;
}
.map-price.sel.visited {
  color: #ffffff;
  background: #222222;
}
.hourly-dot {
  font-size: 11px;
  opacity: 0.85;
}



/* Custom Geolocation Button */
.map-geo-btn {
  position: absolute;
  left: 26px;
  top: 380px;
  z-index: 100;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--white);
  border: 1px solid rgba(0, 0, 0, 0.12);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.15s, background-color 0.15s;
  color: var(--slate-900);
  outline: none;
}
.map-geo-btn:hover {
  transform: scale(1.05);
  background: var(--slate-50);
}
.map-geo-btn svg {
  width: 18px;
  height: 18px;
}
@media (max-width: 991px) {
  .map-geo-btn {
    bottom: 275px;
    right: 16px;
    left: auto !important;
    top: auto !important;
  }
  .map-card-preview {
    bottom: 74px;
    left: 12px;
    right: 12px;
    padding: 12px;
    border-radius: 16px;
  }
}

@media (max-width: 440px) {
  .map-search-area-box {
    top: 56px;
    left: 12px;
  }
  .map-controls-toolbar {
    top: 12px;
    right: 48px;
  }
}

/* ===== FLOATING SEARCH AREA BUTTON ===== */
.map-search-area-box {
  position: absolute;
  top: 16px;
  left: 20px;
  z-index: 1000;
  display: flex;
  align-items: center;
  gap: 6px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgba(226, 232, 240, 0.9);
  border-radius: 30px;
  padding: 4px 6px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  animation: fadeInAreaBtn 0.3s ease-out;
}

@keyframes fadeInAreaBtn {
  from {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.map-search-area-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--amber-dark, #D97706);
  color: #ffffff;
  border: none;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 13.5px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(217, 119, 6, 0.3);
  transition: all 0.2s ease;
  white-space: nowrap;
  outline: none;
}

.map-search-area-btn:hover {
  background: #B45309;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(217, 119, 6, 0.4);
}

.map-search-area-btn.active-area {
  background: #059669;
  box-shadow: 0 2px 8px rgba(5, 150, 105, 0.3);
}

.map-reset-area-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  background: #edf2f7;
  color: #4a5568;
  border: none;
  border-radius: 50%;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  outline: none;
}

.map-reset-area-btn:hover {
  background: #e2e8f0;
  color: #1a202c;
}

/* ===== POI MAP BADGES & TOGGLE ===== */
/* ===== TOP-RIGHT MAP CONTROLS TOOLBAR ===== */
.map-controls-toolbar {
  position: absolute;
  top: 14px;
  right: 52px;
  z-index: 1000;
  display: flex;
  align-items: center;
  gap: 8px;
}

.map-draw-btn,
.map-poi-toggle-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgba(226, 232, 240, 0.9);
  border-radius: 20px;
  padding: 6px 14px;
  font-size: 13px;
  font-weight: 600;
  color: #334155;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
  cursor: pointer;
  transition: all 0.2s ease;
  outline: none;
  white-space: nowrap;
}

.map-draw-btn:hover,
.map-poi-toggle-btn:hover {
  background: #ffffff;
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.16);
}

.map-draw-btn.drawing-active {
  background: var(--amber-dark, #D97706);
  color: #ffffff;
  border-color: var(--amber-dark, #D97706);
  box-shadow: 0 4px 14px rgba(217, 119, 6, 0.4);
}

.map-poi-toggle-btn.active {
  background: var(--slate-900, #0f172a);
  color: #ffffff;
  border-color: var(--slate-900, #0f172a);
}

.map-draw-hint {
  position: absolute;
  top: 56px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 10005;
  display: flex;
  align-items: center;
  gap: 12px;
  background: #0f172a;
  color: #ffffff;
  padding: 8px 16px;
  border-radius: 30px;
  font-size: 13px;
  font-weight: 600;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
  animation: fadeInAreaBtn 0.3s ease-out;
  white-space: nowrap;
}
.map-draw-hint button {
  background: rgba(255, 255, 255, 0.2);
  color: #ffffff;
  border: none;
  padding: 4px 10px;
  border-radius: 14px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.15s;
}
.map-draw-hint button:hover {
  background: rgba(255, 255, 255, 0.35);
}

.map-polygon-bar {
  position: absolute;
  top: 56px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1000;
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1.5px solid #d97706;
  padding: 6px 14px;
  border-radius: 30px;
  font-size: 13px;
  font-weight: 600;
  color: #92400e;
  box-shadow: 0 4px 20px rgba(217, 119, 6, 0.25);
  white-space: nowrap;
}
.map-polygon-bar .reset-poly-btn {
  background: #fef3c7;
  color: #b45309;
  border: none;
  padding: 4px 10px;
  border-radius: 14px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s ease;
}
.map-polygon-bar .reset-poly-btn:hover {
  background: #fde68a;
  color: #78350f;
}

@media (max-width: 1200px), (max-width: 991px) {
  .map-controls-toolbar .btn-lbl {
    display: none;
  }
  .map-draw-btn,
  .map-poi-toggle-btn {
    padding: 6px 10px;
  }
  .map-search-area-box {
    max-width: calc(100% - 150px);
  }
  .map-search-area-btn span {
    font-size: 12px;
  }
  .map-polygon-bar,
  .map-draw-hint {
    top: 52px;
    font-size: 12px;
    padding: 5px 12px;
  }
}

/* ===== AMBER CLUSTER BADGE ===== */
.map-cluster-badge {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: linear-gradient(135deg, #D97706, #B45309);
  color: #ffffff;
  font-weight: 800;
  font-size: 13.5px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 14px rgba(217, 119, 6, 0.45);
  border: 2.5px solid #ffffff;
  cursor: pointer;
  transition: transform 0.2s ease, background 0.2s ease;
}
.map-cluster-badge:hover {
  transform: scale(1.1);
  background: linear-gradient(135deg, #B45309, #92400E);
}

.map-poi-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 4px 9px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  border: 1.5px solid #cbd5e1;
  font-size: 11.5px;
  font-weight: 700;
  color: #0f172a;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
  white-space: nowrap;
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.map-poi-badge:hover {
  transform: scale(1.08);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.22);
}
.map-poi-badge.metro {
  border-color: #3b82f6;
  color: #1d4ed8;
  background: rgba(239, 246, 255, 0.96);
}
.map-poi-badge.mall {
  border-color: #a855f7;
  color: #7e22ce;
  background: rgba(250, 245, 255, 0.96);
}
.map-poi-badge.landmark {
  border-color: #f59e0b;
  color: #b45309;
  background: rgba(254, 252, 232, 0.96);
}
.map-poi-badge.station {
  border-color: #ef4444;
  color: #b91c1c;
  background: rgba(254, 242, 242, 0.96);
}

.map-card-poi-box {
  display: flex;
  flex-direction: column;
  gap: 5px;
  margin: 6px 0 10px 0;
  padding: 8px 12px;
  background: #f8fafc;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
}
.card-poi-row {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  font-weight: 600;
  color: #1e293b;
}
.card-poi-row .poi-icon {
  font-size: 14px;
  flex-shrink: 0;
}
.card-poi-row .poi-text {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* ===== MAP PREVIEW CARD ===== */
.map-card-preview {
  position: absolute;
  bottom: 24px;
  left: 24px;
  right: 24px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.5);
  border-radius: 20px;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
  padding: 16px;
  display: none;
  gap: 16px;
  z-index: 1000;
  cursor: pointer;
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, border-color 0.2s ease, background-color 0.2s ease;
  pointer-events: none;
  text-align: left;
  box-sizing: border-box;
}
.map-card-preview.show {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}
.map-card-preview.show:hover {
  transform: translateY(-4px);
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.22);
  border-color: rgba(0, 0, 0, 0.12);
  background-color: rgba(255, 255, 255, 0.98);
}
.map-card-img-wrap {
  width: 140px;
  align-self: stretch;
  border-radius: 14px;
  overflow: hidden;
  flex-shrink: 0;
  position: relative;
  background: var(--slate-100);
}
.map-card-img-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  position: absolute;
  top: 0;
  left: 0;
}
.map-card-fake-img {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 40px;
  color: #fff;
  position: absolute;
  top: 0;
  left: 0;
}
.map-card-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.map-card-top-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 8px;
  margin-bottom: 2px;
}
.map-card-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--slate-900);
  line-height: 1.3;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  flex: 1;
}
.map-rating-pill {
  flex-shrink: 0;
  margin-top: 1px;
}
.map-card-address {
  font-size: 12.5px;
  color: #4A5568;
  margin-bottom: 4px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-weight: 500;
}
.map-metro-row {
  margin-top: 0 !important;
  margin-bottom: 8px;
  font-size: 12.5px;
}
.map-price-box {
  margin-top: 0 !important;
  padding: 6px 10px !important;
}
.map-price-box .price-amount {
  font-size: 14px !important;
}
.map-price-box .price-period {
  font-size: 10px !important;
}
.map-price-box .price-divider {
  height: 24px !important;
}
.map-badges-row {
  margin-top: 8px !important;
}
.map-badges-row .badge-pill {
  padding: 2px 8px !important;
  font-size: 11px !important;
}
.map-contact-block {
  margin-top: 8px !important;
  min-height: 34px !important;
}
.map-contact-block .primary-action-btn {
  padding: 8px 16px !important;
  font-size: 12.5px !important;
}
.map-messenger-row {
  padding: 4px 10px !important;
}
.map-messenger-row .revealed-phone {
  font-size: 12px !important;
}
.map-messenger-row .messenger-icons-small a {
  width: 22px !important;
  height: 22px !important;
}
.map-messenger-row .messenger-icons-small a svg {
  width: 11px !important;
  height: 11px !important;
}
.map-card-close {
  position: absolute;
  top: -8px;
  right: -8px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #ffffff;
  border: 1px solid rgba(0,0,0,0.1);
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  font-size: 14px;
  font-weight: bold;
  color: var(--slate-900);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.15s;
  z-index: 1010;
  outline: none;
}
.map-card-close:hover {
  transform: scale(1.1);
}

@media (max-width: 991px) {
  .map-card-preview {
    bottom: 24px;
    left: 16px;
    right: 16px;
  }
}
@media (max-width: 576px) {
  .map-card-preview {
    padding: 10px;
    gap: 10px;
    bottom: 20px;
  }
  .map-card-img-wrap {
    width: 100px;
    min-height: 120px;
  }
  .map-card-top-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }
  .map-card-title {
    font-size: 13px;
  }
  .map-rating-pill {
    padding: 2px 2px 2px 6px;
    gap: 4px;
  }
  .map-rating-pill .rating-text {
    font-size: 9px;
  }
  .map-rating-pill .rating-badge {
    width: 16px;
    height: 16px;
    font-size: 9px;
  }
  .map-card-address {
    font-size: 11px;
  }
  .map-metro-row {
    font-size: 11px;
    gap: 4px;
  }
  .map-metro-row svg {
    width: 12px;
    height: 12px;
  }
  .map-price-box {
    padding: 4px 6px !important;
  }
  .map-price-box .price-amount {
    font-size: 12px !important;
  }
  .map-price-box .price-period {
    font-size: 9px !important;
  }
  .map-badges-row {
    margin-top: 6px !important;
    gap: 4px !important;
  }
  .map-badges-row .badge-pill {
    padding: 1px 6px !important;
    font-size: 10px !important;
  }
  .map-contact-block {
    margin-top: 6px !important;
    min-height: 30px !important;
  }
  .map-contact-block .primary-action-btn {
    padding: 6px 12px !important;
    font-size: 11.5px !important;
  }
  .map-messenger-row {
    padding: 3px 8px !important;
    gap: 6px !important;
  }
  .map-messenger-row .revealed-phone {
    font-size: 11px !important;
  }
}

/* ===== SHOW MORE ===== */
.show-more { text-align: center; padding: 32px 0 16px; }
.show-more button {
  border: 1.5px solid var(--slate-900);
  background: var(--white); padding: 14px 28px;
  border-radius: 10px; font-size: 15px; font-weight: 600;
  color: var(--slate-900);
  transition: background .15s;
}
.show-more button:hover { background: var(--slate-50); }

/* ===== FILTER MODAL ===== */
.modal-backdrop {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(26, 26, 46, 0.4);
  backdrop-filter: blur(8px);
  z-index: 3000;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  pointer-events: none;
  transition: opacity .3s cubic-bezier(0.19, 1, 0.22, 1);
}
.modal-backdrop.show {
  opacity: 1;
  pointer-events: auto;
}
.filter-modal-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 20px;
  width: 100%;
  max-width: 560px;
  max-height: 90vh;
  box-shadow: 0 20px 60px rgba(26,26,46,0.15);
  display: flex;
  flex-direction: column;
  transform: scale(0.9) translateY(20px);
  transition: transform .3s cubic-bezier(0.19, 1, 0.22, 1);
}
.modal-backdrop.show .filter-modal-card {
  transform: scale(1) translateY(0);
}
.filter-modal-header {
  padding: 20px 28px;
  border-bottom: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.filter-modal-header h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--slate-900);
}
.close-modal-btn {
  background: var(--slate-50);
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  font-size: 20px;
  line-height: 1;
  color: var(--slate-900);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.15s;
}
.close-modal-btn:hover {
  background: var(--slate-100);
}
.filter-modal-body {
  padding: 24px 28px;
  overflow-y: auto;
  flex: 1;
}
.filter-section {
  margin-bottom: 28px;
}
.filter-section h4 {
  font-size: 14px;
  font-weight: 600;
  color: var(--slate-900);
  margin-bottom: 14px;
}
.filter-options-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}
.filter-chip {
  position: relative;
  cursor: pointer;
}
.filter-chip input {
  position: absolute;
  opacity: 0;
  width: 0; height: 0;
}
.filter-chip span {
  display: block;
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 12px;
  font-size: 13px;
  font-weight: 500;
  text-align: center;
  color: var(--slate-900);
  background: var(--white);
  transition: all 0.15s;
}
.filter-chip input:checked + span {
  border-color: var(--slate-900);
  background: var(--slate-900);
  color: var(--white);
}
.price-inputs {
  display: flex;
  gap: 16px;
}
.price-input-wrap {
  flex: 1;
  display: flex;
  align-items: center;
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 0 16px;
  height: 48px;
  background: var(--white);
}
.price-input-wrap span {
  font-size: 13px;
  color: var(--gray-text);
  margin-right: 8px;
}
.price-input-wrap span:last-child {
  margin-right: 0;
  margin-left: 8px;
  font-weight: 600;
}
.price-input-wrap input {
  border: none;
  outline: none;
  width: 100%;
  font-size: 14px;
  font-weight: 600;
  color: var(--slate-900);
}
.guests-selector {
  display: flex;
  align-items: center;
  border: 1px solid var(--border);
  border-radius: 10px;
  height: 48px;
  width: 180px;
  background: var(--white);
  overflow: hidden;
}
.guests-selector button {
  width: 48px;
  height: 100%;
  border: none;
  background: var(--slate-50);
  font-size: 18px;
  font-weight: 600;
  color: var(--slate-900);
  transition: background .15s;
}
.guests-selector button:hover {
  background: var(--slate-100);
}
.guests-selector span {
  flex: 1;
  text-align: center;
  font-size: 14px;
  font-weight: 600;
  color: var(--slate-900);
}
.filter-amenities-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 14px;
}
.amenity-checkbox {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
  font-weight: 500;
  color: var(--slate-900);
  cursor: pointer;
}
.amenity-checkbox input {
  width: 18px;
  height: 18px;
  accent-color: var(--amber);
}

/* Rooms & Beds selector */
.rooms-beds-row {
  display: flex;
  gap: 8px;
  margin-top: 6px;
  overflow-x: auto;
  scrollbar-width: none;
}
.rooms-beds-row::-webkit-scrollbar { display: none; }
.rb-btn {
  border: 1px solid var(--border);
  background: var(--white);
  border-radius: 20px;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 600;
  color: var(--slate-900);
  cursor: pointer;
  transition: all 0.15s;
  white-space: nowrap;
}
.rb-btn:hover {
  background: var(--slate-50);
  border-color: var(--slate-600);
}
.rb-btn.active {
  background: var(--slate-900);
  color: var(--white);
  border-color: var(--slate-900);
}
.filter-modal-footer {
  padding: 20px 28px;
  border-top: 1px solid var(--border);
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.reset-filter-btn {
  background: none;
  border: none;
  font-size: 14px;
  font-weight: 600;
  text-decoration: underline;
  color: var(--slate-900);
  cursor: pointer;
}
.apply-filter-btn {
  background: var(--slate-900);
  color: var(--white);
  border: none;
  padding: 14px 28px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  transition: opacity .15s;
  cursor: pointer;
}
.apply-filter-btn:hover {
  opacity: 0.9;
}

/* ===== PRICE RANGE SLIDER ===== */
.price-slider-wrapper {
  margin-top: 20px;
  padding: 0 10px;
  margin-bottom: 24px;
}
.range-slider {
  position: relative;
  width: 100%;
  height: 6px;
  background: var(--slate-100);
  border-radius: 3px;
}
.slider-track {
  position: absolute;
  height: 100%;
  background: var(--slate-900);
  border-radius: 3px;
  z-index: 1;
}
.range-slider input[type="range"] {
  position: absolute;
  width: 100%;
  height: 6px;
  top: 0;
  background: none;
  pointer-events: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  z-index: 2;
  margin: 0;
}
.range-slider input[type="range"]::-webkit-slider-thumb {
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: var(--white);
  border: 2px solid var(--slate-900);
  cursor: pointer;
  pointer-events: auto;
  -webkit-appearance: none;
  box-shadow: 0 2px 5px rgba(0,0,0,0.15);
  transition: transform 0.1s ease, background-color 0.1s;
}
.range-slider input[type="range"]::-webkit-slider-thumb:hover {
  transform: scale(1.1);
  background: var(--slate-50);
}
.range-slider input[type="range"]::-webkit-slider-thumb:active {
  transform: scale(0.95);
  background: var(--slate-900);
}
.range-slider input[type="range"]::-moz-range-thumb {
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: var(--white);
  border: 2px solid var(--slate-900);
  cursor: pointer;
  pointer-events: auto;
  box-shadow: 0 2px 5px rgba(0,0,0,0.15);
}
.range-slider input[type="range"]::-moz-range-thumb:hover {
  transform: scale(1.1);
}


/* ===== TARIFFS PROMO BANNER ===== */
.tariffs-banner {
  background: linear-gradient(135deg, #1e293b, #0f172a);
  border-radius: 16px;
  padding: 20px 24px;
  margin-bottom: 28px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: var(--white);
  cursor: pointer;
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
  position: relative;
  overflow: hidden;
}
.tariffs-banner::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: radial-gradient(circle at 80% 20%, rgba(245, 158, 11, 0.15), transparent 50%);
  pointer-events: none;
}
.tariffs-banner:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(245, 158, 11, 0.12);
  border-color: rgba(245, 158, 11, 0.4);
}
.tb-content {
  flex: 1;
  z-index: 2;
}
.tb-title {
  font-size: 17px;
  font-weight: 700;
  margin-bottom: 4px;
  color: var(--white);
  display: flex;
  align-items: center;
  gap: 8px;
}
.tb-badge {
  background: var(--amber);
  color: var(--slate-900);
  font-size: 11px;
  font-weight: 800;
  padding: 2px 8px;
  border-radius: 12px;
  text-transform: uppercase;
}
.tb-text {
  font-size: 13px;
  color: #94a3b8;
}
.tb-pills {
  display: flex;
  gap: 12px;
  align-items: center;
  z-index: 2;
  margin-left: 20px;
}
.tb-pill {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.1);
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  color: var(--white);
  white-space: nowrap;
  transition: background 0.15s;
}
.tb-pill strong {
  color: var(--amber);
}
.tb-pill.promo-pill {
  background: rgba(245, 158, 11, 0.1);
  border-color: rgba(245, 158, 11, 0.3);
}

@media (max-width: 768px) {
  .tb-pills {
    margin-left: 0;
    width: 100%;
    flex-wrap: wrap;
    gap: 8px;
    padding-bottom: 4px;
  }
  .tb-pill {
    font-size: 12px;
    padding: 5px 12px;
  }
}

/* ===== FREE PLACEMENT PROMO BANNER ===== */
.free-placement-banner {
  background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
  border-radius: 16px;
  padding: 22px 26px;
  margin-bottom: 28px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: var(--white);
  cursor: pointer;
  border: 1px solid rgba(99, 102, 241, 0.25);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
  position: relative;
  overflow: hidden;
  gap: 24px;
}
.free-placement-banner::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: radial-gradient(circle at 15% 50%, rgba(99, 102, 241, 0.15), transparent 45%);
  pointer-events: none;
}
.free-placement-banner:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(99, 102, 241, 0.2);
  border-color: rgba(99, 102, 241, 0.45);
}
.fpb-left {
  display: flex;
  align-items: center;
  gap: 18px;
  flex: 1;
  z-index: 2;
}
.fpb-icon-wrapper {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: rgba(99, 102, 241, 0.15);
  border: 1px solid rgba(99, 102, 241, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  color: #818cf8;
  flex-shrink: 0;
}
.fpb-icon-wrapper svg {
  width: 24px;
  height: 24px;
}
.fpb-content {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.fpb-header {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}
.fpb-title {
  font-size: 16.5px;
  font-weight: 700;
  color: var(--white);
  letter-spacing: -0.2px;
}
.fpb-badge {
  background: linear-gradient(90deg, #f59e0b, #d97706);
  color: var(--slate-900);
  font-size: 11px;
  font-weight: 800;
  padding: 2px 8px;
  border-radius: 6px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 6px rgba(245, 158, 11, 0.25);
}
.fpb-description {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.75);
  line-height: 1.5;
  margin: 0;
}
.fpb-btn {
  background: linear-gradient(90deg, #4f46e5, #3b82f6);
  color: var(--white);
  border: none;
  padding: 11px 22px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
  white-space: nowrap;
  z-index: 2;
}
.fpb-btn:hover {
  transform: scale(1.04);
  box-shadow: 0 6px 18px rgba(79, 70, 229, 0.4);
  background: linear-gradient(90deg, #6366f1, #3b82f6);
}
@media (max-width: 768px) {
  .free-placement-banner {
    flex-direction: column;
    align-items: flex-start;
    padding: 18px;
    gap: 16px;
  }
  .fpb-left {
    align-items: flex-start;
    gap: 14px;
  }
  .fpb-btn {
    width: 100%;
    text-align: center;
  }
}

/* ===== TARIFFS MODAL ===== */
.tariffs-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
  margin-top: 14px;
}
.tariff-card {
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 24px 20px;
  background: var(--white);
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  position: relative;
  transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
}
.tariff-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
}
.tariff-card.featured {
  background: #FFFDF5;
  border-color: var(--amber);
  box-shadow: 0 4px 15px rgba(245, 158, 11, 0.05);
}
.tariff-card.featured:hover {
  border-color: #D4890A;
  box-shadow: 0 12px 30px rgba(245, 158, 11, 0.12);
}
.tariff-badge {
  position: absolute;
  top: -12px;
  background: linear-gradient(135deg, var(--amber), #D4890A);
  color: var(--slate-900);
  font-size: 10px;
  font-weight: 800;
  padding: 3px 12px;
  border-radius: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 2px 6px rgba(245,158,11,0.2);
}
.tariff-icon {
  font-size: 28px;
  margin-bottom: 12px;
}
.tariff-name {
  font-size: 15px;
  font-weight: 700;
  color: var(--slate-900);
  margin-bottom: 8px;
}
.tariff-price {
  font-size: 26px;
  font-weight: 800;
  color: var(--slate-900);
  margin-bottom: 4px;
}
.tariff-price span {
  font-size: 13px;
  font-weight: 600;
  color: var(--gray-text);
}
.tariff-duration {
  font-size: 12px;
  color: var(--gray-text);
  font-weight: 500;
  margin-bottom: 16px;
}
.tariff-features {
  list-style: none;
  padding: 0;
  margin: 0 0 24px;
  font-size: 12.5px;
  color: #4b5563;
  line-height: 1.5;
  flex: 1;
  text-align: left;
  width: 100%;
}
.tariff-features li {
  margin-bottom: 8px;
  display: flex;
  align-items: flex-start;
  gap: 8px;
}
.tariff-features li::before {
  content: '✓';
  color: #10B981;
  font-weight: bold;
}
.tariff-features li.not-included::before {
  content: '✕';
  color: #EF4444;
}
.tariff-features li.not-included {
  color: #9ca3af;
  text-decoration: line-through;
}
.tariff-card-btn {
  width: 100%;
  padding: 10px 16px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  border: 1px solid var(--slate-900);
  background: var(--white);
  color: var(--slate-900);
  cursor: pointer;
  transition: all 0.15s;
}
.tariff-card-btn:hover {
  background: var(--slate-900);
  color: var(--white);
}
.tariff-card.featured .tariff-card-btn {
  background: var(--amber);
  border-color: var(--amber);
  color: var(--slate-900);
}
.tariff-card.featured .tariff-card-btn:hover {
  background: var(--amber-dark);
  border-color: var(--amber-dark);
  color: var(--white);
}

@media (max-width: 768px) {
  .tariffs-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }
  .tariffs-banner {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
    padding: 16px;
  }
}


/* Кнопка переключения мобильной карты */
.mobile-map-toggle-btn {
  display: none;
}

/* ===== RESPONSIVE MEDIA QUERIES ===== */
@media (max-width: 991px) {
  .main { flex-direction: column; min-height: auto; }
  .map-col { display: none; }
  .listings { padding: 20px 16px; }
  .grid { grid-template-columns: 1fr; gap: 20px; }
  
  .mobile-map-toggle-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    position: fixed;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--slate-900);
    color: var(--white);
    border: none;
    border-radius: 30px;
    padding: 12px 24px;
    font-size: 14px;
    font-weight: 700;
    box-shadow: 0 4px 16px rgba(26, 26, 46, 0.25);
    z-index: 1000;
    cursor: pointer;
    transition: transform 0.15s, background-color 0.15s;
  }
  .mobile-map-toggle-btn:active {
    transform: translateX(-50%) scale(0.95);
  }
  .mobile-map-toggle-btn svg {
    stroke: var(--white);
    fill: none;
    stroke-width: 2.2;
    flex-shrink: 0;
  }
  
  /* Класс для активной мобильной карты */
  .map-col.mobile-active-map {
    display: block !important;
    width: 100% !important;
    height: calc(100vh - 156px) !important;
    position: relative !important;
    top: 0 !important;
    padding: 16px !important;
  }
}

@media (max-width: 768px) {
  .filters-section {
    flex-direction: column;
    align-items: stretch;
    height: auto;
    padding: 0 16px 12px !important;
    border-bottom: 1px solid var(--border);
  }
  .filters-scroll {
    height: 64px;
    margin-left: -16px;
    margin-right: -16px;
    padding: 0 16px;
  }
  .fi {
    padding: 6px 12px;
    gap: 3px;
  }
  .fi svg {
    width: 20px;
    height: 20px;
  }
  .fi span {
    font-size: 11px;
  }
  .filter-btn-wrapper {
    margin: 4px 0 0;
    padding: 0;
    border-left: none;
    height: auto;
    display: flex;
    justify-content: flex-end;
    flex-shrink: 0;
  }
  .filter-btn {
    border: 1px solid var(--slate-900);
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 12px;
    font-weight: 600;
    background: var(--white);
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .mobile-map-toggle-btn {
    left: 16px;
    bottom: 16px;
    transform: none;
  }
  .mobile-map-toggle-btn:active {
    transform: scale(0.95);
  }
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
    font-size: 11px;
  }
  .modal-grab-bar {
    display: block;
    width: 36px;
    height: 4px;
    background: var(--slate-200);
    border-radius: 2px;
    margin: 10px auto 0;
    flex-shrink: 0;
  }
  .filter-modal-card {
    max-height: 100vh;
    border-radius: 24px 24px 0 0;
    height: 100%;
    transform: translateY(100%);
    transition: transform .35s cubic-bezier(0.19, 1, 0.22, 1);
  }
  .modal-backdrop.show .filter-modal-card {
    transform: translateY(0);
  }
  .filter-modal-header {
    border-bottom: none;
    padding: 10px 20px 14px;
  }
  .filter-modal-body {
    padding: 16px 20px;
  }
  .filter-modal-footer {
    padding: 16px 20px 28px;
  }
  .apply-filter-btn {
    flex: 1;
    margin-left: 16px;
    padding: 14px 20px;
    text-align: center;
  }
  .price-inputs {
    gap: 10px;
  }
  .price-input-wrap {
    padding: 0 10px;
  }
  .object-type-tabs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    width: 100%;
    margin-top: 8px;
  }
  .ot-tab-btn {
    width: 100%;
    text-align: center;
    padding: 10px 6px;
    font-size: 12.5px;
  }
  .filter-amenities-grid {
    grid-template-columns: 1fr;
    gap: 10px;
  }
}

/* Object type tabs style */
.object-type-tabs {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 8px;
}
.ot-tab-btn {
  background: var(--slate-50);
  border: 1px solid var(--border);
  color: var(--slate-900);
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
}
.ot-tab-btn:hover {
  background: var(--slate-100);
}
.ot-tab-btn.active {
  background: var(--slate-900);
  color: #FFF;
  border-color: var(--slate-900);
}

/* Hide/show dynamic filters */
.apt-only, .cottage-only {
  display: none !important;
}
.filter-modal-card[data-active-type="all"] label.apt-only,
.filter-modal-card[data-active-type="all"] label.cottage-only {
  display: flex !important;
}
.filter-modal-card[data-active-type="apartments"] label.apt-only,
.filter-modal-card[data-active-type="studio"] label.apt-only {
  display: flex !important;
}
.filter-modal-card[data-active-type="cottages"] label.cottage-only {
  display: flex !important;
}

.filter-modal-card[data-active-type="all"] div.apt-only,
.filter-modal-card[data-active-type="all"] div.cottage-only,
.filter-modal-card[data-active-type="all"] h5.apt-only,
.filter-modal-card[data-active-type="all"] h5.cottage-only {
  display: block !important;
}
.filter-modal-card[data-active-type="apartments"] div.apt-only,
.filter-modal-card[data-active-type="apartments"] h5.apt-only,
.filter-modal-card[data-active-type="studio"] div.apt-only,
.filter-modal-card[data-active-type="studio"] h5.apt-only {
  display: block !important;
}
.filter-modal-card[data-active-type="cottages"] div.cottage-only,
.filter-modal-card[data-active-type="cottages"] h5.cottage-only {
  display: block !important;
}
/* ===== GLOBAL TEXT SIZE INCREASE OVERRIDES ===== */
.fi span { font-size: 13px !important; }
.filter-btn { font-size: 14px !important; }
@media (max-width: 768px) {
  .fi span { font-size: 11.5px !important; }
  .filter-btn { font-size: 13px !important; }
}
.cb-title { font-size: 17px !important; }
.card-address-row, .card-metro-row { font-size: 14px !important; }
.price-amount { font-size: 16px !important; }
.price-label { font-size: 12.5px !important; }
.badge-pill { font-size: 12.5px !important; }
@media (max-width: 576px) {
  .cb-title { font-size: 15px !important; }
  .card-address-row, .card-metro-row { font-size: 13px !important; }
  .price-amount { font-size: 14.5px !important; }
  .price-label { font-size: 11.5px !important; }
  .badge-pill { font-size: 11.5px !important; }
}

.seo-tag-cloud {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-top: 16px;
  margin-bottom: 30px;
}
.seo-tag-link {
  display: inline-block;
  padding: 8px 16px;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 20px;
  color: var(--slate-900);
  font-size: 13.5px;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.2s ease;
}
.seo-tag-link:hover {
  background: var(--slate-900);
  color: var(--white);
  border-color: var(--slate-900);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.seo-spoiler-container {
  margin-top: -16px;
  margin-bottom: 32px;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow: hidden;
  transition: border-color 0.2s ease;
}
.seo-spoiler-container:hover {
  border-color: var(--slate-300);
}
.seo-spoiler-trigger {
  width: 100%;
  padding: 18px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: none;
  border: none;
  font-family: inherit;
  font-size: 15px;
  font-weight: 600;
  color: var(--slate-900);
  cursor: pointer;
}
.seo-spoiler-icon {
  transition: transform 0.3s ease;
  stroke: var(--slate-600);
}
.seo-spoiler-container.active .seo-spoiler-icon {
  transform: rotate(180deg);
}
.seo-spoiler-content {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.seo-spoiler-inner {
  padding: 0 24px 24px 24px;
  border-top: 1px dashed var(--border);
  padding-top: 18px;
}
.seo-spoiler-inner p {
  font-size: 14px;
  line-height: 1.6;
  color: var(--gray-text);
  margin-bottom: 12px;
}
.seo-spoiler-inner p:last-child {
  margin-bottom: 0;
}

/* ===== STUNNING SEO BLOCK DESIGN ===== */
.seo-text-section {
  margin-top: 56px;
  border-top: 1px solid var(--border);
  padding-top: 48px;
  color: var(--slate-900);
}

.seo-hero-card {
  background: linear-gradient(135deg, rgba(26, 26, 46, 0.02) 0%, rgba(245, 166, 35, 0.02) 100%);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 32px;
  margin-bottom: 40px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(26, 26, 46, 0.01);
}

.seo-hero-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 4px; height: 100%;
  background: linear-gradient(180deg, var(--amber) 0%, var(--amber-dark) 100%);
}

.seo-title {
  font-size: 24px;
  font-weight: 800;
  color: var(--slate-900);
  line-height: 1.3;
  letter-spacing: -0.5px;
  margin-bottom: 16px;
}

.seo-lead {
  font-size: 14.5px;
  line-height: 1.7;
  color: var(--gray-text);
  margin-bottom: 0;
}

.seo-subtitle {
  font-size: 18px;
  font-weight: 700;
  color: var(--slate-900);
  margin-top: 40px;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.seo-subtitle svg {
  width: 20px;
  height: 20px;
  stroke: var(--amber);
  fill: none;
  stroke-width: 2.2;
}

.benefits-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  margin-bottom: 40px;
}

@media (max-width: 640px) {
  .benefits-grid {
    grid-template-columns: 1fr;
    gap: 16px;
  }
}

.benefit-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 24px;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
  display: flex;
  gap: 18px;
  align-items: flex-start;
}

.benefit-card:hover {
  transform: translateY(-5px);
  border-color: var(--amber-light);
  box-shadow: 0 10px 30px rgba(245, 166, 35, 0.08);
}

.benefit-icon-wrapper {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: rgba(245, 166, 35, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: var(--amber-dark);
  transition: all 0.3s ease;
}

.benefit-card:hover .benefit-icon-wrapper {
  background: var(--amber);
  color: var(--white);
  transform: scale(1.05) rotate(5deg);
}

.benefit-icon-wrapper svg {
  width: 22px;
  height: 22px;
  stroke: currentColor;
  fill: none;
  stroke-width: 2.2;
}

.benefit-content {
  flex: 1;
}

.benefit-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--slate-900);
  margin-bottom: 6px;
}

.benefit-desc {
  font-size: 13.5px;
  line-height: 1.55;
  color: var(--gray-text);
}

/* ===== TABLE STYLING ===== */
.seo-table-wrapper {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
  margin-bottom: 40px;
}

.seo-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
  text-align: left;
}

.seo-table th {
  background: var(--slate-50);
  padding: 16px 20px;
  font-weight: 700;
  color: var(--slate-900);
  border-bottom: 1.5px solid var(--border);
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.seo-table td {
  padding: 16px 20px;
  border-bottom: 1px solid var(--border);
  color: var(--slate-900);
  vertical-align: middle;
}

.seo-table tr:last-child td {
  border-bottom: none;
}

.seo-table tr {
  transition: background-color 0.2s ease;
}

.seo-table tr:hover {
  background-color: var(--slate-50);
}

.seo-type-col {
  font-weight: 600;
}

.seo-price-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 30px;
  background: rgba(245, 166, 35, 0.08);
  color: var(--amber-dark);
  font-weight: 700;
  font-size: 13.5px;
}

.seo-suit-pill {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 20px;
  background: var(--slate-100);
  color: var(--slate-700);
  font-weight: 500;
  font-size: 12.5px;
}

/* ===== STEP TIMELINE ===== */
.steps-timeline {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
  margin-bottom: 40px;
  position: relative;
}

@media (max-width: 768px) {
  .steps-timeline {
    grid-template-columns: 1fr;
    gap: 20px;
  }
}

.step-item {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 24px;
  position: relative;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.step-item:hover {
  transform: translateY(-4px);
  border-color: var(--amber-light);
  box-shadow: 0 8px 24px rgba(245, 166, 35, 0.06);
}

.step-badge {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--amber) 0%, var(--amber-dark) 100%);
  color: var(--white);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 800;
  margin-bottom: 16px;
  box-shadow: 0 4px 10px rgba(245, 166, 35, 0.3);
}

.step-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--slate-900);
  margin-bottom: 8px;
}

.step-desc {
  font-size: 13.5px;
  line-height: 1.5;
  color: var(--gray-text);
}

/* ===== CALLOUT RECOMMENDATION ===== */
.seo-recommendation-box {
  background: linear-gradient(135deg, rgba(245, 166, 35, 0.04) 0%, rgba(255, 255, 255, 0) 100%);
  border: 1px solid rgba(245, 166, 35, 0.2);
  border-left: 5px solid var(--amber);
  border-radius: 12px;
  padding: 20px 24px;
  margin-bottom: 24px;
  display: flex;
  gap: 16px;
  align-items: flex-start;
}

.seo-rec-icon {
  color: var(--amber-dark);
  font-size: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 2px;
}

.seo-rec-text {
  font-size: 13.5px;
  line-height: 1.6;
  color: var(--slate-900);
  margin: 0;
}

@media (max-width: 768px) {
  .seo-table {
    min-width: 600px;
  }
}

/* ===== HOMEPAGE HERO SECTION ===== */
.homepage-hero {
  position: relative;
  background: #0f172a;
  padding: 80px 24px 100px;
  color: var(--white);
  overflow: hidden;
  text-align: center;
  border-bottom: 1px solid var(--border);
  margin-bottom: 32px;
  border-radius: 24px;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}
.hero-carousel {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1;
}
.hero-slide {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  opacity: 0;
  z-index: 1;
  transition: opacity 1.2s ease-in-out, transform 10s linear;
  transform: scale(1.0);
}
.hero-slide.active {
  opacity: 1;
  z-index: 2;
  transform: scale(1.06);
}
.hero-slide-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(180deg, rgba(15, 23, 42, 0.4) 0%, rgba(15, 23, 42, 0.75) 100%);
}
.hero-backdrop-gradient {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: radial-gradient(circle at 80% 20%, rgba(245, 166, 35, 0.08) 0%, rgba(0,0,0,0) 65%);
  pointer-events: none;
  z-index: 3;
}
.hero-container {
  max-width: 860px;
  margin: 0 auto;
  position: relative;
  z-index: 4;
}
.hero-title {
  font-size: 36px;
  font-weight: 800;
  letter-spacing: -0.5px;
  line-height: 1.25;
  margin-bottom: 12px;
  color: #ffffff;
}
@media (max-width: 768px) {
  .hero-title {
    font-size: 26px;
  }
}
.hero-subtitle-text {
  font-size: 16px;
  color: rgba(255,255,255,0.85);
  margin-bottom: 36px;
}

/* Carousel Navigation Controls */
.hero-carousel-control {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.12);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: var(--white);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 5;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  outline: none;
}
.hero-carousel-control:hover {
  background: rgba(255, 255, 255, 0.25);
  border-color: rgba(255, 255, 255, 0.4);
  transform: translateY(-50%) scale(1.08);
}
.hero-carousel-control svg {
  width: 24px;
  height: 24px;
  stroke: currentColor;
  stroke-width: 2.5;
  fill: none;
  stroke-linecap: round;
  stroke-linejoin: round;
}
.hero-carousel-control.prev {
  left: 24px;
}
.hero-carousel-control.next {
  right: 24px;
}
@media (max-width: 768px) {
  .hero-carousel-control {
    display: none;
  }
}

/* Carousel Indicators */
.hero-carousel-dots {
  position: absolute;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 8px;
  z-index: 5;
}
.hero-carousel-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.4);
  cursor: pointer;
  transition: all 0.3s ease;
}
.hero-carousel-dot.active {
  width: 24px;
  border-radius: 4px;
  background: var(--white);
}
.hero-search-bar {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 40px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  display: flex;
  align-items: center;
  height: 64px;
  padding: 0 6px 0 20px;
  text-align: left;
}
@media (max-width: 991px) {
  .hero-search-bar {
    flex-direction: column;
    height: auto;
    border-radius: 20px;
    padding: 14px;
    gap: 12px;
  }
  .hero-search-div {
    display: none !important;
  }
  .hero-search-seg {
    width: 100% !important;
    padding: 0 !important;
    height: 48px !important;
  }
  .hero-search-btn {
    width: 100% !important;
    height: 48px !important;
    border-radius: 12px !important;
    justify-content: center;
  }
}
.hero-search-seg {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  cursor: pointer;
  height: 100%;
  position: relative;
  padding: 0 12px;
}
.hero-search-seg:first-child {
  padding-left: 0;
}
.hero-seg-label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  color: var(--slate-900);
  letter-spacing: 0.5px;
  margin-bottom: 2px;
}
.hero-seg-val {
  font-size: 13.5px;
  color: var(--gray-text);
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.hero-date-input {
  border: none;
  background: transparent;
  font-size: 13.5px;
  color: var(--slate-900);
  font-weight: 600;
  padding: 0;
  width: 100%;
  cursor: pointer;
}
.hero-date-input:focus {
  outline: none;
}
.hero-search-div {
  width: 1px;
  height: 32px;
  background: var(--border);
  flex-shrink: 0;
}
.hero-search-btn {
  background: linear-gradient(135deg, var(--amber) 0%, var(--amber-dark) 100%);
  border: none;
  height: 52px;
  padding: 0 24px;
  border-radius: 26px;
  display: flex;
  align-items: center;
  gap: 6px;
  color: var(--white);
  font-weight: 700;
  font-size: 14.5px;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(245, 166, 35, 0.25);
  cursor: pointer;
}
.hero-search-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(245, 166, 35, 0.4);
}
.hero-search-btn svg {
  width: 16px;
  height: 16px;
  stroke: var(--white);
  fill: none;
  stroke-width: 2.5;
}

/* Hero Dropdown Menu */
.hero-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 8px;
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
  display: none;
  flex-direction: column;
  padding: 6px 0;
  z-index: 1500;
  text-align: left;
  min-width: 200px;
  max-height: 280px;
  overflow-y: auto;
}
.hero-dropdown.show {
  display: flex;
}
.hero-dropdown-item {
  padding: 8px 14px;
  font-size: 13px;
  color: var(--slate-900);
  cursor: pointer;
  transition: background 0.1s, color 0.1s;
  border-radius: 6px;
  margin: 1px 6px;
  font-weight: 500;
}
.hero-dropdown-item:hover {
  background: var(--slate-100);
}
.hero-dropdown-item.active {
  background: var(--amber);
  color: var(--white);
}
.hero-dropdown-divider {
  height: 1px;
  background: var(--border);
  margin: 4px 6px;
}

/* City Modal item styles */
.city-modal-item {
  color: var(--slate-900);
  background: var(--white);
  border: 1px solid var(--border);
}
.city-modal-item:hover {
  background: var(--slate-50);
  border-color: var(--slate-300);
}
.city-modal-item.active {
  background: var(--amber) !important;
  color: var(--white) !important;
  border-color: var(--amber) !important;
}
.city-modal-all-item {
  border-color: transparent;
  background: transparent;
}
.city-modal-all-item:hover {
  background: var(--slate-100);
}
#city-modal-all-list::-webkit-scrollbar {
  width: 6px;
}
#city-modal-all-list::-webkit-scrollbar-track {
  background: transparent;
}
#city-modal-all-list::-webkit-scrollbar-thumb {
  background: var(--border);
  border-radius: 3px;
}
@media (max-width: 768px) {
  .city-modal-popular-grid, .city-modal-regions-grid, #city-modal-all-list {
    grid-template-columns: repeat(2, 1fr) !important;
  }
}

/* ===== QUICK INLINE FILTERS BAR ===== */
.quick-filters-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 24px;
  position: relative;
}
.qf-pill {
  position: relative;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 7px 14px;
  font-size: 13px;
  font-weight: 600;
  color: var(--slate-900);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 4px;
  transition: all 0.15s ease;
  user-select: none;
}
.qf-pill:hover {
  border-color: var(--slate-300);
  background: var(--slate-50);
}
.qf-pill.active {
  background: var(--slate-900);
  color: var(--white);
  border-color: var(--slate-900);
}
.qf-pill.active svg {
  stroke: var(--white);
}
.qf-pill svg {
  width: 12px;
  height: 12px;
  stroke: var(--slate-900);
  fill: none;
  stroke-width: 2.2;
  transition: transform 0.15s ease;
}
.qf-pill.open svg {
  transform: rotate(180deg);
}
.qf-divider {
  width: 1px;
  height: 20px;
  background: var(--border);
  margin: 0 4px;
}
.qf-tag {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 7px 14px;
  font-size: 13px;
  font-weight: 600;
  color: var(--slate-900);
  cursor: pointer;
  transition: all 0.15s ease;
  user-select: none;
  white-space: nowrap;
  flex-shrink: 0;
}
.qf-tag:hover {
  border-color: var(--slate-300);
  background: var(--slate-50);
}
.qf-tag.active {
  background: var(--amber);
  color: var(--white);
  border-color: var(--amber);
}
.qf-link-tag {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 7px 14px;
  font-size: 13px;
  font-weight: 600;
  color: var(--slate-900);
  cursor: pointer;
  transition: all 0.15s ease;
  user-select: none;
  white-space: nowrap;
  flex-shrink: 0;
  text-decoration: none;
  display: inline-block;
}
.qf-link-tag:hover {
  border-color: var(--slate-300);
  background: var(--slate-50);
  color: var(--slate-900);
}
.qf-link-tag.active {
  background: var(--slate-900);
  color: var(--white);
  border-color: var(--slate-900);
}

/* Quick Filter Popups */
.qf-popup {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 6px;
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
  padding: 16px;
  display: none;
  flex-direction: column;
  gap: 10px;
  z-index: 1000;
  min-width: 240px;
}
.qf-popup.show {
  display: flex;
}
.qf-popup-title {
  font-size: 12px;
  font-weight: 700;
  color: var(--slate-900);
}
.qf-popup-inputs {
  display: flex;
  align-items: center;
  gap: 6px;
}
.qf-popup-inputs input {
  width: 80px;
  height: 34px;
  border: 1px solid var(--border);
  border-radius: 6px;
  padding: 0 10px;
  font-size: 13px;
  color: var(--slate-900);
}
.qf-popup-inputs input:focus {
  outline: none;
  border-color: var(--amber);
}
.qf-input-div {
  color: var(--gray-text);
  font-size: 13px;
}
.qf-popup-btn {
  background: var(--slate-900);
  color: var(--white);
  border: none;
  height: 32px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.1s;
}
.qf-popup-btn:hover {
  background: var(--slate-800);
}

/* Rooms Select */
.qf-rooms-selector {
  display: flex;
  gap: 4px;
}
.qf-room-btn {
  flex: 1;
  height: 32px;
  border: 1px solid var(--border);
  border-radius: 6px;
  background: var(--white);
  color: var(--slate-900);
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.1s;
}
.qf-room-btn:hover {
  border-color: var(--slate-300);
}
.qf-room-btn.active {
  background: var(--slate-900);
  color: var(--white);
  border-color: var(--slate-900);
}

/* ===== POPULAR CITIES GRID ===== */
.popular-cities-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 32px;
}
@media (max-width: 768px) {
  .popular-cities-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
}
@media (max-width: 480px) {
  .popular-cities-grid {
    grid-template-columns: 1fr;
    gap: 10px;
  }
}
.city-tile {
  position: relative;
  height: 140px;
  border-radius: 12px;
  overflow: hidden;
  display: block;
  box-shadow: 0 4px 10px rgba(0,0,0,0.04);
  transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
}
.city-tile:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.08);
}
.city-tile img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}
.city-tile:hover img {
  transform: scale(1.04);
}
.city-tile-overlay {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background: linear-gradient(180deg, rgba(26,26,46,0) 30%, rgba(26,26,46,0.8) 100%);
}
.city-tile-info {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  padding: 12px;
  color: var(--white);
}
.city-tile-info h4 {
  font-size: 15px;
  font-weight: 700;
  margin: 0 0 2px 0;
}
.city-tile-info p {
  font-size: 11px;
  opacity: 0.8;
  margin: 0;
}

/* ===== REVIEWS WIDGET ===== */
.reviews-widget-scroll {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
  margin-bottom: 32px;
}
@media (max-width: 991px) {
  .reviews-widget-scroll {
    grid-template-columns: 1fr;
    gap: 12px;
  }
}
.review-widget-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.01);
  transition: transform 0.2s ease, border-color 0.2s ease;
  display: flex;
  flex-direction: column;
}
.review-widget-card:hover {
  transform: translateY(-1px);
  border-color: var(--amber-light);
}
.rw-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 10px;
}
.rw-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 13px;
}
.rw-meta {
  flex: 1;
}
.rw-name {
  font-weight: 700;
  font-size: 13px;
  color: var(--slate-900);
}
.rw-date {
  font-size: 11px;
  color: var(--gray-text);
  margin-top: 1px;
}
.rw-content {
  font-size: 12.5px;
  line-height: 1.5;
  color: var(--gray-text);
  font-style: italic;
  margin-bottom: 12px;
  flex: 1;
}
.rw-listing-link {
  font-size: 12px;
  font-weight: 600;
  color: var(--amber-dark);
  text-decoration: none;
  display: inline-flex;
  align-items: center;
}
.rw-listing-link:hover {
  text-decoration: underline;
}
</style>
<script type="text/javascript">
  window.ymapsLoaded = false;
  window.initYmaps = function() {
    window.ymapsLoaded = true;
    if (typeof window.initMap === 'function') {
      ymaps.ready(window.initMap);
    }
  };

  // Dynamically load Yandex Maps after page load to prevent main thread blocking (TBT/FID optimization)
  window.addEventListener('load', function() {
    setTimeout(function() {
      const script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = "https://api-maps.yandex.ru/2.1/?apikey={{ env('YANDEX_MAPS_API_KEY') }}&lang=ru_RU&onload=initYmaps";
      script.async = true;
      script.defer = true;
      document.head.appendChild(script);
    }, 1500);
  });
</script>
@endsection

@section('content')


<!-- FILTERS SECTION -->
<div class="filters-section" style="position: relative;">
  <div class="quick-filters-scroll" style="display: flex; flex-wrap: nowrap; align-items: center; gap: 8px; height: 100%; overflow-x: auto; scrollbar-width: none; flex: 1;">
    <!-- Toggle Tags -->
    <div class="qf-tag" data-tag="animals_allowed" onclick="toggleQfTag('animals_allowed')">С животными</div>
    @php
      $isApartmentsSection = (isset($seoCategory) && $seoCategory === 'apartments') || request('type') === 'apartments' || (isset($selectedType) && $selectedType === 'apartments') || (isset($seoCategory) && $seoCategory === 'hourly') || request('type') === 'hourly' || (isset($selectedType) && $selectedType === 'hourly');
    @endphp
    <div class="qf-tag" data-tag="children_allowed" onclick="toggleQfTag('children_allowed')" id="qf-tag-children" style="display: {{ $isApartmentsSection ? 'inline-block' : 'none' }};">С детьми</div>
    <div class="qf-tag" data-tag="русская баня" onclick="toggleQfTag('русская баня')" id="qf-tag-sauna" style="display: {{ $isApartmentsSection ? 'none' : 'inline-block' }};">Баня</div>
    <div class="qf-tag" data-tag="бассейн" onclick="toggleQfTag('бассейн')" id="qf-tag-pool" style="display: {{ $isApartmentsSection ? 'none' : 'inline-block' }};">Бассейн</div>
    <div class="qf-tag" data-tag="бесконтактное заселение" onclick="toggleQfTag('бесконтактное заселение')">Бесконтактно</div>
    <div class="qf-divider"></div>
    @if(isset($selectedCity))
      @php
        $citySlugSeo = \App\Helpers\CityHelper::slugify($selectedCity);
        
        $topSeoTags = [
            ['url' => "/{$citySlugSeo}/odnokomnatnye", 'text' => '1-комнатная'],
            ['url' => "/{$citySlugSeo}/dvuhkomnatnye", 'text' => '2-комнатная'],
            ['url' => "/{$citySlugSeo}/trehkomnatnye", 'text' => '3-комнатная'],
            ['url' => "/{$citySlugSeo}/chetyrehkomnatnye", 'text' => '4-комнатная'],
            ['url' => "/{$citySlugSeo}/v-centre", 'text' => 'В центре'],
        ];
        if (mb_strtolower($selectedCity) === 'минск') {
            $topSeoTags[] = ['url' => "/{$citySlugSeo}/metro", 'text' => 'Метро'];
        }
        $topSeoTags = array_merge($topSeoTags, [
            ['url' => "/{$citySlugSeo}/na-chasy", 'text' => 'На часы'],
            ['url' => "/{$citySlugSeo}/dzhakuzi", 'text' => 'Джакузи'],
            ['url' => "/{$citySlugSeo}/kottedzhi", 'text' => 'Коттеджи'],
            ['url' => "/{$citySlugSeo}/s-baney", 'text' => 'С баней'],
            ['url' => "/{$citySlugSeo}/s-basseynom", 'text' => 'С бассейном'],
        ]);
      @endphp
      @foreach($topSeoTags as $tag)
        @php
          $isActive = request()->is(trim($tag['url'], '/'));
        @endphp
        <a href="{{ $tag['url'] }}" class="qf-link-tag {{ $isActive ? 'active' : '' }}">{{ $tag['text'] }}</a>
      @endforeach
    @else
      @php
        $topCityLinks = [
            ['url' => '/minsk', 'text' => 'Минск'],
            ['url' => '/brest', 'text' => 'Брест'],
            ['url' => '/grodno', 'text' => 'Гродно'],
            ['url' => '/gomel', 'text' => 'Гомель'],
            ['url' => '/vitebsk', 'text' => 'Витебск'],
            ['url' => '/mogilev', 'text' => 'Могилев'],
        ];
        $reqType = request('type') ?? (isset($selectedType) ? $selectedType : null);
        if (isset($seoCategory)) {
            if ($seoCategory === 'cottages' || $reqType === 'cottages') {
                foreach ($topCityLinks as &$l) { $l['url'] .= '/kottedzhi'; }
            } elseif ($seoCategory === 'hourly' || $reqType === 'hourly') {
                foreach ($topCityLinks as &$l) { $l['url'] .= '/na-chasy'; }
            } elseif ($seoCategory === 'apartments' || $reqType === 'apartments') {
                foreach ($topCityLinks as &$l) { $l['url'] .= '/kvartiry'; }
            }
        }
      @endphp
      @foreach ($topCityLinks as $link)
        @php
          $isActive = request()->is(trim($link['url'], '/'));
        @endphp
        <a href="{{ $link['url'] }}" class="qf-link-tag {{ $isActive ? 'active' : '' }}">{{ $link['text'] }}</a>
      @endforeach
    @endif
  </div>
</div>

<!-- MAIN -->
<div class="main">

  <!-- LISTINGS -->
  <div class="listings">
    @php
      $pageH1 = $seoH1 ?? (isset($selectedCity) && isset($inCity) ? 'Аренда квартир и коттеджей ' . $inCity . ' посуточно' : 'Аренда квартир и коттеджей посуточно в Беларуси');
    @endphp
    <div id="breadcrumbs-wrapper">
      @include('listings.partials.breadcrumbs')
    </div>
    <h1 class="page-main-title" id="page-main-title" style="font-family: 'Syne', Georgia, serif; font-size: 24px; font-weight: 800; color: var(--slate-900); margin-bottom: 8px; line-height: 1.2; letter-spacing: -0.5px;">{{ $pageH1 }}</h1>
    @if(isset($landingPage) && !empty($landingPage['intro']))
      <div class="landing-intro-box" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-left: 4px solid var(--amber); border-radius: 12px; padding: 20px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); text-align: left;">
        <p style="font-size: 15px; line-height: 1.6; color: var(--slate-700); margin: 0; font-weight: 500;">
          {{ $landingPage['intro'] }}
        </p>
      </div>
    @endif
    <p class="top-info"><b id="listings-count" style="font-weight: normal;">0 вариантов</b> в пределах карты · <span>Цены без скрытых сборов</span></p>



    <div class="grid" id="listings-grid">
      @include('listings.partials.grid')
    </div><!-- /grid -->

    <div id="listings-pagination" class="pagination-container"></div>

    <!-- SEO TEXT BLOCK -->
    @php
      $inCitySeo = isset($inCity) ? $inCity : 'в Минске и Беларуси';
      $selectedCitySeo = isset($selectedCity) ? $selectedCity : 'Беларусь';

      // Определяем категорию для SEO-блока
      $seoBlockType = 'general';
      if (isset($seoCategory)) {
          if (in_array($seoCategory, ['apartments', 'dzhakuzi', 'jacuzzi', 'odnokomnatnaya', '1-room', 'dvuhkomnatnaya', '2-room', 'trehkomnatnaya', '3-room', 'chetyrehkomnatnaya', '4-room', 'vokzal', 'near-station', 'metro', 'near-metro'])) {
              $seoBlockType = 'apartments';
          } elseif (in_array($seoCategory, ['cottages', 's-baney', 'with-sauna', 's-basseynom', 'with-pool'])) {
              $seoBlockType = 'cottages';
          } elseif ($seoCategory === 'hourly') {
              $seoBlockType = 'hourly';
          }
      } else {
          $reqType = request('type') ?? (isset($selectedType) ? $selectedType : null);
          if (in_array($reqType, ['apartments', 'apartment'])) {
              $seoBlockType = 'apartments';
          } elseif (in_array($reqType, ['cottages', 'cottage', 'agrotourism'])) {
              $seoBlockType = 'cottages';
          } elseif (in_array($reqType, ['hourly', 'hour'])) {
              $seoBlockType = 'hourly';
          }
      }

      // Тексты в зависимости от категории
      if ($seoBlockType === 'apartments') {
          $seoBlockTitle = "Снять квартиру на сутки " . $inCitySeo . " без посредников";
          $seoBlockLead = "Ищете комфортное жилье для деловой поездки, семейного путешествия или личного отдыха? На Flatly.by вы найдете широкий выбор квартир и апартаментов посуточно в аренду напрямую от хозяев " . (isset($inCity) ? $inCity : 'в Беларуси') . ". Просматривайте реальные фотографии, общайтесь напрямую с владельцами без комиссий агентствам и бронируйте подходящее жилье на интерактивной карте.";
          $seoBlockText1 = "Посуточная аренда квартир — это отличная альтернатива отелям, предлагающая больше пространства, домашний уют и наличие собственной кухни. В нашем каталоге представлены как недорогие однокомнатные квартиры и студии, так и элитные апартаменты в центре города. Вы можете подобрать жилье с нужным набором удобств: кондиционером, стиральной машиной, Wi-Fi и даже джакузи.";
          $seoBlockText2 = "Стоимость аренды квартиры на сутки зависит от ее расположения, количества комнат и сезона. Связываясь напрямую с хозяевами через наш сайт, вы избегаете дополнительных сборов и можете лично обсудить условия заселения, скидки за длительное проживание и предоставление отчетных документов.";
          $seoRatesTitle = "Стоимость аренды квартир посуточно (средние тарифы)";
          $seoRates = [
              ['type' => 'Однокомнатные квартиры / Студии', 'price' => 'от 55 Б̶', 'suit' => 'Для соло-путешественников и пар'],
              ['type' => 'Двухкомнатные квартиры', 'price' => 'от 85 Б̶', 'suit' => 'Для семей с детьми и командированных'],
              ['type' => 'Просторные апартаменты (3+ комнат)', 'price' => 'от 120 Б̶', 'suit' => 'Для больших компаний и деловых поездок']
          ];
      } elseif ($seoBlockType === 'cottages') {
          $seoBlockTitle = "Снять коттедж или усадьбу на сутки " . $inCitySeo . " напрямую от владельцев";
          $seoBlockLead = "Планируете загородный отдых в кругу семьи, празднование юбилея или проведение корпоратива? Flatly.by поможет вам арендовать комфортабельный коттедж, дом или агроусадьбу посуточно напрямую от хозяев " . (isset($inCity) ? $inCity : 'в Беларуси') . ". Выбирайте лучшие варианты с баней, бассейном, банкетным залом и зоной для барбекю на нашей удобной интерактивной карте без переплат и скрытых комиссий.";
          $seoBlockText1 = "Аренда коттеджа или загородного дома на сутки — идеальное решение для больших компаний и любителей отдыха на природе. В каталоге Flatly представлены современные коттеджи в пригороде и живописные агроусадьбы в экологически чистых районах. Многие объекты оборудованы всеми удобствами для комфортного пребывания: от современной бытовой техники до караоке и детских площадок.";
          $seoBlockText2 = "Вы можете арендовать дом на выходные или будние дни. Цена аренды зависит от вместимости дома, наличия бани или бассейна, а также удаленности от города. Общаясь напрямую с владельцем, вы можете согласовать все детали мероприятия, заказать дополнительные услуги (например, организацию питания) и сэкономить на комиссиях.";
          $seoRatesTitle = "Стоимость аренды коттеджей и домов на сутки (средние тарифы)";
          $seoRates = [
              ['type' => 'Небольшие гостевые дома / Дачи', 'price' => 'от 100 Б̶', 'suit' => 'Для семейного отдыха на выходных'],
              ['type' => 'Уютные агроусадьбы с баней', 'price' => 'от 150 Б̶', 'suit' => 'Для традиционного загородного отдыха'],
              ['type' => 'Большие коттеджи с банкетным залом', 'price' => 'от 300 Б̶', 'suit' => 'Для свадеб, юбилеев и корпоративов'],
              ['type' => 'Коттеджи премиум-класса с бассейном', 'price' => 'от 450 Б̶', 'suit' => 'Для комфортабельного отдыха премиум-уровня']
          ];
      } elseif ($seoBlockType === 'hourly') {
          $seoBlockTitle = "Аренда квартир на часы " . $inCitySeo . " на выгодных условиях";
          $seoBlockLead = "Необходимо уединенное место для отдыха на несколько часов, романтической встречи или короткой паузы во время поездки? На Flatly.by вы найдете актуальные предложения аренды квартир на часы (почасово) напрямую от хозяев " . (isset($inCity) ? $inCity : 'в Беларуси') . ". Быстрый поиск по районам, прямые контакты владельцев и конфиденциальность заселения гарантированы.";
          $seoBlockText1 = "Почасовая аренда квартир — это практичное и экономное решение, когда вам не нужно оплачивать полные сутки проживания. Вы платите только за фактически проведенное время (обычно от 2-3 часов). В каталоге представлены уютные квартиры в шаговой доступности от метро и крупных транспортных развязок.";
          $seoBlockText2 = "Квартиры на часы укомплектованы всем необходимым для приятного времяпрепровождения: чистым постельным бельем, полотенцами, посудой и бесплатным интернетом. Выбирайте подходящий вариант на карте и связывайтесь с собственником напрямую по телефону или в мессенджерах для быстрого заселения.";
          $seoRatesTitle = "Стоимость почасовой аренды квартир (средние тарифы)";
          $seoRates = [
              ['type' => 'Однокомнатные квартиры / Студии', 'price' => 'от 15 Б̶ / час', 'suit' => 'Для короткого отдыха и встреч'],
              ['type' => 'Двухкомнатные квартиры', 'price' => 'от 20 Б̶ / час', 'suit' => 'Для деловых встреч и конфиденциального отдыха'],
              ['type' => 'Люксы и апартаменты с джакузи', 'price' => 'от 25 Б̶ / час', 'suit' => 'Для романтических вечеров и праздников']
          ];
      } else {
          $seoBlockTitle = "Снять квартиру посуточно или коттедж " . $inCitySeo . " без посредников";
          $seoBlockLead = "Ищете уютное жилье для семейного отдыха, краткосрочной командировки или загородного мероприятия? На Flatly.by вы можете быстро арендовать квартиру на сутки или снять комфортный загородный коттедж напрямую у владельцев " . (isset($inCity) ? $inCity : 'в Беларуси') . ". Никаких скрытых платежей, комиссий агентствам и переплат — только честные цены, интерактивная карта и прямые контакты хозяев в один клик.";
          $seoBlockText1 = "Планируете поездку и хотите выгодно снять квартиру или уютные загородные апартаменты? Наш сервис Flatly.by предоставляет удобный каталог жилья, где доступна прямая аренда без посредников. Вы можете выбрать подходящее жилье и снять его на сутки, часы или более длительный срок напрямую у собственников в любом районе.";
          $seoBlockText2 = "Итоговая цена за сутки зависит от местоположения объекта, количества комнат, уровня ремонта и наличия дополнительных удобств, таких как баня, джакузи или парковка во дворе. Выбирая посуточное жилье на нашей платформе, вы экономите на комиссиях агентств и получаете актуальные контакты хозяев для быстрого заселения.";
          $seoRatesTitle = "Стоимость аренды жилья посуточно (средние тарифы)";
          $seoRates = [
              ['type' => 'Однокомнатные квартиры / Студии', 'price' => 'от 55 Б̶', 'suit' => 'Для соло-путешественников и пар'],
              ['type' => 'Двухкомнатные квартиры', 'price' => 'от 85 Б̶', 'suit' => 'Для семей с детьми и командированных'],
              ['type' => 'Просторные апартаменты (3+ комнат)', 'price' => 'от 120 Б̶', 'suit' => 'Для больших компаний и деловых поездок'],
              ['type' => 'Коттеджи, усадьбы и дачи', 'price' => 'от 150 Б̶', 'suit' => 'Для праздников, загородного отдыха и свадеб']
          ];
      }
    @endphp

    <div class="seo-text-section">
      @if(isset($landingPage))
        <!-- SEO Article from Docs -->
        @if(!empty($landingPage['content_html']))
          <div class="seo-article-card" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 32px; margin-bottom: 32px; box-shadow: 0 4px 16px rgba(0,0,0,0.02); text-align: left; line-height: 1.7; color: var(--slate-700);">
            {!! str_replace(['\n', '\r'], ["\n", ''], $landingPage['content_html']) !!}
          </div>
        @endif

        <!-- Преимущества района (если заданы) -->
        @if(isset($landingPage['advantages']))
          <h3 class="seo-subtitle" style="display: flex; align-items: center; gap: 8px; font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 800; color: var(--slate-900); margin-bottom: 20px;">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--amber-dark);"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Главные преимущества района
          </h3>
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-bottom: 40px;">
            @foreach($landingPage['advantages'] as $adv)
              <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 16px; padding: 20px; text-align: left; box-shadow: 0 4px 12px rgba(0,0,0,0.01); display: flex; gap: 12px; align-items: flex-start;">
                <div style="font-size: 20px; flex-shrink: 0; line-height: 1;">✔️</div>
                <p style="font-size: 14.5px; line-height: 1.55; color: var(--slate-700); margin: 0; font-weight: 600;">{{ $adv }}</p>
              </div>
            @endforeach
          </div>
        @endif

        <!-- Таблица цен (если задана) -->
        @if(isset($landingPage['price_table']))
          <h3 class="seo-subtitle" style="display: flex; align-items: center; gap: 8px; font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 800; color: var(--slate-900); margin-bottom: 20px;">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--amber-dark);"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Средняя стоимость аренды жилья посуточно
          </h3>
          <div style="overflow-x: auto; background: var(--white); border: 1px solid var(--border); border-radius: 16px; margin-bottom: 40px; box-shadow: 0 4px 12px rgba(0,0,0,0.01);">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
              <thead>
                <tr style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                  <th style="padding: 16px 20px; font-family: 'Outfit', sans-serif; font-size: 14px; font-weight: 800; color: var(--slate-900);">Тип жилья</th>
                  <th style="padding: 16px 20px; font-family: 'Outfit', sans-serif; font-size: 14px; font-weight: 800; color: var(--slate-900); text-align: right;">Ориентировочная цена за сутки</th>
                </tr>
              </thead>
              <tbody>
                @foreach($landingPage['price_table'] as $row)
                  <tr style="border-bottom: 1px solid var(--border); transition: background 0.15s;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background='none'">
                    <td style="padding: 16px 20px; font-size: 14px; font-weight: 600; color: var(--slate-700);">{{ $row['type'] }}</td>
                    <td style="padding: 16px 20px; font-size: 14px; font-weight: 700; color: var(--amber-dark); text-align: right;">{{ $row['price'] }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif

        <!-- Секция Советов для НЧ-страниц -->
        @if(isset($landingPage['tips']) && is_array($landingPage['tips']))
          <h3 class="seo-subtitle" style="display: flex; align-items: center; gap: 8px; font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 800; color: var(--slate-900); margin-bottom: 20px;">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--amber-dark);"><polyline points="20 6 9 17 4 12"/></svg>
            Полезные советы при выборе и бронировании
          </h3>
          
          <div style="display: grid; grid-template-columns: 1fr; gap: 16px; margin-bottom: 40px;">
            @foreach($landingPage['tips'] as $index => $tip)
              <div style="background: var(--white); border: 1px solid var(--border); border-radius: 16px; padding: 18px 20px; display: flex; gap: 16px; align-items: flex-start; text-align: left; box-shadow: 0 4px 12px rgba(0,0,0,0.01);">
                <div style="width: 28px; height: 28px; border-radius: 50%; background: #fffbeb; border: 1px solid rgba(245,166,35,0.3); color: var(--amber-dark); font-weight: 800; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                  {{ $index + 1 }}
                </div>
                <p style="font-size: 14.5px; line-height: 1.55; color: var(--slate-700); margin: 0; font-weight: 500;">
                  {{ $tip }}
                </p>
              </div>
            @endforeach
          </div>
        @endif

        <!-- Секция FAQ для НЧ-страниц -->
        @if(isset($landingPage['faq']) && is_array($landingPage['faq']))
          <h3 class="seo-subtitle" style="display: flex; align-items: center; gap: 8px; font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 800; color: var(--slate-900); margin-bottom: 20px;">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" style="color: var(--amber-dark);"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            Часто задаваемые вопросы (FAQ)
          </h3>

          <div class="faq-wrap" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 48px;">
            @foreach($landingPage['faq'] as $faqIndex => $item)
              <div class="faq-item" style="border: 1px solid var(--border); border-radius: 12px; background: var(--white); overflow: hidden; transition: all 0.2s;">
                <button class="faq-trigger" onclick="toggleFaq(this)" style="width: 100%; background: none; border: none; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center; text-align: left; cursor: pointer; outline: none;">
                  <span class="faq-question" style="font-size: 15px; font-weight: 700; color: var(--slate-900);">{{ $item['question'] }}</span>
                  <svg class="faq-icon" viewBox="0 0 24 24" style="width: 20px; height: 20px; stroke: var(--slate-900); stroke-width: 2.5; fill: none; transition: transform 0.25s; flex-shrink: 0; margin-left: 12px;"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div class="faq-panel" style="max-height: 0; overflow: hidden; transition: max-height 0.25s cubic-bezier(0.4, 0, 0.2, 1);">
                  <div class="faq-content" style="padding: 0 24px 20px; font-size: 14px; color: var(--gray-text); line-height: 1.6; text-align: left;">
                    {{ $item['answer'] }}
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif

        @php
          $citySlugSeo = isset($selectedCity) ? \App\Helpers\CityHelper::slugify($selectedCity) : '';
          $cityInNameSeo = isset($selectedCity) ? \App\Helpers\CityHelper::getInCityName($selectedCity) : 'в Беларуси';
          $catalogAllLink = $citySlugSeo ? '/' . $citySlugSeo : (isset($seoCategory) && $seoCategory === 'cottages' ? '/cottages' : '/apartments');
          $catalogAllText = isset($seoCategory) && ($seoCategory === 'cottages' || str_contains($seoCategory, 'cottage')) ? 'Смотреть все коттеджи ' . $cityInNameSeo : 'Смотреть все квартиры ' . $cityInNameSeo;
        @endphp
        <div style="text-align: center; margin-top: 36px; margin-bottom: 24px;">
          <a href="{{ $catalogAllLink }}" class="primary-action-btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; padding: 12px 32px; font-size: 15px; font-weight: 700; width: auto; max-width: 340px; margin: 0 auto; box-shadow: 0 4px 14px rgba(245, 166, 35, 0.25);">
            {{ $catalogAllText }}
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </a>
        </div>
      @else
        <!-- Hero Intro Card -->
        <div class="seo-hero-card">
          <h2 class="seo-title">{{ $seoBlockTitle }}</h2>
          <p class="seo-lead">
            {{ $seoBlockLead }}
          </p>
        </div>

      <!-- Collapsible SEO Spoiler -->
      <div class="seo-spoiler-container">
        <button class="seo-spoiler-trigger" onclick="toggleSeoSpoiler(event)">
          <span>Подробнее об аренде {{ $inCitySeo }}</span>
          <svg class="seo-spoiler-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        
        <div class="seo-spoiler-content" id="seo-spoiler-content">
          <div class="seo-spoiler-inner">
            <p>
              {!! str_replace(['снять квартиру', 'аренда без посредников', 'на сутки', 'цена за сутки'], ['<strong>снять квартиру</strong>', '<strong>аренда без посредников</strong>', '<strong>на сутки</strong>', '<strong>цена за сутки</strong>'], $seoBlockText1) !!}
            </p>
            <p>
              {!! str_replace(['снять квартиру', 'аренда без посредников', 'на сутки', 'цена за сутки'], ['<strong>снять квартиру</strong>', '<strong>аренда без посредников</strong>', '<strong>на сутки</strong>', '<strong>цена за сутки</strong>'], $seoBlockText2) !!}
            </p>
          </div>
        </div>
      </div>

      <!-- Section: Benefits -->
      <h3 class="seo-subtitle">
        <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        Преимущества посуточной аренды на Flatly.by
      </h3>
      <p style="font-size: 14.5px; line-height: 1.6; color: var(--gray-text); margin-bottom: 24px;">
        Забудьте об утомительных звонках посредникам и переплатах на международных сервисах бронирования. Мы создали платформу, которая делает аренду жилья простой и прозрачной:
      </p>

      <div class="benefits-grid">
        <!-- Benefit 1 -->
        <div class="benefit-card">
          <div class="benefit-icon-wrapper">
            <svg viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
          </div>
          <div class="benefit-content">
            <h4 class="benefit-title">Прямое общение с хозяевами</h4>
            <p class="benefit-desc">Связывайтесь в один клик через удобные мессенджеры (Telegram, Viber, WhatsApp) или по телефону.</p>
          </div>
        </div>

        <!-- Benefit 2 -->
        <div class="benefit-card">
          <div class="benefit-icon-wrapper">
            <svg viewBox="0 0 24 24"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"/><line x1="9" y1="3" x2="9" y2="18"/><line x1="15" y1="6" x2="15" y2="21"/></svg>
          </div>
          <div class="benefit-content">
            <h4 class="benefit-title">Интерактивный поиск на карте</h4>
            <p class="benefit-desc">Выбирайте жилье по точному местоположению — рядом с метро, в историческом центре или за городом.</p>
          </div>
        </div>

        <!-- Benefit 3 -->
        <div class="benefit-card">
          <div class="benefit-icon-wrapper">
            <svg viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
          </div>
          <div class="benefit-content">
            <h4 class="benefit-title">Актуальные цены и фото</h4>
            <p class="benefit-desc">Мы следим за достоверностью данных и соответствием правилам. Вы видите реальные фотографии интерьеров и точные тарифы.</p>
          </div>
        </div>

        <!-- Benefit 4 -->
        <div class="benefit-card">
          <div class="benefit-icon-wrapper">
            <svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          </div>
          <div class="benefit-content">
            <h4 class="benefit-title">Экономия до 20%</h4>
            <p class="benefit-desc">Аренда без посредников позволяет хозяевам не платить коммерческий сбор сервисам бронирования, что делает цену ниже для гостей.</p>
          </div>
        </div>
      </div>

      <!-- Section: Rates Table -->
      <h3 class="seo-subtitle">
        <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
        {{ $seoRatesTitle }}
      </h3>
      
      <div class="seo-table-wrapper">
        <table class="seo-table">
          <thead>
            <tr>
              <th>{{ $seoBlockType === 'cottages' ? 'Тип дома / усадьбы' : 'Тип жилья' }}</th>
              <th>{{ $seoBlockType === 'hourly' ? 'Средняя цена за час' : 'Средняя цена за сутки' }}</th>
              <th>Кому подойдет</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($seoRates as $rate)
              <tr>
                <td class="seo-type-col">{{ $rate['type'] }}</td>
                <td><span class="seo-price-badge">{{ $rate['price'] }}</span></td>
                <td><span class="seo-suit-pill">{{ $rate['suit'] }}</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @php
        $selectedCityName = isset($selectedCity) ? trim($selectedCity) : '';
        $seoGeoNote = '';
        if ($selectedCityName === 'Минск') {
            $seoGeoNote = 'Студия в центре Минска обойдётся дороже, чем двухкомнатная в спальном районе Уручье. Стоимость посуточной аренды напрямую зависит от близости к метро и уровня комфорта. Выбирайте оптимальный вариант жилья под свой бюджет и планируемый маршрут поездки.';
        } elseif ($selectedCityName === 'Брест') {
            $seoGeoNote = 'Студия в центре Бреста обойдётся дороже, чем двухкомнатная в спальном районе Ковалёво. Стоимость посуточной аренды напрямую зависит от близости к главным достопримечательностям и уровня комфорта. Выбирайте оптимальный вариант жилья под свой бюджет и планируемый маршрут поездки.';
        } elseif ($selectedCityName === 'Гродно') {
            $seoGeoNote = 'Студия в центре Гродно обойдётся дороже, чем двухкомнатная в спальном районе Девятовка. Стоимость посуточной аренды напрямую зависит от близости к исторической части города и уровня комфорта. Выбирайте оптимальный вариант жилья под свой бюджет и планируемый маршрут поездки.';
        } elseif ($selectedCityName === 'Гомель') {
            $seoGeoNote = 'Студия в центре Гомеля обойдётся дороже, чем двухкомнатная в спальном районе Сельмаш. Стоимость посуточной аренды напрямую зависит от близости к парковой зоне и уровня комфорта. Выбирайте оптимальный вариант жилья под свой бюджет и планируемый маршрут поездки.';
        } elseif ($selectedCityName === 'Витебск') {
            $seoGeoNote = 'Студия в центре Витебска обойдётся дороже, чем двухкомнатная в спальном районе Билево. Стоимость посуточной аренды напрямую зависит от близости к местам проведения фестивалей и уровня комфорта. Выбирайте оптимальный вариант жилья под свой бюджет и планируемый маршрут поездки.';
        } elseif ($selectedCityName === 'Могилев' || $selectedCityName === 'Могилёв') {
            $seoGeoNote = 'Студия в центре Могилева обойдётся дороже, чем двухкомнатная в спальном районе Заднепровье. Стоимость посуточной аренды напрямую зависит от близости к основным культурным объектам и уровня комфорта. Выбирайте оптимальный вариант жилья под свой бюджет и планируемый маршрут поездки.';
        } else {
            $seoGeoNote = 'Снять квартиру или студию ' . $inCitySeo . ' в центральных районах обойдётся дороже, чем двухкомнатную квартиру на окраине. Итоговая стоимость посуточной аренды зависит от расположения, уровня ремонта и доступных удобств. Выбирайте подходящий вариант под свой бюджет и планируемый маршрут.';
        }
      @endphp
      <p class="seo-table-note" style="font-size: 14px; line-height: 1.6; color: var(--gray-text); margin-top: -24px; margin-bottom: 32px; font-style: italic;">
        {{ $seoGeoNote }}
      </p>

      <!-- Section: SEO Linking Cloud -->
      @if(isset($selectedCity))
        @php
          $citySlugSeo = \App\Helpers\CityHelper::slugify($selectedCity);
          $inCityNameSeo = \App\Helpers\CityHelper::getInCityName($selectedCity);

          $cloudFilters = [
              'dzhakuzi' => [
                  'url' => "/{$citySlugSeo}/dzhakuzi",
                  'text' => 'Квартиры с джакузи',
                  'query' => function($q) {
                      $q->where('type', '!=', 'cottage')->where(function($sub) {
                          $sub->where('amenities', 'like', '%джакузи%')
                              ->orWhere('amenities', 'like', '%Гидромассаж%')
                              ->orWhere('title', 'like', '%джакузи%')
                              ->orWhere('description', 'like', '%джакузи%');
                      });
                  }
              ],
              'odnokomnatnye' => [
                  'url' => "/{$citySlugSeo}/odnokomnatnye",
                  'text' => '1-комнатные квартиры',
                  'query' => function($q) {
                      $q->where('rooms', 1)->where('type', '!=', 'cottage');
                  }
              ],
              'dvuhkomnatnye' => [
                  'url' => "/{$citySlugSeo}/dvuhkomnatnye",
                  'text' => '2-комнатные квартиры',
                  'query' => function($q) {
                      $q->where('rooms', 2)->where('type', '!=', 'cottage');
                  }
              ],
              'trehkomnatnye' => [
                  'url' => "/{$citySlugSeo}/trehkomnatnye",
                  'text' => '3-комнатные квартиры',
                  'query' => function($q) {
                      $q->where('rooms', 3)->where('type', '!=', 'cottage');
                  }
              ],
              'chetyrehkomnatnye' => [
                  'url' => "/{$citySlugSeo}/chetyrehkomnatnye",
                  'text' => '4-комнатные квартиры',
                  'query' => function($q) {
                      $q->where('rooms', '>=', 4)->where('type', '!=', 'cottage');
                  }
              ],
              'v-centre' => [
                  'url' => "/{$citySlugSeo}/v-centre",
                  'text' => 'Квартиры в центре',
                  'query' => function($q) {
                      $q->where('type', '!=', 'cottage')->where(function($sub) {
                          $sub->where('address', 'like', '%центр%')
                              ->orWhere('description', 'like', '%центр%')
                              ->orWhere('title', 'like', '%центр%');
                      });
                  }
              ],
              'metro' => [
                  'url' => "/{$citySlugSeo}/metro",
                  'text' => 'Квартиры возле метро',
                  'query' => function($q) {
                      $q->where('type', '!=', 'cottage')->where(function($sub) {
                          $sub->whereNotNull('nearest_metro')
                              ->where('nearest_metro', '!=', '')
                              ->where('nearest_metro', '!=', 'Нет')
                              ->where('nearest_metro', '!=', 'Метро не указано')
                              ->orWhere('address', 'like', '%метро%')
                              ->orWhere('description', 'like', '%метро%')
                              ->orWhere('title', 'like', '%метро%');
                      });
                  },
                  'only_minsk' => true
              ],
              'na-chasy' => [
                  'url' => "/{$citySlugSeo}/na-chasy",
                  'text' => 'Квартиры на часы',
                  'query' => function($q) {
                      $q->where('type', '!=', 'cottage')->where('weekend_price', '>', 0);
                  }
              ],
              'odnokomnatnye/na-chasy' => [
                  'url' => "/{$citySlugSeo}/odnokomnatnye/na-chasy",
                  'text' => '1-комнатные на часы',
                  'query' => function($q) {
                      $q->where('type', '!=', 'cottage')->where('rooms', 1)->where('weekend_price', '>', 0);
                  }
              ],
              'dvuhkomnatnye/na-chasy' => [
                  'url' => "/{$citySlugSeo}/dvuhkomnatnye/na-chasy",
                  'text' => '2-комнатные на часы',
                  'query' => function($q) {
                      $q->where('type', '!=', 'cottage')->where('rooms', 2)->where('weekend_price', '>', 0);
                  }
              ],
              'kottedzhi' => [
                  'url' => "/{$citySlugSeo}/kottedzhi",
                  'text' => 'Коттеджи и дома',
                  'query' => function($q) {
                      $q->whereIn('type', ['cottage', 'agrotourism']);
                  }
              ],
              's-baney' => [
                  'url' => "/{$citySlugSeo}/s-baney",
                  'text' => 'Коттеджи с баней',
                  'query' => function($q) {
                      $q->whereIn('type', ['cottage', 'agrotourism'])->where(function($sub) {
                          $sub->where('amenities', 'like', '%баня%')
                              ->orWhere('amenities', 'like', '%сауна%')
                              ->orWhere('territory_features', 'like', '%баня%')
                              ->orWhere('territory_features', 'like', '%сауна%')
                              ->orWhere('title', 'like', '%баня%')
                              ->orWhere('title', 'like', '%сауна%')
                              ->orWhere('description', 'like', '%баня%')
                              ->orWhere('description', 'like', '%сауна%');
                      });
                  }
              ],
              's-basseynom' => [
                  'url' => "/{$citySlugSeo}/s-basseynom",
                  'text' => 'Коттеджи с бассейном',
                  'query' => function($q) {
                      $q->whereIn('type', ['cottage', 'agrotourism'])->where(function($sub) {
                          $sub->where('amenities', 'like', '%бассейн%')
                              ->orWhere('amenities', 'like', '%бассеин%')
                              ->orWhere('amenities', 'like', '%купель%')
                              ->orWhere('territory_features', 'like', '%бассейн%')
                              ->orWhere('territory_features', 'like', '%бассеин%')
                              ->orWhere('territory_features', 'like', '%купель%')
                              ->orWhere('title', 'like', '%бассейн%')
                              ->orWhere('title', 'like', '%бассеин%')
                              ->orWhere('title', 'like', '%купель%')
                              ->orWhere('description', 'like', '%бассейн%')
                              ->orWhere('description', 'like', '%бассеин%')
                              ->orWhere('description', 'like', '%купель%');
                      });
                  }
              ],
          ];

          $visibleTags = [];
          foreach ($cloudFilters as $filterData) {
              if (isset($filterData['only_minsk']) && $filterData['only_minsk'] && mb_strtolower($selectedCity) !== 'минск') {
                  continue;
              }

              $exists = \App\Models\Listing::where('status', 'active')
                  ->where('city', $selectedCity)
                  ->where(function($query) use ($filterData) {
                      $filterData['query']($query);
                  })
                  ->exists();

              if ($exists) {
                  $visibleTags[] = $filterData;
              }
          }
        @endphp

        @if(count($visibleTags) > 0)
          <h3 class="seo-subtitle">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
            Быстрый выбор жилья {{ $inCityNameSeo }}
          </h3>
          
          <div class="seo-tag-cloud">
            @foreach($visibleTags as $tag)
              <a href="{{ $tag['url'] }}" class="seo-tag-link">{{ $tag['text'] }}</a>
            @endforeach
          </div>
        @endif

        @if(isset($selectedCity) && mb_strtolower($selectedCity) === 'минск')
          @php
            $minskDistricts = [
                'uruchye' => 'Уручье',
                'kamennaya-gorka' => 'Каменная Горка',
                'nemiga' => 'Немига',
                'suharevo' => 'Сухарево',
                'zelenyy-lug' => 'Зелёный Луг',
                'malinovka' => 'Малиновка',
                'serebryanka' => 'Серебрянка',
                'yugo-zapad' => 'Юго-Запад',
                'vesnyanka' => 'Веснянка',
                'kuntsevschina' => 'Кунцевщина',
                'minsk-mir' => 'Минск Мир'
            ];
          @endphp
          <h3 class="seo-subtitle" style="margin-top: 24px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21"></polygon><line x1="9" y1="3" x2="9" y2="18"></line><line x1="15" y1="6" x2="15" y2="21"></line></svg>
            Квартиры посуточно по районам Минска
          </h3>
          <div class="seo-tag-cloud" style="margin-bottom: 24px;">
            @foreach($minskDistricts as $dSlug => $dName)
              <a href="/minsk/{{ $dSlug }}" class="seo-tag-link">{{ $dName }}</a>
            @endforeach
          </div>
        @endif
      @else
        @php
          // Динамический заголовок и ссылки для облака тегов городов в зависимости от категории
          if ($seoBlockType === 'cottages') {
              $cityCloudTitle = "Аренда коттеджей и усадеб в городах Беларуси";
              $cityLinks = [
                  ['url' => '/minsk/kottedzhi', 'text' => 'Снять коттедж в Минске'],
                  ['url' => '/brest/kottedzhi', 'text' => 'Снять коттедж в Бресте'],
                  ['url' => '/grodno/kottedzhi', 'text' => 'Снять коттедж в Гродно'],
                  ['url' => '/gomel/kottedzhi', 'text' => 'Снять коттедж в Гомеле'],
                  ['url' => '/vitebsk/kottedzhi', 'text' => 'Снять коттедж в Витебске'],
                  ['url' => '/mogilev/kottedzhi', 'text' => 'Снять коттедж в Могилеве'],
                  ['url' => '/bobruysk/kottedzhi', 'text' => 'Снять коттедж в Бобруйске'],
                  ['url' => '/baranovichi/kottedzhi', 'text' => 'Снять коттедж в Барановичах']
              ];
          } elseif ($seoBlockType === 'hourly') {
              $cityCloudTitle = "Аренда квартир на часы в городах Беларуси";
              $cityLinks = [
                  ['url' => '/minsk/na-chasy', 'text' => 'Квартиры на часы в Минске'],
                  ['url' => '/brest/na-chasy', 'text' => 'Квартиры на часы в Бресте'],
                  ['url' => '/grodno/na-chasy', 'text' => 'Квартиры на часы в Гродно'],
                  ['url' => '/gomel/na-chasy', 'text' => 'Квартиры на часы в Гомеле'],
                  ['url' => '/vitebsk/na-chasy', 'text' => 'Квартиры на часы в Витебске'],
                  ['url' => '/mogilev/na-chasy', 'text' => 'Квартиры на часы в Могилеве'],
                  ['url' => '/bobruysk/na-chasy', 'text' => 'Квартиры на часы в Бобруйске'],
                  ['url' => '/baranovichi/na-chasy', 'text' => 'Квартиры на часы в Барановичах']
              ];
          } elseif ($seoBlockType === 'apartments') {
              $cityCloudTitle = "Аренда квартир на сутки в городах Беларуси";
              $cityLinks = [
                  ['url' => '/minsk/kvartiry', 'text' => 'Снять квартиру на сутки в Минске'],
                  ['url' => '/brest/kvartiry', 'text' => 'Снять квартиру на сутки в Бресте'],
                  ['url' => '/grodno/kvartiry', 'text' => 'Снять квартиру на сутки в Гродно'],
                  ['url' => '/gomel/kvartiry', 'text' => 'Снять квартиру на сутки в Гомеле'],
                  ['url' => '/vitebsk/kvartiry', 'text' => 'Снять квартиру на сутки в Витебске'],
                  ['url' => '/mogilev/kvartiry', 'text' => 'Снять квартиру на сутки в Могилеве'],
                  ['url' => '/bobruysk/kvartiry', 'text' => 'Снять квартиру на сутки в Бобруйске'],
                  ['url' => '/baranovichi/kvartiry', 'text' => 'Снять квартиру на сутки в Барановичах']
              ];
          } else {
              $cityCloudTitle = "Аренда жилья посуточно в городах Беларуси";
              $cityLinks = [
                  ['url' => '/minsk', 'text' => 'Жилье посуточно в Минске'],
                  ['url' => '/brest', 'text' => 'Жилье посуточно в Бресте'],
                  ['url' => '/grodno', 'text' => 'Жилье посуточно в Гродно'],
                  ['url' => '/gomel', 'text' => 'Жилье посуточно в Гомеле'],
                  ['url' => '/vitebsk', 'text' => 'Жилье посуточно в Витебске'],
                  ['url' => '/mogilev', 'text' => 'Жилье посуточно в Могилеве'],
                  ['url' => '/bobruysk', 'text' => 'Жилье посуточно в Бобруйске'],
                  ['url' => '/baranovichi', 'text' => 'Жилье посуточно в Барановичах']
              ];
          }
        @endphp
        <h3 class="seo-subtitle">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
          {{ $cityCloudTitle }}
        </h3>
        
        <div class="seo-tag-cloud">
          @foreach ($cityLinks as $link)
            <a href="{{ $link['url'] }}" class="seo-tag-link">{{ $link['text'] }}</a>
          @endforeach
        </div>
      @endif

      <!-- Section: How it Works Timeline -->
      <h3 class="seo-subtitle">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Как это работает: 3 шага до заселения
      </h3>
      
      <div class="steps-timeline">
        <!-- Step 1 -->
        <div class="step-item">
          <div class="step-badge">1</div>
          <h4 class="step-title">Настройте фильтры</h4>
          <p class="step-desc">Укажите нужный город, цену, количество гостей и удобства (джакузи, детская кроватка).</p>
        </div>

        <!-- Step 2 -->
        <div class="step-item">
          <div class="step-badge">2</div>
          <h4 class="step-title">Выберите лучший вариант на карте</h4>
          <p class="step-desc">Изучите фотографии, подробности и правила проживания.</p>
        </div>

        <!-- Step 3 -->
        <div class="step-item">
          <div class="step-badge">3</div>
          <h4 class="step-title">Свяжитесь напрямую</h4>
          <p class="step-desc">Позвоните владельцу или отправьте сообщение в мессенджеры для мгновенного подтверждения.</p>
        </div>
      </div>



      <!-- Recommendation Box -->
      <div class="seo-recommendation-box">
        <div class="seo-rec-icon">💡</div>
        <p class="seo-rec-text">
          <strong>Рекомендация гостям:</strong> Для надежности сделки всегда подписывайте письменный договор найма при заселении и требуйте платежный документ (чек). Для владельцев жилья на Flatly.by реализована верификация по УНП, что делает сдачу прозрачной и безопасной.
        </p>
      </div>
      @endif
    </div>
  </div><!-- /listings -->

  <!-- MAP -->
  <div class="map-col">
    <div class="map-inner-wrapper">
      <div id="map" style="width: 100%; height: 100%;"></div>

      <!-- Floating Search Area Button -->
      <div id="map-search-area-box" class="map-search-area-box" style="display: none;">
        <button id="map-search-area-btn" class="map-search-area-btn" onclick="triggerAreaSearch(event)">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <span>Искать в этой области</span>
        </button>
        <button id="map-reset-area-btn" class="map-reset-area-btn" onclick="resetAreaSearch(event)" title="Сбросить ограничение по карте" style="display: none;">
          ✕
        </button>
      </div>

      <!-- Top-Right Controls Toolbar -->
      <div class="map-controls-toolbar">
        <button id="map-draw-btn" class="map-draw-btn" onclick="toggleDrawMode(event)" title="Обвести область на карте зажав мышь или палец">
          <span>✏️</span>
          <span class="btn-lbl">Обвести область</span>
        </button>

        <button id="map-poi-toggle-btn" class="map-poi-toggle-btn active" onclick="togglePoiLayer(event)" title="Показать/скрыть ориентиры и метро">
          <span>📍</span>
          <span class="btn-lbl">Ориентиры</span>
        </button>
      </div>

      <!-- Drawing Hint Overlay -->
      <div id="map-draw-hint" class="map-draw-hint" style="display: none;">
        <span>✏️ Зажмите мышь (или палец) и обведите область на карте</span>
        <button onclick="cancelDrawMode(event)">Отмена</button>
      </div>

      <!-- Active Polygon Bar -->
      <div id="map-polygon-bar" class="map-polygon-bar" style="display: none;">
        <span>✏️ Выделенная область</span>
        <button class="reset-poly-btn" onclick="clearDrawnPolygon(event)">✕ Сбросить контур</button>
      </div>

      <!-- Geolocation Button -->
      <button class="map-geo-btn" onclick="triggerMapGeolocation(event)" title="Определить мое местоположение" aria-label="Определить мое местоположение">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="22" y1="12" x2="18" y2="12"></line>
          <line x1="6" y1="12" x2="2" y2="12"></line>
          <line x1="12" y1="6" x2="12" y2="2"></line>
          <line x1="12" y1="22" x2="12" y2="18"></line>
        </svg>
      </button>

      <!-- Floating Map Card Preview -->
      <div id="map-card-preview" class="map-card-preview" onclick="openPreviewDetails()">
        <button class="map-card-close" onclick="closeMapPreview(event)" aria-label="Закрыть превью">×</button>
        <div class="map-card-img-wrap">
          <img id="map-card-img" src="" alt="Превью жилья на карте" loading="lazy" style="display: none;">
          <div id="map-card-fake-img" class="map-card-fake-img" style="display: none;">🏢</div>
        </div>
        <div class="map-card-info">
          <div class="map-card-top-row">
            <a id="map-card-title" class="map-card-title" href="#" target="_self" style="text-decoration: none; color: inherit;"></a>
          </div>
          <div id="map-card-address" class="map-card-address"></div>
          
          <!-- POI & Walking Time Box -->
          <div id="map-card-poi-box" class="map-card-poi-box" style="display: none;">
            <div id="map-card-metro-row" class="card-poi-row">
              <span id="map-card-metro-time" class="poi-text">Метро не указано</span>
            </div>
            <div id="map-card-landmark-row" class="card-poi-row" style="display: none;">
              <span id="map-card-landmark-icon" class="poi-icon">🛍️</span>
              <span id="map-card-landmark-time" class="poi-text"></span>
            </div>
          </div>

          <div class="card-price-box map-price-box">
            <div class="price-section left">
              <div class="price-label">
                <span class="price-prefix">от</span>
                <span id="map-card-price-day" class="price-amount price-val"></span>
              </div>
              <div class="price-period">за сутки</div>
            </div>
            <div class="price-divider"></div>
            <div class="price-section right">
              <div class="price-label">
                <span id="map-card-price-hour" class="price-amount price-val"></span>
              </div>
              <div class="price-period">за часы</div>
            </div>
          </div>

          <div class="card-badges-row map-badges-row">
            <span id="map-card-badge-area" class="badge-pill"></span>
            <span id="map-card-badge-rooms" class="badge-pill"></span>
            <span id="map-card-badge-guests" class="badge-pill"></span>
          </div>

          <div class="contact-block map-contact-block" onclick="event.stopPropagation()">
            <button class="primary-action-btn" id="map-phone-btn" onclick="toggleMapCardContact(event)">
              Показать контакты
            </button>
            <div class="card-messenger-row map-messenger-row" id="map-messengers" style="display: none;">
              <span id="map-card-revealed-phone" class="revealed-phone"></span>
              <div class="messenger-icons-small">
                <a id="map-wa-link" href="#" class="wa-s" target="_blank" onclick="event.stopPropagation()" aria-label="Связаться через WhatsApp"><svg viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg></a>
                <a id="map-vb-link" href="#" class="vb-s" target="_blank" onclick="event.stopPropagation()" aria-label="Связаться через Viber"><svg viewBox="0 0 24 24"><path d="M11.398.002C9.473.028 5.331.344 3.014 2.467 1.294 4.177.693 6.698.623 9.82c-.06 3.11-.13 8.95 5.5 10.541v2.42s-.038.97.602 1.17c.79.25 1.24-.499 1.99-1.299l1.4-1.58c3.85.32 6.8-.419 7.14-.529.78-.25 5.181-.811 5.901-6.652.74-6.031-.36-9.831-2.34-11.551l-.01-.002c-.6-.55-3-2.3-8.37-2.32 0 0-.396-.025-1.038-.016zm.067 1.697c.545-.003.88.02.88.02 4.54.01 6.711 1.38 7.221 1.84 1.67 1.429 2.528 4.856 1.9 9.892-.6 4.88-4.17 5.19-4.83 5.4-.28.09-2.88.73-6.152.52 0 0-2.439 2.941-3.199 3.701-.12.13-.26.17-.35.15-.13-.03-.17-.19-.16-.41l.02-4.019c-4.771-1.32-4.491-6.302-4.441-8.902.06-2.6.55-4.732 2-6.172 1.957-1.77 5.475-2.01 7.11-2.02zm.36 2.6a.299.299 0 0 0-.3.299.3.3 0 0 0 .3.3 5.631 5.631 0 0 1 4.03 1.59c1.09 1.06 1.621 2.48 1.641 4.34a.3.3 0 0 0 .3.3v-.009a.3.3 0 0 0 .3-.3 6.451 6.451 0 0 0-1.81-4.76c-1.19-1.16-2.692-1.76-4.462-1.76zm-3.954.69a.955.955 0 0 0-.615.12h-.012c-.41.24-.788.54-1.148.94-.27.32-.421.639-.461.949a1.24 1.24 0 0 0 .05.541l.02.01a13.722 13.722 0 0 0 1.2 2.6 15.383 15.383 0 0 0 2.32 3.171l.03.04.04.03.03.03.03.03a15.603 15.603 0 0 0 3.18 2.33c1.32.72 2.122 1.06 2.602 1.2v.01c.14.04.268.06.398.06a1.84 1.84 0 0 0 1.102-.472c.39-.35.7-.738.93-1.148v-.01c.23-.43.15-.841-.18-1.121a13.632 13.632 0 0 0-2.15-1.54c-.51-.28-1.03-.11-1.24.17l-.45.569c-.23.28-.65.24-.65.24l-.012.01c-3.12-.8-3.95-3.959-3.95-3.959s-.04-.43.25-.65l.56-.45c.27-.22.46-.74.17-1.25a13.522 13.522 0 0 0-1.54-2.15.843.843 0 0 0-.504-.3zm4.473.89a.3.3 0 0 0 .002.6 3.78 3.78 0 0 1 2.65 1.15 3.5 3.5 0 0 1 .9 2.57.3.3 0 0 0 .3.299l.01.012a.3.3 0 0 0 .3-.301c.03-1.19-.34-2.19-1.07-2.99-.73-.8-1.75-1.25-3.05-1.34a.3.3 0 0 0-.042 0zm.49 1.619a.305.305 0 0 0-.018.611c.99.05 1.47.55 1.53 1.58a.3.3 0 0 0 .3.29h.01a.3.3 0 0 0 .29-.32c-.07-1.34-.8-2.091-2.1-2.161a.305.305 0 0 0-.012 0z"/></svg></a>
                <a id="map-tg-link" href="#" class="tg-s" target="_blank" onclick="event.stopPropagation()" aria-label="Связаться через Telegram"><svg viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12l-6.87 4.326-2.96-.924c-.643-.204-.658-.643.136-.953l11.57-4.46c.537-.204.997.12.825.861z"/></svg></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div><!-- /main -->

<!-- Floating Map/List Toggle Button for Mobile -->
<button class="mobile-map-toggle-btn" id="mobile-map-toggle" onclick="toggleMobileMap()">
  <span class="btn-text">Карта</span>
  <svg class="map-icon" viewBox="0 0 24 24" width="16" height="16"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" fill="var(--white)"/><circle cx="12" cy="9" r="2.5" fill="var(--slate-900)"/></svg>
  <svg class="list-icon" viewBox="0 0 24 24" width="16" height="16" style="display:none;"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
</button>

<!-- CITY SELECTION MODAL -->
<div class="modal-backdrop" id="city-modal" onclick="closeCityModal()" style="z-index: 3100;">
  <div class="filter-modal-card" id="city-modal-card" onclick="event.stopPropagation()" style="max-width: 600px; width: 95%; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden;">
    <div class="modal-grab-bar"></div>
    <div class="filter-modal-header" style="border-bottom: 1px solid var(--border); padding-bottom: 16px;">
      <h3 style="font-size: 18px; font-weight: 700; color: var(--slate-900);">Выбор города</h3>
      <button class="close-modal-btn" onclick="closeCityModal()" aria-label="Закрыть модальное окно выбора города">×</button>
    </div>
    
    <div class="filter-modal-body" style="padding: 20px; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 20px;">
      <!-- Search Input inside modal -->
      <div class="hero-city-search-wrap" style="position: relative;">
        <input type="text" id="city-modal-search-input" placeholder="Поиск города..." aria-label="Поиск города в списке" 
               style="width: 100%; height: 42px; border: 1px solid var(--border); border-radius: 8px; padding: 0 12px 0 36px; font-size: 14px; box-sizing: border-box; background: var(--slate-50);"
               oninput="filterModalCities()">
        <svg viewBox="0 0 24 24" style="position: absolute; left: 12px; top: 13px; width: 16px; height: 16px; fill: none; stroke: var(--gray-text); stroke-width: 2.5;"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/></svg>
      </div>
      
      <!-- Regional Centers -->
      <div class="city-modal-popular-section">
        <span style="font-size: 10.5px; font-weight: 700; color: var(--gray-text); text-transform: uppercase; display: block; margin-bottom: 8px; letter-spacing: 0.5px;">крупные города</span>
        <div class="city-modal-popular-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
          <div class="city-modal-item active" style="grid-column: span 3; padding: 10px 8px; text-align: center; font-size: 13px; font-weight: 700; border: 1px solid var(--border); border-radius: 8px; cursor: pointer; transition: all 0.15s; box-sizing: border-box;" onclick="selectModalCity('Вся Беларусь')">Вся Беларусь</div>
          @php
            $popularCenters = ['Минск', 'Брест', 'Гродно', 'Витебск', 'Гомель', 'Могилев'];
          @endphp
          @foreach($popularCenters as $pCenter)
            <div class="city-modal-item" style="padding: 10px 8px; text-align: center; font-size: 13px; font-weight: 600; border: 1px solid var(--border); border-radius: 8px; cursor: pointer; transition: all 0.15s; box-sizing: border-box;" onclick="selectModalCity('{{ $pCenter }}')">{{ $pCenter }}</div>
          @endforeach
        </div>
      </div>

      <!-- Regions (Oblasts) -->
      <div class="city-modal-regions-section" style="border-top: 1px solid var(--border); padding-top: 16px;">
        <span style="font-size: 10.5px; font-weight: 700; color: var(--gray-text); text-transform: uppercase; display: block; margin-bottom: 8px; letter-spacing: 0.5px;">Области</span>
        <div class="city-modal-regions-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
          @php
            $regions = ['Брестская область', 'Витебская область', 'Гомельская область', 'Гродненская область', 'Минская область', 'Могилевская область'];
          @endphp
          @foreach($regions as $region)
            <div class="city-modal-item" style="padding: 10px 8px; text-align: center; font-size: 13px; font-weight: 600; border: 1px solid var(--border); border-radius: 8px; cursor: pointer; transition: all 0.15s; box-sizing: border-box;" onclick="selectModalCity('{{ $region }}')">{{ $region }}</div>
          @endforeach
        </div>
      </div>
      
      <!-- All Cities list -->
      <div class="city-modal-all-section" style="border-top: 1px solid var(--border); padding-top: 16px; display: flex; flex-direction: column;">
        <span class="city-modal-all-section-title" style="font-size: 10.5px; font-weight: 700; color: var(--gray-text); text-transform: uppercase; display: block; margin-bottom: 8px; letter-spacing: 0.5px;">Остальные города</span>
        <div id="city-modal-all-list" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; max-height: 240px; overflow-y: auto; padding-right: 4px;">
          @php
            $bigCities = ['Минск', 'Брест', 'Гродно', 'Витебск', 'Гомель', 'Могилев'];
            $bigCitiesNorm = array_map(function($c) { return str_replace('ё', 'е', mb_strtolower($c)); }, $bigCities);
            
            $oblastNames = ['Минская область', 'Брестская область', 'Гродненская область', 'Гомельская область', 'Витебская область', 'Могилевская область', 'Могилёвская область'];
            $oblastNamesNorm = array_map(function($c) { return str_replace('ё', 'е', mb_strtolower($c)); }, $oblastNames);

            $allHeroCities = [
                'Барановичи', 'Белыничи', 'Береза', 'Бобруйск', 'Борисов', 'Брест', 'Витебск', 'Волковыск', 'Глубокое',
                'Гомель', 'Горки', 'Городок', 'Гродно', 'Добруш', 'Докшицы', 'Дрогичин', 'Ельск', 'Жлобин', 'Жодино',
                'Заславль', 'Иваново', 'Калинковичи', 'Клецк', 'Климовичи', 'Кобрин', 'Кричев', 'Лепель', 'Лида', 'Лунинец',
                'Минск', 'Могилев', 'Мозырь', 'Молодечно', 'Мосты', 'Мстиславль', 'Нарочь', 'Несвиж', 'Новогрудок', 'Новолукомль',
                'Новополоцк', 'Орша', 'Осиповичи', 'Островец', 'Ошмяны', 'Пинск', 'Полоцк', 'Поставы', 'Пружаны', 'Речица',
                'Рогачев', 'Светлогорск', 'Слоним', 'Смолевичи', 'Сморгонь', 'Солигорск', 'Столин', 'Фаниполь', 'Хойники',
                'Чаусы', 'Шклов', 'Щучин'
            ];
            
            // Add oblasts to the list of searchable cities
            foreach ($oblastNames as $obl) {
                if (!in_array($obl, $allHeroCities)) {
                    $allHeroCities[] = $obl;
                }
            }

            if (isset($activeCities) && is_array($activeCities)) {
                foreach ($activeCities as $dbCity) {
                    if (!empty($dbCity) && !in_array($dbCity, $allHeroCities)) {
                        $allHeroCities[] = $dbCity;
                    }
                }
            }
            sort($allHeroCities, SORT_STRING);
          @endphp
          @foreach($allHeroCities as $hCity)
            @php
              $norm = str_replace('ё', 'е', mb_strtolower(trim($hCity)));
              $isSpecial = in_array($norm, $bigCitiesNorm) || in_array($norm, $oblastNamesNorm);
            @endphp
            <div class="city-modal-item city-modal-all-item {{ $isSpecial ? 'city-special-item' : '' }}" style="padding: 8px 12px; font-size: 13px; font-weight: 500; border-radius: 6px; cursor: pointer; transition: all 0.15s; box-sizing: border-box; display: {{ $isSpecial ? 'none' : 'block' }};" onclick="selectModalCity('{{ $hCity }}')">
              {{ $hCity }}
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

<!-- TARIFFS MODAL -->
<div class="modal-backdrop" id="tariffs-modal" onclick="closeTariffsModal()" style="z-index: 3100;">
  <div class="filter-modal-card" id="tariffs-modal-card" onclick="event.stopPropagation()" style="max-width: 800px; width: 90%; max-height: 90vh; display: flex; flex-direction: column;">
    <div class="modal-grab-bar"></div>
    <div class="filter-modal-header" style="border-bottom: 1px solid var(--border); padding-bottom: 16px;">
      <h3 style="font-size: 18px; font-weight: 700; color: var(--slate-900);">Тарифы размещения объявлений</h3>
      <button class="close-modal-btn" onclick="closeTariffsModal()" aria-label="Закрыть модальное окно тарифов">×</button>
    </div>
    
    <div class="filter-modal-body" style="padding-top: 20px; overflow-y: auto; flex: 1;">
      <p style="font-size: 14px; color: var(--gray-text); line-height: 1.5; margin-bottom: 24px; text-align: center;">
        Flatly.by — это прямые сделки без посредников и комиссий. Оплачивайте только размещение вашего объявления по фиксированным тарифам.
      </p>

      <div class="tariffs-grid">
        <!-- Квартира -->
        <div class="tariff-card">
          <div class="tariff-icon">🏢</div>
          <div class="tariff-name">Тариф «Квартира»</div>
          <div class="tariff-price">10.00 <span>Б̶</span></div>
          <div class="tariff-duration">на 30 дней размещения</div>
          <ul class="tariff-features">
            <li>Сдача квартир посуточно или на часы</li>
            <li>Прямые контакты (телефон, мессенджеры)</li>
            <li>Отображение на интерактивной карте</li>
            <li>Статистика просмотров объявления</li>
            <li class="not-included">Закрепление в топе выдачи</li>
          </ul>
          <button class="tariff-card-btn" onclick="goToCreateListing()">Разместить</button>
        </div>

        <!-- Коттедж -->
        <div class="tariff-card">
          <div class="tariff-icon">🏡</div>
          <div class="tariff-name">Тариф «Загородный»</div>
          <div class="tariff-price">30.00 <span>Б̶</span></div>
          <div class="tariff-duration">на 30 дней размещения</div>
          <ul class="tariff-features">
            <li>Сдача коттеджей, усадеб и дач посуточно</li>
            <li>Прямые контакты (телефон, мессенджеры)</li>
            <li>Детальное описание развлечений</li>
            <li>Статистика просмотров объявления</li>
            <li class="not-included">Закрепление в топе выдачи</li>
          </ul>
          <button class="tariff-card-btn" onclick="goToCreateListing()">Разместить</button>
        </div>

        <!-- Премиум-поднятие -->
        <div class="tariff-card featured">
          <div class="tariff-badge">Популярно</div>
          <div class="tariff-icon">👑</div>
          <div class="tariff-name">«Премиум-поднятие»</div>
          <div class="tariff-price">15.00 <span>Б̶</span></div>
          <div class="tariff-duration">на 7 дней продвижения</div>
          <ul class="tariff-features">
            <li>Поднятие объявления в самый верх поиска</li>
            <li>Золотое выделение карточки на карте/в списке</li>
            <li>В 5 раз больше просмотров и звонков</li>
            <li>Приоритетная индексация в поисковиках</li>
            <li>Для любых типов недвижимости</li>
          </ul>
          <button class="tariff-card-btn" onclick="goToCreateListing()">Продвинуть</button>
        </div>
      </div>
    </div>
    
    <div class="filter-modal-footer" style="margin-top: 12px; border-top: 1px solid var(--border); padding-top: 16px; display: flex; justify-content: flex-end; gap: 12px;">
      <span style="font-size: 12.5px; color: var(--gray-text); align-self: center; margin-right: auto;">
        * Оплата производится через защищенный шлюз bePaid.
      </span>
      <button class="apply-filters-btn" style="max-width: 140px;" onclick="closeTariffsModal()">Закрыть</button>
    </div>
  </div>
</div>

<!-- ADVANCED FILTERS MODAL -->
<div class="modal-backdrop" id="filter-modal" onclick="closeFilterModal()">
  <div class="filter-modal-card" id="filter-modal-card" data-active-type="apartments" onclick="event.stopPropagation()">
    <div class="modal-grab-bar"></div>
    <div class="filter-modal-header">
      <h3>Фильтры</h3>
      <button class="close-modal-btn" onclick="closeFilterModal()" aria-label="Закрыть модальное окно фильтров">×</button>
    </div>
    
    <div class="filter-modal-body">
      <!-- Выбор объекта -->
      <div class="filter-section">
        <h4>Выбор объекта</h4>
        <div class="object-type-tabs">
          <button class="ot-tab-btn active" data-type="apartments" onclick="selectObjectType('apartments', event)">Квартиры</button>

          <button class="ot-tab-btn" data-type="cottages" onclick="selectObjectType('cottages', event)">Коттеджи</button>

        </div>
      </div>
      
      <!-- Диапазон цен -->
      <div class="filter-section">
        <h4>Диапазон цен (за сутки)</h4>
        <div class="price-inputs">
          <div class="price-input-wrap">
            <span>от</span>
            <input type="number" id="price-min" placeholder="0" min="0" aria-label="Минимальная цена">
            <span class="currency-symbol-label">Б̶</span>
          </div>
          <div class="price-input-wrap">
            <span>до</span>
            <input type="number" id="price-max" placeholder="300" min="0" aria-label="Максимальная цена">
            <span class="currency-symbol-label">Б̶</span>
          </div>
        </div>
        <!-- Слайдер цен -->
        <div class="price-slider-wrapper">
          <div class="range-slider">
            <div class="slider-track" id="price-slider-track"></div>
            <input type="range" id="price-range-min" min="0" max="500" value="0" aria-label="Минимальный порог цены">
            <input type="range" id="price-range-max" min="0" max="500" value="500" aria-label="Максимальный порог цены">
          </div>
        </div>
      </div>

      <!-- Количество гостей -->
      <div class="filter-section">
        <h4>Количество гостей</h4>
        <div class="guests-selector">
          <button onclick="changeGuestsFilter(-1)" aria-label="Уменьшить количество гостей">-</button>
          <span id="guests-filter-val">Любое</span>
          <button onclick="changeGuestsFilter(1)" aria-label="Увеличить количество гостей">+</button>
        </div>
      </div>

      <!-- Комнаты и кровати -->
      <div class="filter-section">
        <h4>Комнаты и кровати</h4>
        
        <h5 style="font-size:13px; font-weight:600; color:var(--slate-900); margin: 12px 0 8px;">Спальни</h5>
        <div class="rooms-beds-row" id="row-bedrooms">
          <button class="rb-btn active" onclick="setRoomFilter('bedrooms', 0, this)">Любое</button>
          <button class="rb-btn" onclick="setRoomFilter('bedrooms', 1, this)">1</button>
          <button class="rb-btn" onclick="setRoomFilter('bedrooms', 2, this)">2</button>
          <button class="rb-btn" onclick="setRoomFilter('bedrooms', 3, this)">3</button>
          <button class="rb-btn" onclick="setRoomFilter('bedrooms', 4, this)">4+</button>
        </div>

        <h5 style="font-size:13px; font-weight:600; color:var(--slate-900); margin: 16px 0 8px;">Кровати</h5>
        <div class="rooms-beds-row" id="row-beds">
          <button class="rb-btn active" onclick="setRoomFilter('beds', 0, this)">Любое</button>
          <button class="rb-btn" onclick="setRoomFilter('beds', 1, this)">1</button>
          <button class="rb-btn" onclick="setRoomFilter('beds', 2, this)">2</button>
          <button class="rb-btn" onclick="setRoomFilter('beds', 3, this)">3</button>
          <button class="rb-btn" onclick="setRoomFilter('beds', 4, this)">4+</button>
        </div>

        <h5 style="font-size:13px; font-weight:600; color:var(--slate-900); margin: 16px 0 8px;">Ванные комнаты</h5>
        <div class="rooms-beds-row" id="row-bathrooms">
          <button class="rb-btn active" onclick="setRoomFilter('bathrooms', 0, this)">Любое</button>
          <button class="rb-btn" onclick="setRoomFilter('bathrooms', 1, this)">1</button>
          <button class="rb-btn" onclick="setRoomFilter('bathrooms', 2, this)">2</button>
          <button class="rb-btn" onclick="setRoomFilter('bathrooms', 3, this)">3+</button>
        </div>
      </div>

      <!-- Характеристики квартиры (Apartments/Studio only) -->
      <div class="filter-section apt-only">
        <h4>Параметры квартиры</h4>
        
        <h5 style="font-size:13px; font-weight:600; color:var(--slate-900); margin: 12px 0 8px;">Тип санузла</h5>
        <div class="rooms-beds-row" id="row-bathroom-type" style="margin-bottom: 16px;">
          <button class="rb-btn active" onclick="setBathroomFilter('', this)">Любой</button>
          <button class="rb-btn" onclick="setBathroomFilter('Совмещённый', this)">Совмещённый</button>
          <button class="rb-btn" onclick="setBathroomFilter('Раздельный', this)">Раздельный</button>
          <button class="rb-btn" onclick="setBathroomFilter('2 санузла', this)">2 санузла</button>
        </div>

        <h5 style="font-size:13px; font-weight:600; color:var(--slate-900); margin: 12px 0 8px;">Балкон</h5>
        <div class="rooms-beds-row" id="row-balcony-type" style="margin-bottom: 16px;">
          <button class="rb-btn active" onclick="setBalconyFilter('', this)">Любой</button>
          <button class="rb-btn" onclick="setBalconyFilter('balcony', this)">Балкон</button>
          <button class="rb-btn" onclick="setBalconyFilter('loggia', this)">Лоджия</button>
          <button class="rb-btn" onclick="setBalconyFilter('terrace', this)">Терраса</button>
        </div>

        <h5 style="font-size:13px; font-weight:600; color:var(--slate-900); margin: 12px 0 8px;">Этаж</h5>
        <div class="filter-amenities-grid" style="margin-bottom: 16px;">
          <label class="amenity-checkbox">
            <input type="checkbox" id="apt-not-first">
            <span>Не первый этаж</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" id="apt-not-last">
            <span>Не последний этаж</span>
          </label>
        </div>

        <h5 style="font-size:13px; font-weight:600; color:var(--slate-900); margin: 12px 0 8px;">Удобства дома и территории</h5>
        <div class="filter-amenities-grid">
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="парковка">
            <span>Парковка</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="детская площадка">
            <span>Детская площадка</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="спортивная площадка">
            <span>Спортивная площадка</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="зарядка для электромобилей">
            <span>Зарядка для электромобилей</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="беседка">
            <span>Беседка</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="видеонаблюдение">
            <span>Видеонаблюдение</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="лифт">
            <span>Лифт</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="пандус">
            <span>Пандус</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="консьерж">
            <span>Консьерж</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="колясочная">
            <span>Колясочная</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="подъёмник для инвалидов">
            <span>Подъёмник для инвалидов</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="домофон">
            <span>Домофон</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="велопарковка">
            <span>Велопарковка</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="парковка для инвалидов">
            <span>Парковка для инвалидов</span>
          </label>
        </div>
      </div>

      <!-- Характеристики участка и дома (Cottages/Manors only) -->
      <div class="filter-section cottage-only">
        <h4>Характеристики участка и дома</h4>
        
        <h5 style="font-size:13px; font-weight:600; color:var(--slate-900); margin: 12px 0 8px;">Вместимость банкетного зала</h5>
        <div class="rooms-beds-row" id="row-banquet" style="margin-bottom: 16px;">
          <button class="rb-btn active" onclick="setBanquetFilter(0, this)">Любая</button>
          <button class="rb-btn" onclick="setBanquetFilter(10, this)">До 10 чел.</button>
          <button class="rb-btn" onclick="setBanquetFilter(20, this)">До 20 чел.</button>
          <button class="rb-btn" onclick="setBanquetFilter(50, this)">До 50 чел.</button>
          <button class="rb-btn" onclick="setBanquetFilter(100, this)">50+ чел.</button>
        </div>

        <h5 style="font-size:13px; font-weight:600; color:var(--slate-900); margin: 12px 0 8px;">Тип парковки</h5>
        <div style="margin-bottom: 16px;">
          <select id="cot-parking-type" class="filter-select-item" style="width: 100%; max-width: 300px; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--white); font-size: 13px;">
            <option value="">Любой тип</option>
            <option value="private">Охраняемая на территории</option>
            <option value="free">Бесплатная на территории</option>
            <option value="street">Рядом с домом</option>
          </select>
        </div>

        <h5 style="font-size:13px; font-weight:600; color:var(--slate-900); margin: 16px 0 8px;">Окрестности</h5>
        <div class="filter-amenities-grid" style="margin-bottom: 16px;">
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="лес">
            <span>Лес рядом</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="озеро">
            <span>Озеро</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="река">
            <span>Река</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="водоём">
            <span>Водоём</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="сад / зеленая зона">
            <span>Сад / Зеленая зона</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="горы / холмы">
            <span>Горы / Холмы</span>
          </label>
        </div>

        <h5 style="font-size:13px; font-weight:600; color:var(--slate-900); margin: 16px 0 8px;">Назначение (цель аренды)</h5>
        <div class="filter-amenities-grid" style="margin-bottom: 16px;">
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="семейный отдых">
            <span>Семейный отдых</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="вечеринки / праздники">
            <span>Вечеринки / Праздники</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="корпоративы / тимбилдинги">
            <span>Корпоративы / Тимбилдинги</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="свадьбы / банкеты">
            <span>Свадьбы / Банкеты</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="фотосессии / видеосъемка">
            <span>Фотосессии / Видеосъемка</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="спокойный отдых">
            <span>Спокойный отдых</span>
          </label>
        </div>

        <h5 style="font-size:13px; font-weight:600; color:var(--slate-900); margin: 16px 0 8px;">Видеонаблюдение</h5>
        <div class="filter-amenities-grid">
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="на территории">
            <span>На территории</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="в доме">
            <span>В доме</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="в общих зонах">
            <span>В общих зонах</span>
          </label>
        </div>
      </div>

      <!-- Правила проживания -->
      <div class="filter-section">
        <h4>Правила проживания</h4>
        <div class="filter-amenities-grid">
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="children_allowed">
            <span>Можно с детьми</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="animals_allowed">
            <span>С животными разрешено</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="events_allowed">
            <span>Мероприятия разрешены</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="smoking">
            <span>Разрешено курить</span>
          </label>
        </div>
      </div>

      <!-- Типы спальных мест (кровати) -->
      <div class="filter-section">
        <h4>Тип кровати</h4>
        <div class="filter-amenities-grid">
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-bed-type-item" value="двуспальная кровать">
            <span>Двуспальная кровать</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-bed-type-item" value="односпальная кровать">
            <span>Односпальная кровать</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-bed-type-item" value="диван-кровать">
            <span>Диван-кровать</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-bed-type-item" value="двуспальная широкая (king-size)">
            <span>Широкая кровать (King-size)</span>
          </label>
        </div>
      </div>

      <!-- Удобства (Бытовая техника и комфорт) -->
      <div class="filter-section">
        <h4>Удобства и бытовая техника</h4>
        
        <!-- Для квартир и апартаментов -->
        <div class="filter-amenities-grid apt-only">
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="wi-fi">
            <span>Интернет Wi-Fi</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="кондиционер">
            <span>Кондиционер</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="стиральная машина">
            <span>Стиральная машина</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="телевизор">
            <span>Телевизор</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="гардеробная">
            <span>Гардеробная</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="гладильная доска">
            <span>Гладильная доска</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="детский стул">
            <span>Детский стул</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="сушилка для белья">
            <span>Сушилка для белья</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="утюг">
            <span>Утюг</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="шкаф для одежды">
            <span>Шкаф для одежды</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="шторы блэкаут">
            <span>Шторы блэкаут</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="индукционная плита">
            <span>Индукционная плита</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="газовая плита">
            <span>Газовая плита</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="микроволновка">
            <span>Микроволновка</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="холодильник">
            <span>Холодильник</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="электрочайник">
            <span>Электрочайник</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="посуда и столовые приборы">
            <span>Посуда и приборы</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="посудомоечная машина">
            <span>Посудомоечная машина</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="джакузи">
            <span>Джакузи / Гидромассаж</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="детская кроватка">
            <span>Детская кроватка</span>
          </label>
        </div>

        <!-- Для коттеджей и усадеб -->
        <div class="filter-amenities-grid cottage-only">
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="беседка">
            <span>Беседка</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="мангал / зона барбекю">
            <span>Мангал / Зона барбекю</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="детская площадка">
            <span>Детская площадка</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="русская баня">
            <span>Русская баня</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="финская сауна">
            <span>Финская сауна</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="бассейн">
            <span>Бассейн</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="купель">
            <span>Купель</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="терраса">
            <span>Терраса</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="качели">
            <span>Качели</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="гамак">
            <span>Гамак</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="зона для костра">
            <span>Зона для костра</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="шезлонги">
            <span>Шезлонги</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="интернет wi-fi">
            <span>Интернет Wi-Fi</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="кондиционер">
            <span>Кондиционер</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="камин">
            <span>Камин на дровах</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="отопление">
            <span>Отопление</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="подогрев полов">
            <span>Подогрев полов</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="телевизор / smart tv">
            <span>Телевизор / Smart TV</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="настольный теннис">
            <span>Настольный теннис</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="бильярд">
            <span>Бильярд</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="настольные игры">
            <span>Настольные игры</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="караоке">
            <span>Караоке</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="детские игрушки / книги">
            <span>Детские игрушки / Книги</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="постельное белье">
            <span>Постельное белье</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="полотенца">
            <span>Полотенца</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="средства личной гигиены">
            <span>Средства личной гигиены</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="кухонная посуда">
            <span>Кухонная посуда</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="мангальный набор (шампуры, решетка)">
            <span>Мангальный набор</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="услуги повара / банкетное меню">
            <span>Услуги повара / Меню</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="трансфер">
            <span>Трансфер</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="джакузи">
            <span>Джакузи / Гидромассаж</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="детская кроватка">
            <span>Детская кроватка</span>
          </label>
        </div>
      </div>

      <!-- Развлечения на природе (Cottages/Manors only) -->
      <div class="filter-section cottage-only">
        <h4>Развлечения на природе и активный отдых</h4>
        <div class="filter-amenities-grid">
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="катание на телеге / санях">
            <span>Катание на телеге / Санях</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="прокат квадроциклов">
            <span>Прокат квадроциклов</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="прокат велосипедов">
            <span>Прокат велосипедов</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="рыбалка">
            <span>Рыбалка</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="катание на лодке / катамаране">
            <span>Катание на лодке / катамаране</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="экскурсии / прогулки с гидом">
            <span>Экскурсии / Гид</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="конные прогулки">
            <span>Конные прогулки</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="стрельба из лука / пневматики">
            <span>Стрельба из лука / пневматики</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="лазертаг / пейнтбол">
            <span>Лазертаг / Пейнтбол</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="спортивный инвентарь (мячи, бадминтон)">
            <span>Спортивный инвентарь</span>
          </label>
        </div>
      </div>

      <!-- Правила и условия заселения -->
      <div class="filter-section">
        <h4>Правила и бронирование</h4>
        <div class="filter-amenities-grid">
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="children_allowed">
            <span>Можно с детьми</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="animals_allowed">
            <span>С животными разрешено</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="events_allowed">
            <span>Мероприятия разрешены</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="разрешено курить">
            <span>Разрешено курить</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="бесконтактное заселение">
            <span>Бесконтактное заселение</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="круглосуточное заселение">
            <span>Круглосуточное заселение</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="отчётные документы">
            <span>Отчётные документы</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="предоплата обязательна">
            <span>Предоплата обязательна</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="залог обязателен">
            <span>Залог обязателен</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" class="filter-checkbox-item" data-tag="нужен документ личности">
            <span>Нужен документ личности</span>
          </label>
        </div>
      </div>

      <!-- Способы оплаты -->
      <div class="filter-section">
        <h4>Способы оплаты</h4>
        <div class="filter-amenities-grid" style="margin-bottom: 20px;">
          <label class="amenity-checkbox">
            <input type="checkbox" id="pay-cash">
            <span>Наличными</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" id="pay-card">
            <span>Банковской картой</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" id="pay-erip">
            <span>ЕРИП</span>
          </label>
          <label class="amenity-checkbox">
            <input type="checkbox" id="pay-invoice">
            <span>Безналичный расчет (юр. лица)</span>
          </label>
        </div>
      </div>
    </div>

    <div class="filter-modal-footer">
      <button class="reset-filter-btn" onclick="resetAdvancedFilters()">Сбросить всё</button>
      <button class="apply-filter-btn" onclick="applyAdvancedFiltersAndClose()">Показать варианты</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
// FAQ Accordion Toggle
function toggleFaq(btn) {
  const item = btn.closest('.faq-item');
  const panel = item.querySelector('.faq-panel');
  const icon = item.querySelector('.faq-icon');
  
  if (panel.style.maxHeight && panel.style.maxHeight !== '0px') {
    panel.style.maxHeight = '0px';
    icon.style.transform = 'rotate(0deg)';
  } else {
    panel.style.maxHeight = panel.scrollHeight + 'px';
    icon.style.transform = 'rotate(180deg)';
  }
}

// Яндекс.Карты
var myMap;
var myClusterer;
var poiCollection = null;
var isPoiLayerVisible = true;
var placemarks = {};
var MyDefaultIconLayout, MySelectedIconLayout, MyPoiIconLayout;
var activeQuickCategory = 'all';
var currentPreviewId = null;

// Filter and state variables (Defined at the top to prevent hoisting timing issues)
var activeCity = {!! isset($selectedCity) ? json_encode($selectedCity) : 'null' !!};
var selectedGuests = null;
var filterBedrooms = 0;   // 0 = Любое
var filterBeds = 0;       // 0 = Любое
var filterBathrooms = 0;  // 0 = Любое
var activeObjectType = 'apartments';
var filterBanquet = 0;    // 0 = Любое
var filterBathroomType = '';
var filterBalconyType = '';

// Toggle POI layer visibility (metro and landmarks) on the map
function togglePoiLayer(event) {
  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }
  
  isPoiLayerVisible = !isPoiLayerVisible;
  
  const btn = document.getElementById('map-poi-toggle-btn');
  if (btn) {
    if (isPoiLayerVisible) {
      btn.classList.add('active');
    } else {
      btn.classList.remove('active');
    }
  }
  
  if (poiCollection && myMap) {
    var currentZoom = myMap.getZoom();
    var shouldBeVisible = isPoiLayerVisible && (currentZoom >= 12);
    poiCollection.options.set('visible', shouldBeVisible);
    
    if (isPoiLayerVisible && currentZoom < 12) {
      showToast('🔍 Приблизьте карту, чтобы увидеть ориентиры и метро');
    }
  }
}
window.togglePoiLayer = togglePoiLayer;

// Points of Interest (Metro, Malls, Landmarks, Stations)
const MAP_POIS = [
  // Метро Минска
  { name: 'ст. м. Немига', type: 'metro', coords: [53.9056, 27.5539], icon: '🚇', desc: 'Автозаводская линия' },
  { name: 'ст. м. Октябрьская', type: 'metro', coords: [53.9015, 27.5613], icon: '🚇', desc: 'Московская линия (Переход)' },
  { name: 'ст. м. Площадь Победы', type: 'metro', coords: [53.9085, 27.5746], icon: '🚇', desc: 'Московская линия' },
  { name: 'ст. м. Площадь Якуба Коласа', type: 'metro', coords: [53.9160, 27.5830], icon: '🚇', desc: 'Московская линия' },
  { name: 'ст. м. Вокзальная', type: 'metro', coords: [53.8906, 27.5503], icon: '🚇', desc: 'Ж/Д Вокзал' },
  { name: 'ст. м. Академия Наук', type: 'metro', coords: [53.9214, 27.5991], icon: '🚇', desc: 'Московская линия' },
  { name: 'ст. м. Парк Челюскинцев', type: 'metro', coords: [53.9242, 27.6136], icon: '🚇', desc: 'Московская линия' },
  { name: 'ст. м. Восток', type: 'metro', coords: [53.9344, 27.6513], icon: '🚇', desc: 'Возле ТРЦ Dana Mall' },
  { name: 'ст. м. Пушкинская', type: 'metro', coords: [53.9096, 27.4965], icon: '🚇', desc: 'Автозаводская линия' },
  { name: 'ст. м. Каменная Горка', type: 'metro', coords: [53.9062, 27.4370], icon: '🚇', desc: 'Автозаводская линия' },
  { name: 'ст. м. Фрунзенская', type: 'metro', coords: [53.9057, 27.5392], icon: '🚇', desc: 'Автозаводская линия' },
  { name: 'ст. м. Юбилейная площадь', type: 'metro', coords: [53.9042, 27.5396], icon: '🚇', desc: 'Зеленолужская линия' },

  // Вокзалы
  { name: 'Минск-Пассажирский (Ж/Д)', type: 'station', coords: [53.8903, 27.5507], icon: '🚉', desc: 'Главный железнодорожный вокзал' },
  { name: 'Центральный автовокзал', type: 'station', coords: [53.8902, 27.5548], icon: '🚌', desc: 'Центральный автовокзал' },

  // ТРЦ и Утвержденные Торговые Центры
  { name: 'ТРЦ Galleria Minsk', type: 'mall', coords: [53.9085, 27.5488], icon: '🛍️', desc: 'ТЦ на Победителей' },
  { name: 'ТРЦ Dana Mall', type: 'mall', coords: [53.9333, 27.6517], icon: '🛍️', desc: 'Крупнейший ТРЦ у метро Восток' },
  { name: 'ТРЦ Замок', type: 'mall', coords: [53.9261, 27.5175], icon: '🛍️', desc: 'ТЦ на Победителей' },
  { name: 'ТРЦ Arena City', type: 'mall', coords: [53.9360, 27.4815], icon: '🛍️', desc: 'ТЦ возле Минск-Арены' },
  { name: 'ТД На Немиге', type: 'mall', coords: [53.9042, 27.5528], icon: '🛍️', desc: 'Торговый центр' },
  { name: 'ЦУМ Минск', type: 'mall', coords: [53.9160, 27.5815], icon: '🛍️', desc: 'Универмаг' },

  // Достопримечательности и Арены
  { name: 'Троицкое предместье', type: 'landmark', coords: [53.9080, 27.5565], icon: '🏛️', desc: 'Исторический центр' },
  { name: 'Минск-Арена', type: 'landmark', coords: [53.9362, 27.4816], icon: '🏟️', desc: 'Спортивный комплекс' },
  { name: 'Национальная библиотека', type: 'landmark', coords: [53.9312, 27.6458], icon: '💎', desc: 'Смотровая площадка' },
  { name: 'Парк Победы / Музей ВОВ', type: 'landmark', coords: [53.9158, 27.5372], icon: '🌳', desc: 'Мемориальный комплекс' },
  { name: 'Стадион Динамо', type: 'landmark', coords: [53.8954, 27.5604], icon: '🏟️', desc: 'Олимпийский стадион' }
];

function calculateDistanceMeters(lat1, lon1, lat2, lon2) {
  const R = 6371000;
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLon = (lon2 - lon1) * Math.PI / 180;
  const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return Math.round(R * c);
}

function isPointInPolygon(point, polygonCoords) {
  if (!point || point.length < 2 || !polygonCoords || polygonCoords.length < 3) return false;
  const x = point[0], y = point[1];
  let inside = false;
  for (let i = 0, j = polygonCoords.length - 1; i < polygonCoords.length; j = i++) {
    const xi = polygonCoords[i][0], yi = polygonCoords[i][1];
    const xj = polygonCoords[j][0], yj = polygonCoords[j][1];
    const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
    if (intersect) inside = !inside;
  }
  return inside;
}

function simplifyDrawingPoints(points) {
  if (!points || points.length < 3) return points;
  const result = [points[0]];
  for (let i = 1; i < points.length; i++) {
    const prev = result[result.length - 1];
    const curr = points[i];
    const dist = calculateDistanceMeters(prev[0], prev[1], curr[0], curr[1]);
    if (dist >= 25) {
      result.push(curr);
    }
  }
  const lastPoint = points[points.length - 1];
  if (result[result.length - 1] !== lastPoint) {
    result.push(lastPoint);
  }
  return result;
}
// Structured data for all listings
// Structured data for all listings
var apartmentsData = {
  @foreach($listings as $item)
    @php
      $cat = 'apartments';
      if ($item->type === 'cottage') {
          $cat = 'cottages';
      } elseif ($item->weekend_price > 0 && $item->type !== 'cottage') {
          $cat = 'hourly';
      } else {
          $cat = 'apartments';
      }

      if ($item instanceof \App\Models\Listing) {
          $itemSlug = $item->slug;
          $bedrooms = $item->rooms;
          $beds = $item->sleeping_places;
          $bathrooms = $item->bathrooms_count;
          $city = $item->city ?: 'Минск';
      } else {
          $title = $item->title ?? '';
          $rus = [
              'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
              'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'
          ];
          $lat = [
              'A', 'B', 'V', 'G', 'D', 'E', 'E', 'Zh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'Ts', 'Ch', 'Sh', 'Sch', '', 'Y', '', 'E', 'Yu', 'Ya',
              'a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ya'
          ];
          $transliterated = str_replace($rus, $lat, $title);
          $itemSlug = \Illuminate\Support\Str::slug($transliterated);

          $mockSpecs = [
              1 => ['bedrooms' => 1, 'beds' => 2, 'bathrooms' => 1, 'city' => 'Минск', 'tags' => 'wifi,bath,tv,kitchen,parking,pets,instant', 'host' => 'Наталья'],
              2 => ['bedrooms' => 1, 'beds' => 1, 'bathrooms' => 1, 'city' => 'Минск', 'tags' => 'wifi,conditioner,kitchen,instant,self', 'host' => 'Дмитрий'],
              3 => ['bedrooms' => 1, 'beds' => 1, 'bathrooms' => 1, 'city' => 'Минск', 'tags' => 'wifi,tv,kitchen,self', 'host' => 'Игорь'],
              4 => ['bedrooms' => 4, 'beds' => 6, 'bathrooms' => 2, 'city' => 'Брест', 'tags' => 'wifi,bath,pool,fireplace,parking,kitchen,grill,pets,events', 'host' => 'Сергей'],
              5 => ['bedrooms' => 2, 'beds' => 3, 'bathrooms' => 1, 'city' => 'Минск', 'tags' => 'wifi,tv,kitchen,parking,instant', 'host' => 'Наталья'],
              6 => ['bedrooms' => 3, 'beds' => 5, 'bathrooms' => 2, 'city' => 'Гродно', 'tags' => 'wifi,bath,pool,parking,kitchen,grill,pets,events,self', 'host' => 'Александр']
          ];
          $specs = $mockSpecs[$item->id] ?? ['bedrooms' => 1, 'beds' => 2, 'bathrooms' => 1, 'city' => 'Минск', 'tags' => 'wifi', 'host' => 'Частный владелец'];
          $bedrooms = $specs['bedrooms'];
          $beds = $specs['beds'];
          $bathrooms = $specs['bathrooms'];
          $city = $specs['city'];
      }

      $firstPhoto = null;
      $photos = [];
      if ($item->photos) {
          if (is_string($item->photos)) {
              $photos = json_decode($item->photos, true) ?: [];
          } elseif (is_array($item->photos)) {
              $photos = $item->photos;
          }
      }
      if (empty($photos) && $item->image) {
          $photos = [$item->image];
      }
      
      $isFakePhoto = false;
      $fakeBg = 'linear-gradient(140deg, #2C3E50, #3D5A6B)';
      $fakeEmoji = '🏢';
      
      if (!empty($photos) && is_array($photos)) {
          $firstPhoto = $photos[0];
          if (is_array($firstPhoto)) {
              $isFakePhoto = true;
              $fakeBg = $firstPhoto['bg'] ?? $fakeBg;
              $fakeEmoji = $firstPhoto['emoji'] ?? $fakeEmoji;
              $firstPhoto = null;
          } elseif (is_string($firstPhoto) && (strpos($firstPhoto, 'linear-gradient') !== false || strlen($firstPhoto) < 10)) {
              $isFakePhoto = true;
              $firstPhoto = null;
          }
      } else {
          $isFakePhoto = true;
      }
      
      $specsList = [];
      if ($item->area) {
          $specsList[] = $item->area . ' м²';
      }
      if ($item->type === 'cottage' || $item->type === 'agrotourism') {
          if ($item->rooms) {
              $specsList[] = $item->rooms . ' спален';
          }
      } else {
          if ($item->rooms) {
              $specsList[] = $item->rooms . '-комн.';
          }
      }
      if ($item->sleeping_places) {
          $specsList[] = $item->sleeping_places . ' сп. места';
      }
      $specsText = implode(' · ', $specsList);
      
      $itemLat = number_format($item->latitude ?: 53.900, 6, '.', '');
      $itemLng = number_format($item->longitude ?: 27.559, 6, '.', '');

      $displayAddress = $item->address;
      if (!empty($city) && str_starts_with($displayAddress, $city . ', ')) {
          $displayAddress = substr($displayAddress, strlen($city . ', '));
      }

      $phone = $item->phone ?? '';
      $whatsapp = $item->whatsapp ?? '';
      $viber = $item->viber ?? '';
      $telegram = $item->telegram ?? '';
      $nearestMetro = (!empty($item->nearest_metro) && $item->nearest_metro !== 'Нет' && $item->nearest_metro !== 'Метро не указано') ? $item->nearest_metro : '';
      $weekendPrice = $item->weekend_price ?? 0;
      $area = $item->area ?? 0;

      $bedroomsText = $bedrooms === 0 ? 'Студия' : $bedrooms . ' ' . ($bedrooms == 1 ? 'комната' : ($bedrooms <= 4 ? 'комнаты' : 'комнат'));
      $guestsText = $beds . ' ' . ($beds == 1 ? 'гость' : ($beds <= 4 ? 'гостя' : 'гостей'));

      // Whatsapp Link format
      $waLink = '';
      if (!empty($whatsapp)) {
          $waVal = trim($whatsapp);
          if (str_starts_with($waVal, 'http://') || str_starts_with($waVal, 'https://') || str_starts_with($waVal, 'wa.me/')) {
              $waLink = $waVal;
          } else {
              $cleanPhone = preg_replace('/[^0-9]/', '', $waVal);
              if (str_starts_with($cleanPhone, '80') && strlen($cleanPhone) == 11) {
                  $cleanPhone = '375' . substr($cleanPhone, 2);
              } elseif (strlen($cleanPhone) == 9) {
                  $cleanPhone = '375' . $cleanPhone;
              }
              $waLink = "https://wa.me/" . $cleanPhone;
          }
      }

      // Viber Link format
      $vbLink = '';
      if (!empty($viber)) {
          $vbVal = trim($viber);
          if (str_starts_with($vbVal, 'viber://') || str_starts_with($vbVal, 'http://') || str_starts_with($vbVal, 'https://')) {
              $vbLink = $vbVal;
          } else {
              $cleanPhone = preg_replace('/[^0-9]/', '', $vbVal);
              if (str_starts_with($cleanPhone, '80') && strlen($cleanPhone) == 11) {
                  $cleanPhone = '375' . substr($cleanPhone, 2);
              } elseif (strlen($cleanPhone) == 9) {
                  $cleanPhone = '375' . $cleanPhone;
              }
              $vbLink = "viber://chat?number=%2B" . $cleanPhone;
          }
      }

      // Telegram Link format
      $tgLink = '';
      if (!empty($telegram)) {
          $tgVal = trim($telegram);
          if (str_starts_with($tgVal, 'http://') || str_starts_with($tgVal, 'https://') || str_starts_with($tgVal, 't.me/')) {
              $tgLink = $tgVal;
          } else {
              $tgUsername = ltrim($tgVal, '@');
              $tgLink = "https://t.me/" . $tgUsername;
          }
      }
    @endphp
    "{{ $item->id }}": {
      id: {{ $item->id }},
      title: {!! json_encode($item->title) !!},
      address: {!! json_encode($displayAddress) !!},
      basePrice: {{ $item->price }},
      category: "{{ $cat }}",
      coords: [{{ $itemLat }}, {{ $itemLng }}],
      isAmber: {{ $cat === 'hourly' ? 'true' : 'false' }},
      slug: {!! json_encode($itemSlug) !!},
      firstPhoto: {!! json_encode($firstPhoto) !!},
      isFakePhoto: {{ $isFakePhoto ? 'true' : 'false' }},
      fakeBg: "{{ $fakeBg }}",
      fakeEmoji: "{{ $fakeEmoji }}",
      specsText: {!! json_encode($specsText) !!},
      phone: {!! json_encode($phone) !!},
      whatsapp: {!! json_encode($waLink) !!},
      viber: {!! json_encode($vbLink) !!},
      telegram: {!! json_encode($tgLink) !!},
      nearestMetro: {!! json_encode($nearestMetro) !!},
      weekendPrice: {{ $weekendPrice }},
      area: {{ $area }},
      bedroomsText: {!! json_encode($bedroomsText) !!},
      guestsText: {!! json_encode($guestsText) !!}
    },
  @endforeach
};

// Helper for visited listings
function getVisitedList() {
  try {
    return JSON.parse(localStorage.getItem('flatly_visited_ids') || '[]');
  } catch (e) {
    return [];
  }
}

function markAsVisited(id) {
  let list = getVisitedList();
  id = Number(id);
  if (!list.includes(id)) {
    list.push(id);
    localStorage.setItem('flatly_visited_ids', JSON.stringify(list));
    
    // Update marker styling
    const placemark = placemarks[id];
    if (placemark) {
      placemark.properties.set('visitedClass', 'visited');
    }
  }
}

// Глобальная функция обновления цен в соответствии с выбранной валютой
function updateCurrencyDisplay() {
  try {
    const currentCurrency = localStorage.getItem('flatly_currency') || 'BYN';
    const rate = EXCHANGE_RATES[currentCurrency] || 1.0;
    const formatFn = CURRENCY_FORMATS[currentCurrency];

    // 1. Обновляем цены на карточках объявлений
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

    // 2. Обновляем карточку превью на карте, если она открыта
    if (typeof currentPreviewId !== 'undefined' && currentPreviewId !== null) {
      const apt = apartmentsData[currentPreviewId];
      if (apt) {
        const priceDayEl = document.getElementById('map-card-price-day');
        if (priceDayEl) {
          priceDayEl.innerText = formatFn(apt.basePrice * rate);
        }
        const priceHourEl = document.getElementById('map-card-price-hour');
        if (priceHourEl) {
          if (apt.weekendPrice > 0) {
            priceHourEl.innerText = formatFn(apt.weekendPrice * rate);
            priceHourEl.className = 'price-amount price-val';
          } else {
            priceHourEl.innerText = '—';
            priceHourEl.className = 'price-amount no-hourly';
          }
        }
      }
    }

    // 3. Обновляем метки на карте Yandex
    if (typeof placemarks !== 'undefined' && placemarks) {
      const visitedList = getVisitedList();
      Object.keys(placemarks).forEach(function(id) {
        const placemark = placemarks[id];
        if (placemark) {
          const basePrice = placemark.properties.get('basePrice');
          if (typeof basePrice !== 'undefined') {
            placemark.properties.set('price', formatFn(basePrice * rate));
          }
          const isVisited = visitedList.includes(Number(id));
          placemark.properties.set('visitedClass', isVisited ? 'visited' : '');
          
          // Принудительно обновляем макет, чтобы применить новые свойства
          const currentLayout = placemark.options.get('iconLayout');
          if (currentLayout) {
            placemark.options.set('iconLayout', currentLayout);
          }
        }
      });
    }
  } catch (e) {
    console.error('Error updating currency display on listings index page', e);
  }
}

function updateMapMarkers(newApartmentsData) {
  if (!myMap) return;
  
  if (myClusterer) {
    myClusterer.removeAll();
  } else if (myMap.geoObjects) {
    myMap.geoObjects.removeAll();
  }
  
  // 2. Clear placemarks global object
  placemarks = {};
  
  // 3. Create new placemarks
  const currentCurrency = localStorage.getItem('flatly_currency') || 'BYN';
  const rate = EXCHANGE_RATES[currentCurrency] || 1.0;
  const formatFn = CURRENCY_FORMATS[currentCurrency];
  const visitedList = getVisitedList();
  
  Object.keys(newApartmentsData).forEach(function(key) {
    var apt = newApartmentsData[key];
    var isVisited = visitedList.includes(Number(apt.id));
    
    var placemark = new ymaps.Placemark(apt.coords, {
      id: apt.id,
      basePrice: apt.basePrice,
      isAmber: apt.isAmber,
      price: formatFn(apt.basePrice * rate),
      visitedClass: isVisited ? 'visited' : '',
      hourlyBadge: apt.isAmber ? '<span class="hourly-dot">🕒</span>' : ''
    }, {
      iconLayout: MyDefaultIconLayout,
      iconShape: {
        type: 'Rectangle',
        coordinates: [
          [-30, -15], [30, 15]
        ]
      },
      hasBalloon: false
    });

    // Hover effect
    placemark.events.add('mouseenter', function() {
      if (myMap && myMap.geoObjects) {
        placemark.options.set('iconLayout', MySelectedIconLayout);
        highlightCard(apt.id);
      }
    });

    placemark.events.add('mouseleave', function() {
      if (myMap && myMap.geoObjects) {
        const activeCurr = localStorage.getItem('flatly_currency') || 'BYN';
        const curRate = EXCHANGE_RATES[activeCurr] || 1.0;
        const curFormat = CURRENCY_FORMATS[activeCurr];
        const isVisitedCheck = getVisitedList().includes(Number(apt.id));
        
        placemark.properties.set('price', curFormat(apt.basePrice * curRate));
        placemark.properties.set('visitedClass', isVisitedCheck ? 'visited' : '');
        placemark.options.set('iconLayout', MyDefaultIconLayout);
        
        unhighlightCard(apt.id);
      }
    });

    // Click opens preview
    placemark.events.add('click', function(e) {
      if (e && typeof e.stopPropagation === 'function') {
        e.stopPropagation();
      }
      showMapPreview(apt.id);
    });

    placemarks[apt.id] = placemark;
  });

  if (myClusterer) {
    myClusterer.add(Object.values(placemarks));
  }
}

function loadListingsAJAX(targetUrl) {
  // 1. Show loading state
  const gridEl = document.getElementById('listings-grid');
  if (gridEl) {
    gridEl.style.opacity = '0.5';
    gridEl.style.pointerEvents = 'none';
  }
  
  // Add ajax parameter to url
  const urlObj = new URL(targetUrl, window.location.origin);
  urlObj.searchParams.set('ajax', '1');
  
  fetch(urlObj.toString(), {
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => {
    if (!response.ok) throw new Error('Network response was not ok');
    return response.json();
  })
  .then(data => {
    // 2. Update address bar with final canonical URL
    const canonicalUrl = data.canonicalUrl || targetUrl;
    const historyUrl = new URL(canonicalUrl, window.location.origin);
    historyUrl.searchParams.delete('ajax');
    window.history.pushState({ ajax: true }, '', historyUrl.pathname + historyUrl.search);
    
    // Update active class on quick filter link tags (.qf-link-tag) based on the new URL path
    const newPath = historyUrl.pathname;
    document.querySelectorAll('.qf-link-tag').forEach(link => {
      const linkPath = new URL(link.href, window.location.origin).pathname;
      const normPath = p => p.replace(/^\/+|\/+$/g, '');
      if (normPath(linkPath) === normPath(newPath)) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
    
    // 3. Update grid HTML
    if (gridEl && data.html) {
      gridEl.innerHTML = data.html;
    }
    
    // 4. Update apartmentsData
    if (data.apartmentsData) {
      apartmentsData = data.apartmentsData;
    }
    
    // 5. Update SEO elements
    if (data.seo) {
      if (data.seo.title) document.title = data.seo.title;
      const metaDesc = document.querySelector('meta[name="description"]');
      if (metaDesc && data.seo.description) metaDesc.setAttribute('content', data.seo.description);
      const h1El = document.getElementById('page-main-title');
      if (h1El && data.seo.h1) h1El.textContent = data.seo.h1;
      const breadcrumbsEl = document.getElementById('breadcrumbs-wrapper');
      if (breadcrumbsEl) breadcrumbsEl.innerHTML = data.seo.breadcrumbs || '';
    }
    
    // 6. Update Map Markers
    if (data.apartmentsData) {
      updateMapMarkers(data.apartmentsData);
    }
    
    // 7. Update activeCity variable
    if (typeof activeCity !== 'undefined') {
      activeCity = data.selectedCity;
    }
    
    // 8. Re-apply active filters and fit bounds
    applyFilters(true, true);
    
    // Smooth scroll to catalog
    const listingsEl = document.querySelector('.listings');
    if (listingsEl) {
      listingsEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  })
  .catch(error => {
    console.error('AJAX filtering error:', error);
    showToast('❌ Ошибка загрузки объявлений');
  })
  .finally(() => {
    if (gridEl) {
      gridEl.style.opacity = '1';
      gridEl.style.pointerEvents = 'auto';
    }
  });
}
function updateMapPrices() {
  updateCurrencyDisplay();
}
window.loadListingsAJAX = loadListingsAJAX;

window.initMap = initMap;
if (window.ymapsLoaded && typeof ymaps !== 'undefined') {
  ymaps.ready(initMap);
}

function initMap() {
  var initialZoom = {!! isset($selectedCity) ? 13 : 10 !!};
  if (window.innerWidth <= 991 && !{!! isset($selectedCity) ? 'true' : 'false' !!}) {
    initialZoom = 8; // Zoom for Belarus on mobile
  }

  myMap = new ymaps.Map("map", {
    center: {!! isset($mapCenter) ? json_encode($mapCenter) : '[53.9006, 27.5590]' !!}, // fallback to Minsk
    zoom: initialZoom,
    controls: ['fullscreenControl']
  });

  // Инициализация слоя POI (Ориентиры и метро)
  poiCollection = new ymaps.GeoObjectCollection({}, {
    preset: 'islands#circleIcon'
  });

  MyPoiIconLayout = ymaps.templateLayoutFactory.createClass(
    '<div class="map-poi-badge $[properties.poiType]">' +
      '<span>$[properties.icon]</span>' +
      '<span>$[properties.shortName]</span>' +
    '</div>'
  );

  var currentMapCenter = myMap.getCenter();
  MAP_POIS.forEach(function(poi) {
    if (currentMapCenter && typeof calculateDistanceMeters === 'function') {
      if (calculateDistanceMeters(currentMapCenter[0], currentMapCenter[1], poi.coords[0], poi.coords[1]) > 45000) {
        return;
      }
    }
    var poiPlacemark = new ymaps.Placemark(poi.coords, {
      hintContent: poi.name + (poi.desc ? ' — ' + poi.desc : ''),
      balloonContentHeader: poi.icon + ' ' + poi.name,
      balloonContentBody: poi.desc || '',
      icon: poi.icon,
      shortName: poi.name.replace('ст. м. ', ''),
      poiType: poi.type
    }, {
      iconLayout: MyPoiIconLayout,
      iconShape: {
        type: 'Rectangle',
        coordinates: [[-20, -10], [60, 10]]
      }
    });

    poiCollection.add(poiPlacemark);
  });

  myMap.geoObjects.add(poiCollection);

  // Show POIs only when zoomed in (zoom >= 12)
  function updatePoiVisibility() {
    if (poiCollection) {
      var currentZoom = myMap.getZoom();
      var shouldBeVisible = isPoiLayerVisible && (currentZoom >= 12);
      poiCollection.options.set('visible', shouldBeVisible);
    }
  }
  updatePoiVisibility();
  myMap.events.add('boundschange', updatePoiVisibility);

  // Инициализация Кластеризатора для меток объявлений с фирменным стилем Amber
  var ClusterIconLayout = ymaps.templateLayoutFactory.createClass(
    '<div class="map-cluster-badge">$[properties.geoObjects.length]</div>'
  );

  myClusterer = new ymaps.Clusterer({
    clusterIconLayout: ClusterIconLayout,
    clusterIconShape: {
      type: 'Rectangle',
      coordinates: [[-19, -19], [19, 19]]
    },
    groupByCoordinates: false,
    clusterDisableClickZoom: false,
    clusterHideIconOnBalloonOpen: false,
    geoObjectHideIconOnBalloonOpen: false,
    clusterMinClusterSize: 2,
    gridSize: 64,
    margin: 10
  });
  myMap.geoObjects.add(myClusterer);

  myMap.controls.add('zoomControl', {
    position: { left: 20, top: 120 },
    size: 'large'
  });

  // Custom price pins using CSS styling
  MyDefaultIconLayout = ymaps.templateLayoutFactory.createClass(
    '<div class="map-price $[properties.visitedClass]">$[properties.price] $[properties.hourlyBadge]</div>', {
      build: function() {
        MyDefaultIconLayout.superclass.build.call(this);
        var el = this.getElement();
        if (!el && this.getParentElement()) {
          el = this.getParentElement().lastElementChild || this.getParentElement().lastChild;
        }
        if (el) {
          this.element = el;
          this.onClick = this.onClick.bind(this);
          this.element.addEventListener('click', this.onClick);
        }
      },
      clear: function() {
        if (this.element) {
          this.element.removeEventListener('click', this.onClick);
        }
        MyDefaultIconLayout.superclass.clear.call(this);
      },
      onClick: function(e) {
        if (e && typeof e.stopPropagation === 'function') {
          e.stopPropagation();
        }
        var properties = this.getData().properties;
        var id = properties ? properties.get('id') : null;
        if (id) {
          window.showMapPreview(id);
        }
      }
    }
  );

  MySelectedIconLayout = ymaps.templateLayoutFactory.createClass(
    '<div class="map-price sel">$[properties.price] $[properties.hourlyBadge]</div>', {
      build: function() {
        MySelectedIconLayout.superclass.build.call(this);
        var el = this.getElement();
        if (!el && this.getParentElement()) {
          el = this.getParentElement().lastElementChild || this.getParentElement().lastChild;
        }
        if (el) {
          this.element = el;
          this.onClick = this.onClick.bind(this);
          this.element.addEventListener('click', this.onClick);
        }
      },
      clear: function() {
        if (this.element) {
          this.element.removeEventListener('click', this.onClick);
        }
        MySelectedIconLayout.superclass.clear.call(this);
      },
      onClick: function(e) {
        if (e && typeof e.stopPropagation === 'function') {
          e.stopPropagation();
        }
        var properties = this.getData().properties;
        var id = properties ? properties.get('id') : null;
        if (id) {
          window.showMapPreview(id);
        }
      }
    }
  );


  const currentCurrency = localStorage.getItem('flatly_currency') || 'BYN';
  const rate = EXCHANGE_RATES[currentCurrency] || 1.0;
  const formatFn = CURRENCY_FORMATS[currentCurrency];
  const visitedList = getVisitedList();

  // Create markers
  Object.keys(apartmentsData).forEach(function(key) {
    var apt = apartmentsData[key];
    var isVisited = visitedList.includes(Number(apt.id));
    
    var placemark = new ymaps.Placemark(apt.coords, {
      id: apt.id,
      basePrice: apt.basePrice,
      isAmber: apt.isAmber,
      price: formatFn(apt.basePrice * rate),
      visitedClass: isVisited ? 'visited' : '',
      hourlyBadge: apt.isAmber ? '<span class="hourly-dot">🕒</span>' : ''
    }, {
      iconLayout: MyDefaultIconLayout,
      iconShape: {
        type: 'Rectangle',
        coordinates: [
          [-30, -15], [30, 15]
        ]
      },
      hasBalloon: false
    });

    // Hover effect
    placemark.events.add('mouseenter', function() {
      if (myMap && myMap.geoObjects) {
        placemark.options.set('iconLayout', MySelectedIconLayout);
        highlightCard(apt.id);
      }
    });

    placemark.events.add('mouseleave', function() {
      if (myMap && myMap.geoObjects) {
        const activeCurr = localStorage.getItem('flatly_currency') || 'BYN';
        const curRate = EXCHANGE_RATES[activeCurr] || 1.0;
        const curFormat = CURRENCY_FORMATS[activeCurr];
        const isVisitedCheck = getVisitedList().includes(Number(apt.id));
        
        placemark.properties.set('price', curFormat(apt.basePrice * curRate));
        placemark.properties.set('visitedClass', isVisitedCheck ? 'visited' : '');
        placemark.options.set('iconLayout', MyDefaultIconLayout);
        
        unhighlightCard(apt.id);
      }
    });

    // Click opens preview
    placemark.events.add('click', function(e) {
      if (e && typeof e.stopPropagation === 'function') {
        e.stopPropagation();
      }
      showMapPreview(apt.id);
    });

    placemarks[apt.id] = placemark;
  });

  // Click on map closes preview
  myMap.events.add('click', function(e) {
    if (e.get('target') === myMap) {
      if (window.justClickedMarker) {
        window.justClickedMarker = false;
        return;
      }
      closeMapPreview();
    }
  });

  // Show "Search in this area" button when map is moved/zoomed by user
  myMap.events.add(['boundschange', 'actionend'], function(e) {
    if (window.isProgrammaticMapMove) {
      window.isProgrammaticMapMove = false;
      return;
    }
    var areaBox = document.getElementById('map-search-area-box');
    if (areaBox) {
      areaBox.style.display = 'flex';
      var areaBtn = document.getElementById('map-search-area-btn');
      if (areaBtn && !window.isMapBoundsFilterActive) {
        areaBtn.classList.remove('active-area');
        areaBtn.querySelector('span').textContent = 'Искать в этой области';
      }
    }
  });

  // Handle fullscreen transitions to keep controls visible
  // Event listeners for Freehand Drawing on map (Mouse)
  function finishMapDrawing() {
    if (!window.isDrawingMode || !isMouseDownDrawing) return;
    isMouseDownDrawing = false;

    const sampledPoints = simplifyDrawingPoints(currentDrawingPoints);

    if (sampledPoints && sampledPoints.length >= 3) {
      var closedPoints = sampledPoints.slice();
      closedPoints.push(closedPoints[0]);

      if (activePolylineObject) {
        try { myMap.geoObjects.remove(activePolylineObject); } catch(err){}
        activePolylineObject = null;
      }

      if (drawnPolygonObject) {
        try { myMap.geoObjects.remove(drawnPolygonObject); } catch(err){}
      }

      drawnPolygonObject = new ymaps.Polygon([closedPoints], {
        hintContent: 'Выделенная область'
      }, {
        fillColor: 'rgba(217, 119, 6, 0.25)',
        strokeColor: '#D97706',
        strokeWidth: 3,
        strokeOpacity: 0.95
      });

      myMap.geoObjects.add(drawnPolygonObject);

      window.activeDrawnPolygonCoords = closedPoints;
      window.isPolygonFilterActive = true;

      const polyBar = document.getElementById('map-polygon-bar');
      if (polyBar) polyBar.style.display = 'flex';

      cancelDrawMode(null);

      applyFilters(true, false, false);
      showToast('✏️ Фильтр по выделенной области применён');
    } else {
      if (activePolylineObject) {
        try { myMap.geoObjects.remove(activePolylineObject); } catch(err){}
        activePolylineObject = null;
      }
    }
  }

  myMap.events.add('mousedown', function(e) {
    if (!window.isDrawingMode) return;
    isMouseDownDrawing = true;
    currentDrawingPoints = [e.get('coords')];

    // Prevent default browser drag-select behavior when drawing
    if (e.get('domEvent')) {
      try { e.get('domEvent').preventDefault(); } catch(err){}
    }

    if (activePolylineObject) {
      try { myMap.geoObjects.remove(activePolylineObject); } catch(err){}
      activePolylineObject = null;
    }

    activePolylineObject = new ymaps.Polyline(currentDrawingPoints, {}, {
      strokeColor: '#D97706',
      strokeWidth: 3,
      strokeOpacity: 0.95
    });
    myMap.geoObjects.add(activePolylineObject);
  });

  myMap.events.add('mousemove', function(e) {
    if (!window.isDrawingMode || !isMouseDownDrawing) return;
    const coords = e.get('coords');
    if (coords && coords.length === 2) {
      currentDrawingPoints.push(coords);
      if (activePolylineObject) {
        activePolylineObject.geometry.setCoordinates(currentDrawingPoints);
      }
    }
  });

  myMap.events.add('mouseup', function(e) {
    finishMapDrawing();
  });

  // Event listeners for Touch Drawing on Mobile Devices
  const mapElem = myMap.container.getElement();
  
  function getCoordsFromTouch(touch) {
    if (!myMap) return null;
    const rect = mapElem.getBoundingClientRect();
    const pagePixels = [touch.clientX - rect.left, touch.clientY - rect.top];
    const globalPixels = myMap.converter.pageToGlobal(pagePixels);
    return myMap.options.get('projection').fromGlobalPixels(globalPixels, myMap.getZoom());
  }

  mapElem.addEventListener('touchstart', function(e) {
    if (!window.isDrawingMode) return;
    if (e.touches.length === 1) {
      // Prevent browser scroll/pan during touch drawing
      e.preventDefault();
      isMouseDownDrawing = true;
      const coords = getCoordsFromTouch(e.touches[0]);
      if (coords) {
        currentDrawingPoints = [coords];
        if (activePolylineObject) {
          try { myMap.geoObjects.remove(activePolylineObject); } catch(err){}
          activePolylineObject = null;
        }
        activePolylineObject = new ymaps.Polyline(currentDrawingPoints, {}, {
          strokeColor: '#D97706',
          strokeWidth: 3,
          strokeOpacity: 0.95
        });
        myMap.geoObjects.add(activePolylineObject);
      }
    }
  }, { passive: false });

  mapElem.addEventListener('touchmove', function(e) {
    if (!window.isDrawingMode || !isMouseDownDrawing) return;
    if (e.touches.length === 1) {
      // Prevent browser scroll/pan during touch drawing
      e.preventDefault();
      const coords = getCoordsFromTouch(e.touches[0]);
      if (coords) {
        currentDrawingPoints.push(coords);
        if (activePolylineObject) {
          activePolylineObject.geometry.setCoordinates(currentDrawingPoints);
        }
      }
    }
  }, { passive: false });

  mapElem.addEventListener('touchend', function(e) {
    finishMapDrawing();
  });

  // Global document level mouseup/touchend to guarantee drawing completion
  document.addEventListener('mouseup', function() {
    if (window.isDrawingMode && isMouseDownDrawing) {
      finishMapDrawing();
    }
  });

  document.addEventListener('touchend', function() {
    if (window.isDrawingMode && isMouseDownDrawing) {
      finishMapDrawing();
    }
  });

  myMap.container.events.add('fullscreenenter', function () {
    var mapElement = myMap.container.getElement();
    var geoBtn = document.querySelector('.map-geo-btn');
    var previewCard = document.getElementById('map-card-preview');
    var areaBox = document.getElementById('map-search-area-box');
    var toolbar = document.querySelector('.map-controls-toolbar');
    var drawHint = document.getElementById('map-draw-hint');
    var polyBar = document.getElementById('map-polygon-bar');
    
    if (geoBtn) { mapElement.appendChild(geoBtn); geoBtn.style.zIndex = '10000'; }
    if (previewCard) { mapElement.appendChild(previewCard); previewCard.style.zIndex = '10001'; }
    if (areaBox) { mapElement.appendChild(areaBox); areaBox.style.zIndex = '10002'; }
    if (toolbar) { mapElement.appendChild(toolbar); toolbar.style.zIndex = '10003'; }
    if (drawHint) { mapElement.appendChild(drawHint); drawHint.style.zIndex = '10004'; }
    if (polyBar) { mapElement.appendChild(polyBar); polyBar.style.zIndex = '10005'; }
  });

  myMap.container.events.add('fullscreenexit', function () {
    var mapCol = document.querySelector('.map-col');
    var geoBtn = document.querySelector('.map-geo-btn');
    var previewCard = document.getElementById('map-card-preview');
    var areaBox = document.getElementById('map-search-area-box');
    var toolbar = document.querySelector('.map-controls-toolbar');
    var drawHint = document.getElementById('map-draw-hint');
    var polyBar = document.getElementById('map-polygon-bar');
    
    if (mapCol) {
      if (geoBtn) { mapCol.appendChild(geoBtn); geoBtn.style.zIndex = ''; }
      if (previewCard) { mapCol.appendChild(previewCard); previewCard.style.zIndex = ''; }
      if (areaBox) { mapCol.appendChild(areaBox); areaBox.style.zIndex = ''; }
      if (toolbar) { mapCol.appendChild(toolbar); toolbar.style.zIndex = ''; }
      if (drawHint) { mapCol.appendChild(drawHint); drawHint.style.zIndex = ''; }
      if (polyBar) { mapCol.appendChild(polyBar); polyBar.style.zIndex = ''; }
    }
  });

  // Initial filter apply
  applyFilters(false, true);
}

function highlightCard(id) {
  var card = document.querySelector('.card[data-id="' + id + '"]');
  if (card) {
    card.style.transform = 'translateY(-4px)';
    card.style.boxShadow = '0 6px 16px rgba(0,0,0,0.1)';
    card.style.transition = 'transform 0.2s, box-shadow 0.2s';
  }
}

function unhighlightCard(id) {
  var card = document.querySelector('.card[data-id="' + id + '"]');
  if (card) {
    card.style.transform = '';
    card.style.boxShadow = '';
  }
}

function highlightMarker(id) {
  id = Number(id);
  if (placemarks) {
    var placemark = placemarks[id];
    if (placemark) {
      placemark.options.set('iconLayout', MySelectedIconLayout);
    }
  }
}

function unhighlightMarker(id) {
  id = Number(id);

  if (placemarks) {
    var placemark = placemarks[id];
    if (placemark) {
      const activeCurr = localStorage.getItem('flatly_currency') || 'BYN';
      const curRate = (typeof EXCHANGE_RATES !== 'undefined' && EXCHANGE_RATES[activeCurr]) ? EXCHANGE_RATES[activeCurr] : 1.0;
      const curFormat = (typeof CURRENCY_FORMATS !== 'undefined' && CURRENCY_FORMATS[activeCurr]) ? CURRENCY_FORMATS[activeCurr] : function(v){ return Math.round(v) + ' BYN'; };
      const isVisitedCheck = typeof getVisitedList === 'function' ? getVisitedList().includes(Number(id)) : false;
      
      var basePrice = placemark.properties.get('basePrice');
      if (basePrice !== undefined) {
        placemark.properties.set('price', curFormat(basePrice * curRate));
      }
      placemark.properties.set('visitedClass', isVisitedCheck ? 'visited' : '');
      placemark.options.set('iconLayout', MyDefaultIconLayout);
    }
  }
}

function showMapPreview(id) {
  id = Number(id);
  window.justClickedMarker = true;
  setTimeout(function() {
    window.justClickedMarker = false;
  }, 500);

  const apt = apartmentsData[id];
  if (!apt) return;
  
  currentPreviewId = id;
  markAsVisited(id);

  
  const previewEl = document.getElementById('map-card-preview');
  const previewImg = document.getElementById('map-card-img');
  const previewFake = document.getElementById('map-card-fake-img');
  
  if (apt.firstPhoto) {
    previewImg.src = apt.firstPhoto;
    previewImg.alt = `Снять посуточно: ${apt.title}`;
    previewImg.style.display = 'block';
    previewFake.style.display = 'none';
  } else {
    previewImg.style.display = 'none';
    previewFake.style.display = 'flex';
    previewFake.style.background = apt.fakeBg || 'linear-gradient(140deg, #2C3E50, #3D5A6B)';
    previewFake.innerHTML = apt.fakeEmoji ? `<span style="font-size: 40px;">${apt.fakeEmoji}</span>` : `
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.7;">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
        <polyline points="9 22 9 12 15 12 15 22"></polyline>
      </svg>
    `;
  }
  
  const titleEl = document.getElementById('map-card-title');
  if (titleEl) {
    titleEl.innerText = apt.title;
    titleEl.href = '/listings/' + id + (apt.slug ? '-' + apt.slug : '');
  }
  document.getElementById('map-card-address').innerText = apt.address;
  
  // Calculate Walking Time & Distances to POIs
  if (apt.coords && apt.coords.length === 2) {
    const lat = apt.coords[0];
    const lng = apt.coords[1];

    let closestMetro = null;
    let minMetroDist = Infinity;

    let closestLandmark = null;
    let minLandmarkDist = Infinity;

    MAP_POIS.forEach(function(poi) {
      const dist = calculateDistanceMeters(lat, lng, poi.coords[0], poi.coords[1]);
      if (poi.type === 'metro') {
        if (dist < minMetroDist) {
          minMetroDist = dist;
          closestMetro = poi;
        }
      } else {
        if (dist < minLandmarkDist) {
          minLandmarkDist = dist;
          closestLandmark = poi;
        }
      }
    });

    const poiBox = document.getElementById('map-card-poi-box');
    const metroText = document.getElementById('map-card-metro-time');
    const landmarkRow = document.getElementById('map-card-landmark-row');
    const landmarkIcon = document.getElementById('map-card-landmark-icon');
    const landmarkText = document.getElementById('map-card-landmark-time');

    let hasPoi = false;

    if (closestMetro && minMetroDist <= 3500) {
      const walkMin = Math.ceil(minMetroDist / 80);
      const text = (walkMin <= 25) 
        ? `🚶 ${walkMin} мин пешком (${minMetroDist} м) до ${closestMetro.name}`
        : `🚇 ${closestMetro.name} (~${(minMetroDist/1000).toFixed(1)} км)`;
      if (metroText) metroText.innerText = text;
      hasPoi = true;
    } else if (metroText) {
      if (apt.nearestMetro && apt.nearestMetro !== 'Нет' && apt.nearestMetro !== 'Метро не указано') {
        metroText.innerText = '🚇 ' + apt.nearestMetro;
        hasPoi = true;
      }
    }

    if (closestLandmark && minLandmarkDist <= 3000) {
      const walkMin = Math.ceil(minLandmarkDist / 80);
      const text = (walkMin <= 25)
        ? `${walkMin} мин пешком до ${closestLandmark.name}`
        : `${closestLandmark.name} (~${(minLandmarkDist/1000).toFixed(1)} км)`;
      if (landmarkIcon) landmarkIcon.innerText = closestLandmark.icon;
      if (landmarkText) landmarkText.innerText = text;
      if (landmarkRow) landmarkRow.style.display = 'flex';
      hasPoi = true;
    } else if (landmarkRow) {
      landmarkRow.style.display = 'none';
    }

    if (poiBox) {
      poiBox.style.display = hasPoi ? 'flex' : 'none';
    }
  }
  
  // Prices
  const currentCurrency = localStorage.getItem('flatly_currency') || 'BYN';
  const rate = EXCHANGE_RATES[currentCurrency] || 1.0;
  const formatFn = CURRENCY_FORMATS[currentCurrency];
  
  const priceDayEl = document.getElementById('map-card-price-day');
  if (priceDayEl) {
    priceDayEl.innerText = formatFn(apt.basePrice * rate);
  }
  
  const priceHourEl = document.getElementById('map-card-price-hour');
  if (priceHourEl) {
    if (apt.weekendPrice > 0) {
      priceHourEl.innerText = formatFn(apt.weekendPrice * rate);
      priceHourEl.className = 'price-amount price-val';
    } else {
      priceHourEl.innerText = '—';
      priceHourEl.className = 'price-amount no-hourly';
    }
  }
  
  // Badges
  const badgeArea = document.getElementById('map-card-badge-area');
  const badgeRooms = document.getElementById('map-card-badge-rooms');
  const badgeGuests = document.getElementById('map-card-badge-guests');
  
  if (badgeArea) {
    badgeArea.innerText = (apt.area ? apt.area : 35) + ' м²';
  }
  if (badgeRooms) {
    badgeRooms.innerText = apt.bedroomsText || '1 комната';
  }
  if (badgeGuests) {
    badgeGuests.innerText = apt.guestsText || '2 гостя';
  }
  
  // Contact details
  const mapPhoneBtn = document.getElementById('map-phone-btn');
  const mapMessengers = document.getElementById('map-messengers');
  const phoneText = document.getElementById('map-card-revealed-phone');
  
  if (phoneText) {
    phoneText.innerText = apt.phone || 'Нет телефона';
  }
  
  // Reset contact button state
  if (mapPhoneBtn && mapMessengers) {
    mapPhoneBtn.style.display = '';
    mapMessengers.style.display = 'none';
  }
  
  // Setup messenger links
  const waLink = document.getElementById('map-wa-link');
  const vbLink = document.getElementById('map-vb-link');
  const tgLink = document.getElementById('map-tg-link');
  
  if (waLink) {
    if (apt.whatsapp) {
      waLink.href = apt.whatsapp;
      waLink.style.display = '';
    } else {
      waLink.style.display = 'none';
    }
  }
  if (vbLink) {
    if (apt.viber) {
      vbLink.href = apt.viber;
      vbLink.style.display = '';
    } else {
      vbLink.style.display = 'none';
    }
  }
  if (tgLink) {
    if (apt.telegram) {
      tgLink.href = apt.telegram;
      tgLink.style.display = '';
    } else {
      tgLink.style.display = 'none';
    }
  }

  previewEl.style.display = 'flex';
  setTimeout(() => {
    previewEl.classList.add('show');
  }, 10);
}
window.showMapPreview = showMapPreview;

function closeMapPreview(event) {
  if (event) event.stopPropagation();
  const previewEl = document.getElementById('map-card-preview');
  if (previewEl) {
    previewEl.classList.remove('show');
    setTimeout(() => {
      previewEl.style.display = 'none';
    }, 300);
  }
  if (currentPreviewId) {
    currentPreviewId = null;
  }
}

function toggleMapCardContact(event) {
  if (event) event.stopPropagation();
  const btn = document.getElementById('map-phone-btn');
  const mRow = document.getElementById('map-messengers');
  if (btn && mRow) {
    if (btn.style.display === 'none') {
      btn.style.display = '';
      mRow.style.display = 'none';
    } else {
      btn.style.display = 'none';
      mRow.style.display = 'flex';
    }
  }
}

function openPreviewDetails() {
  if (currentPreviewId) {
    openDetails(currentPreviewId);
  }
}

function openDetails(id, slug = null) {
  if (!slug && typeof apartmentsData !== 'undefined' && apartmentsData[id]) {
    slug = apartmentsData[id].slug;
  }
  window.location.href = '/listings/' + id + (slug ? '-' + slug : '');
}

// Custom GPS Geolocation Button Trigger
let userLocationPlacemark = null;

function triggerMapGeolocation(event) {
  if (event) event.stopPropagation();

  if (!myMap) {
    showToast('❌ Карта еще не загружена');
    return;
  }

  showToast('📍 Определение местоположения...');

  function removeUserPlacemark() {
    if (userLocationPlacemark && myMap) {
      try {
        myMap.geoObjects.remove(userLocationPlacemark);
      } catch (e) {}
      userLocationPlacemark = null;
    }
  }

  function setUserPosition(lat, lng, isApproximate) {
    removeUserPlacemark();

    myMap.setCenter([lat, lng], isApproximate ? 13 : 15, {
      duration: 400
    });

    userLocationPlacemark = new ymaps.Placemark([lat, lng], {
      hintContent: isApproximate ? 'Ваше примерное местоположение (по IP)' : 'Вы здесь',
      balloonContentHeader: isApproximate ? 'Примерное местоположение' : 'Вы здесь',
      balloonContentBody: isApproximate ? 'Определено по IP-адресу' : 'Точное местоположение (GPS)'
    }, {
      preset: isApproximate ? 'islands#blueCircleIcon' : 'islands#geolocationIcon'
    });

    myMap.geoObjects.add(userLocationPlacemark);

    if (isApproximate) {
      showToast('📍 Определено примерное местоположение (по IP)');
    } else {
      showToast('✅ Местоположение определено');
    }
  }

  function fallbackToYandexIP() {
    if (typeof ymaps !== 'undefined' && ymaps.geolocation) {
      ymaps.geolocation.get({
        provider: 'yandex',
        mapStateAutoApply: false
      }).then(function(result) {
        if (result && result.geoObjects && result.geoObjects.get(0)) {
          const coords = result.geoObjects.get(0).geometry.getCoordinates();
          if (coords && coords.length === 2) {
            setUserPosition(coords[0], coords[1], true);
            return;
          }
        }
        showToast('❌ Ошибка геолокации. Проверьте разрешения.');
      }, function(err) {
        console.warn("Yandex geolocation fallback error:", err);
        showToast('❌ Ошибка геолокации. Проверьте разрешения.');
      });
    } else {
      showToast('❌ Ошибка геолокации. Проверьте разрешения.');
    }
  }

  if (navigator.geolocation && (window.isSecureContext !== false)) {
    navigator.geolocation.getCurrentPosition(
      function(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        setUserPosition(lat, lng, false);
      },
      function(error) {
        console.warn("Browser Geolocation error:", error);
        fallbackToYandexIP();
      },
      {
        enableHighAccuracy: true,
        timeout: 7000,
        maximumAge: 60000
      }
    );
  } else {
    fallbackToYandexIP();
  }
}

// === SEARCH IN MAP AREA HELPERS ===
window.isMapBoundsFilterActive = false;
window.isProgrammaticMapMove = false;

function triggerAreaSearch(event) {
  if (event) event.stopPropagation();
  if (!myMap) return;

  window.isMapBoundsFilterActive = true;
  applyFilters(true, false, true);

  const areaBtn = document.getElementById('map-search-area-btn');
  const resetBtn = document.getElementById('map-reset-area-btn');

  if (areaBtn) {
    areaBtn.classList.add('active-area');
    areaBtn.querySelector('span').textContent = 'Область обновлена ✓';
  }
  if (resetBtn) {
    resetBtn.style.display = 'flex';
  }

  showToast('🔍 Варианты отфильтрованы по области карты');
}

function resetAreaSearch(event, triggerApply = true) {
  if (event) event.stopPropagation();

  window.isMapBoundsFilterActive = false;

  const areaBox = document.getElementById('map-search-area-box');
  const areaBtn = document.getElementById('map-search-area-btn');
  const resetBtn = document.getElementById('map-reset-area-btn');

  if (areaBox) areaBox.style.display = 'none';
  if (areaBtn) {
    areaBtn.classList.remove('active-area');
    areaBtn.querySelector('span').textContent = 'Искать в этой области';
  }
  if (resetBtn) resetBtn.style.display = 'none';

  if (triggerApply) {
    window.isProgrammaticMapMove = true;
    applyFilters(true, false, false);
    showToast('🗺️ Фильтр области сброшен');
  }
}

// === FREEHAND DRAWING TOOL HELPERS ===
window.isDrawingMode = false;
window.isPolygonFilterActive = false;
window.activeDrawnPolygonCoords = null;

var drawnPolygonObject = null;
var activePolylineObject = null;
var isMouseDownDrawing = false;
var currentDrawingPoints = [];

function toggleDrawMode(event) {
  if (event) event.stopPropagation();
  if (!myMap) return;

  if (window.isDrawingMode) {
    cancelDrawMode(event);
  } else {
    startDrawMode();
  }
}

function startDrawMode() {
  window.isDrawingMode = true;
  
  const drawBtn = document.getElementById('map-draw-btn');
  const hintEl = document.getElementById('map-draw-hint');
  const searchAreaBox = document.getElementById('map-search-area-box');

  if (drawBtn) drawBtn.classList.add('drawing-active');
  if (hintEl) hintEl.style.display = 'flex';
  if (searchAreaBox) searchAreaBox.style.display = 'none';

  // Disable map behaviors while drawing
  myMap.behaviors.disable(['drag', 'multiTouch', 'dblClickZoom', 'rightMouseButtonMagnifier']);
  
  // Set map cursor to crosshair
  if (myMap.container && myMap.container.getElement()) {
    myMap.container.getElement().style.cursor = 'crosshair';
  }

  showToast('✏️ Зажмите мышь (или палец) и ведите по карте');
}

function cancelDrawMode(event) {
  if (event) event.stopPropagation();
  window.isDrawingMode = false;
  isMouseDownDrawing = false;

  const drawBtn = document.getElementById('map-draw-btn');
  const hintEl = document.getElementById('map-draw-hint');

  if (drawBtn) drawBtn.classList.remove('drawing-active');
  if (hintEl) hintEl.style.display = 'none';

  if (activePolylineObject && myMap) {
    try { myMap.geoObjects.remove(activePolylineObject); } catch(e){}
    activePolylineObject = null;
  }

  if (myMap) {
    myMap.behaviors.enable(['drag', 'multiTouch', 'dblClickZoom', 'rightMouseButtonMagnifier']);
    if (myMap.container && myMap.container.getElement()) {
      myMap.container.getElement().style.cursor = '';
    }
  }
}

function clearDrawnPolygon(event) {
  if (event) event.stopPropagation();

  window.isPolygonFilterActive = false;
  window.activeDrawnPolygonCoords = null;

  if (drawnPolygonObject && myMap) {
    try { myMap.geoObjects.remove(drawnPolygonObject); } catch(e){}
    drawnPolygonObject = null;
  }

  const polyBar = document.getElementById('map-polygon-bar');
  if (polyBar) polyBar.style.display = 'none';

  cancelDrawMode(event);

  applyFilters(true, false, false);
  showToast('🗺️ Область сброшена');
}

// === QUICK POPULAR FILTERS JS ===
function toggleQfPopup(type, event) {
  if (event) event.stopPropagation();
  
  // Close all other popups
  document.querySelectorAll('.qf-popup').forEach(p => {
    if (p.id !== 'qf-popup-' + type) p.style.display = 'none';
  });
  document.querySelectorAll('.qf-pill').forEach(p => {
    if (p.id !== 'qf-pill-' + type) p.classList.remove('open');
  });
  
  const popup = document.getElementById('qf-popup-' + type);
  const pill = document.getElementById('qf-pill-' + type);
  if (popup && pill) {
    const isShow = (popup.style.display === 'flex');
    if (isShow) {
      popup.style.display = 'none';
      pill.classList.remove('open');
    } else {
      popup.style.display = 'flex';
      pill.classList.add('open');
      requestAnimationFrame(() => {
        positionQfPopup(type);
      });
    }
  }
}

function positionQfPopup(type) {
  const pill = document.getElementById('qf-pill-' + type);
  const popup = document.getElementById('qf-popup-' + type);
  const section = document.querySelector('.filters-section');
  if (pill && popup && section) {
    const pillRect = pill.getBoundingClientRect();
    const sectionRect = section.getBoundingClientRect();
    let leftPos = pillRect.left - sectionRect.left;
    const popupWidth = popup.offsetWidth || 240;
    if (leftPos + popupWidth > sectionRect.width) {
      leftPos = sectionRect.width - popupWidth - 16;
    }
    popup.style.left = Math.max(16, leftPos) + 'px';
  }
}

// Close popups when clicking outside
document.addEventListener('click', function(event) {
  if (!event.target.closest('.qf-pill') && !event.target.closest('.qf-popup')) {
    document.querySelectorAll('.qf-popup').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.qf-pill').forEach(p => p.classList.remove('open'));
  }
});

// Sync Price Filters
function syncPriceFilters() {
  const minVal = document.getElementById('qf-price-min').value;
  const maxVal = document.getElementById('qf-price-max').value;
  
  const modalMin = document.getElementById('price-min');
  const modalMax = document.getElementById('price-max');
  
  if (modalMin) modalMin.value = minVal;
  if (modalMax) modalMax.value = maxVal;
  
  // Update price ranges if slider is initialized
  const rangeMin = document.getElementById('price-range-min');
  const rangeMax = document.getElementById('price-range-max');
  if (rangeMin && minVal !== '') rangeMin.value = minVal;
  if (rangeMax && maxVal !== '') rangeMax.value = maxVal;
  if (typeof updateSliderTrack === 'function') updateSliderTrack();
  
  const pill = document.getElementById('qf-pill-price');
  if (minVal || maxVal) {
    pill.classList.add('active');
  } else {
    pill.classList.remove('active');
  }
}

function applyQfPrice(event) {
  if (event) event.stopPropagation();
  document.getElementById('qf-popup-price').style.display = 'none';
  document.getElementById('qf-pill-price').classList.remove('open');
  applyFilters();
}

// Sync Guests Filters
function changeQfGuests(val, event) {
  if (event) event.stopPropagation();
  
  if (selectedGuests === null) {
    selectedGuests = 1;
  } else {
    selectedGuests += val;
    if (selectedGuests < 1) selectedGuests = null;
    if (selectedGuests > 10) selectedGuests = 10;
  }
  
  const countText = selectedGuests === null ? 'Любое' : selectedGuests + '+';
  document.getElementById('qf-guests-count').textContent = countText;
  
  // Sync filter modal
  const modalVal = document.getElementById('guests-filter-val');
  if (modalVal) modalVal.textContent = countText;
  
  // Sync top search bar
  const labelText = selectedGuests === null ? 'Добавить' : selectedGuests + ' ' + getGuestsPlural(selectedGuests);
  const searchGuestsLabel = document.getElementById('search-guests-label');
  const searchGuestsVal = document.getElementById('search-guests-val');
  if (searchGuestsLabel) searchGuestsLabel.textContent = labelText;
  if (searchGuestsVal) searchGuestsVal.textContent = countText;
  
  // Update global variable in layouts/app.blade.php
  if (typeof searchGuestsCount !== 'undefined') {
    searchGuestsCount = selectedGuests;
  }
  
  const pill = document.getElementById('qf-pill-guests');
  if (selectedGuests !== null) {
    pill.classList.add('active');
  } else {
    pill.classList.remove('active');
  }
}

function applyQfGuests(event) {
  if (event) event.stopPropagation();
  document.getElementById('qf-popup-guests').style.display = 'none';
  document.getElementById('qf-pill-guests').classList.remove('open');
  applyFilters();
}

// Sync Bedrooms Filters
function selectQfBedrooms(val, btn, event) {
  if (event) event.stopPropagation();
  
  filterBedrooms = val;
  
  const parent = btn.parentElement;
  parent.querySelectorAll('.qf-room-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  
  // Sync filter modal bedrooms buttons
  const modalRow = document.getElementById('row-bedrooms');
  if (modalRow) {
    modalRow.querySelectorAll('.rb-btn').forEach(b => {
      const onclickAttr = b.getAttribute('onclick');
      if (onclickAttr && onclickAttr.includes("'" + 'bedrooms' + "', " + val)) {
        b.classList.add('active');
      } else {
        b.classList.remove('active');
      }
    });
  }
  
  const pill = document.getElementById('qf-pill-bedrooms');
  if (val > 0) {
    pill.classList.add('active');
  } else {
    pill.classList.remove('active');
  }
  
  applyFilters();
}

// Toggle Tag Filter
function toggleQfTag(tagCode) {
  let checkbox = null;
  document.querySelectorAll('.filter-checkbox-item').forEach(cb => {
    if (cb.getAttribute('data-tag') === tagCode) {
      checkbox = cb;
    }
  });
  
  if (checkbox) {
    checkbox.checked = !checkbox.checked;
    
    // Update tag active class
    const tagEl = document.querySelector(`.qf-tag[data-tag="${tagCode}"]`);
    if (tagEl) {
      if (checkbox.checked) {
        tagEl.classList.add('active');
      } else {
        tagEl.classList.remove('active');
      }
    }
    
    applyFilters();
  }
}

// Synchronization function to update popular filters tag state
function syncQfTags() {
  document.querySelectorAll('.qf-tag').forEach(tagEl => {
    const tagCode = tagEl.getAttribute('data-tag');
    let isChecked = false;
    document.querySelectorAll('.filter-checkbox-item').forEach(cb => {
      if (cb.getAttribute('data-tag') === tagCode && cb.checked) {
        isChecked = true;
      }
    });
    if (isChecked) {
      tagEl.classList.add('active');
    } else {
      tagEl.classList.remove('active');
    }
  });
}

function getPluralVariants(n) {
  let plural = 'вариантов';
  const mod10 = n % 10;
  const mod100 = n % 100;
  if (mod10 === 1 && mod100 !== 11) {
    plural = 'вариант';
  } else if (mod10 >= 2 && mod10 <= 4 && (mod100 < 10 || mod100 >= 20)) {
    plural = 'варианта';
  }
  return n + ' ' + plural;
}

function toggleFav(btn){
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
    if (document.body.classList.contains('showing-only-favorites')) {
      applyFilters();
    }
  }
}
function callPhone(num){
  event.stopPropagation();
  showToast('📞 Звоним: ' + num);
}
function showMore(){
  showToast('Загружаем ещё объявления...');
}

// Фильтрация по городам

function setCityFilter(city) {
  if (typeof resetAreaSearch === 'function') {
    resetAreaSearch(null, false);
  }
  activeCity = (city === 'Вся Беларусь') ? null : city;
  applyFilters();
}

function setGuestsFilter(count) {
  selectedGuests = count;
  // Синхронизируем счетчик гостей внутри модального окна фильтров
  const modalValEl = document.getElementById('guests-filter-val');
  if (modalValEl) modalValEl.textContent = count === null ? 'Любое' : count + '+';
  applyFilters();
}

// === Инициализация и синхронизация слайдера цен ===
function initPriceSlider() {
  const cards = document.querySelectorAll('.card');
  let maxPrice = 300; // Дефолтный максимум
  cards.forEach(c => {
    const p = parseFloat(c.getAttribute('data-price')) || 0;
    if (p > maxPrice) {
      maxPrice = Math.ceil(p);
    }
  });

  const rangeMin = document.getElementById('price-range-min');
  const rangeMax = document.getElementById('price-range-max');
  const inputMin = document.getElementById('price-min');
  const inputMax = document.getElementById('price-max');
  
  if (rangeMin && rangeMax && inputMin && inputMax) {
    rangeMin.max = maxPrice;
    rangeMax.max = maxPrice;
    
    // Начальные значения из инпутов или дефолтные
    const minVal = parseFloat(inputMin.value) || 0;
    const maxVal = parseFloat(inputMax.value) || maxPrice;
    
    rangeMin.value = minVal;
    rangeMax.value = maxVal;
    
    inputMin.placeholder = "0";
    inputMax.placeholder = maxPrice;
    
    updateSliderTrack();

    // Слушатели для слайдеров
    rangeMin.addEventListener('input', () => {
      if (parseInt(rangeMin.value) > parseInt(rangeMax.value)) {
        rangeMin.value = rangeMax.value;
      }
      syncInputsToSlider();
    });

    rangeMax.addEventListener('input', () => {
      if (parseInt(rangeMax.value) < parseInt(rangeMin.value)) {
        rangeMax.value = rangeMin.value;
      }
      syncInputsToSlider();
    });

    // Слушатели для полей ввода
    inputMin.addEventListener('change', syncSliderToInputs);
    inputMax.addEventListener('change', syncSliderToInputs);
  }
}

function updateSliderTrack() {
  const rangeMin = document.getElementById('price-range-min');
  const rangeMax = document.getElementById('price-range-max');
  const track = document.getElementById('price-slider-track');
  if (rangeMin && rangeMax && track) {
    const minVal = parseInt(rangeMin.value);
    const maxVal = parseInt(rangeMax.value);
    const minPercent = (minVal / rangeMin.max) * 100;
    const maxPercent = (maxVal / rangeMax.max) * 100;
    track.style.left = minPercent + '%';
    track.style.width = (maxPercent - minPercent) + '%';
  }
}

function syncSliderToInputs() {
  const rangeMin = document.getElementById('price-range-min');
  const rangeMax = document.getElementById('price-range-max');
  const inputMin = document.getElementById('price-min');
  const inputMax = document.getElementById('price-max');
  if (rangeMin && rangeMax && inputMin && inputMax) {
    const minVal = parseInt(inputMin.value) || 0;
    const maxVal = parseInt(inputMax.value) || parseInt(rangeMax.max);
    rangeMin.value = Math.min(minVal, rangeMin.max);
    rangeMax.value = Math.max(Math.min(maxVal, rangeMax.max), rangeMin.value);
    updateSliderTrack();
  }
}

function syncInputsToSlider() {
  const rangeMin = document.getElementById('price-range-min');
  const rangeMax = document.getElementById('price-range-max');
  const inputMin = document.getElementById('price-min');
  const inputMax = document.getElementById('price-max');
  if (rangeMin && rangeMax && inputMin && inputMax) {
    inputMin.value = rangeMin.value;
    inputMax.value = rangeMax.value;
    updateSliderTrack();
  }
}

// Связываем hover карточек со списком и обрабатываем URL параметры
document.addEventListener('DOMContentLoaded', function() {
  initPriceSlider();

  // Sync quick price input listeners
  const modalMinInput = document.getElementById('price-min');
  const modalMaxInput = document.getElementById('price-max');
  if (modalMinInput && modalMaxInput) {
    modalMinInput.addEventListener('input', function() {
      const qfMin = document.getElementById('qf-price-min');
      if (qfMin) qfMin.value = this.value;
      const pill = document.getElementById('qf-pill-price');
      if (this.value || modalMaxInput.value) pill.classList.add('active');
      else pill.classList.remove('active');
    });
    modalMaxInput.addEventListener('input', function() {
      const qfMax = document.getElementById('qf-price-max');
      if (qfMax) qfMax.value = this.value;
      const pill = document.getElementById('qf-pill-price');
      if (this.value || modalMinInput.value) pill.classList.add('active');
      else pill.classList.remove('active');
    });
  }
  const favs = getFavorites();
  document.querySelectorAll('.card').forEach(function(card) {
    var id = parseInt(card.getAttribute('data-id'), 10);
    if (favs.includes(id)) {
      const favBtn = card.querySelector('.fav-btn');
      if (favBtn) favBtn.classList.add('active');
    }
    
    card.addEventListener('mouseenter', function() {
      highlightMarker(id);
    });
    card.addEventListener('mouseleave', function() {
      unhighlightMarker(id);
    });
  });

  // Первичный пересчет валют под active-currency
  updateCurrencyDisplay();

  // Проверяем наличие параметра избранного в URL
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('favorites') === '1') {
    document.body.classList.add('showing-only-favorites');
    showToast('❤️ Показываем только избранное');
  }

  // Поддержка переходов из поисковой строки на других страницах
  
  @if(isset($selectedCity))
    const searchWhereLabel = document.getElementById('search-where-label');
    if (searchWhereLabel) searchWhereLabel.textContent = "{{ $selectedCity }}";
  @else
    // Парсинг города
    const cityParam = urlParams.get('city');
    if (cityParam) {
      const searchWhereLabel = document.getElementById('search-where-label');
      if (searchWhereLabel) searchWhereLabel.textContent = cityParam;
      activeCity = (cityParam === 'Вся Беларусь') ? null : cityParam;
    }
  @endif


  // Парсинг гостей
  const guestsParam = urlParams.get('guests');
  if (guestsParam) {
    const guestsCount = parseInt(guestsParam);
    if (!isNaN(guestsCount)) {
      searchGuestsCount = guestsCount;
      selectedGuests = guestsCount;
      const labelText = guestsCount + ' ' + getGuestsPlural(guestsCount);
      const countText = guestsCount + '+';
      const searchGuestsLabel = document.getElementById('search-guests-label');
      const searchGuestsVal = document.getElementById('search-guests-val');
      if (searchGuestsLabel) searchGuestsLabel.textContent = labelText;
      if (searchGuestsVal) searchGuestsVal.textContent = countText;
      
      const modalValEl = document.getElementById('guests-filter-val');
      if (modalValEl) modalValEl.textContent = countText;
    }
  }

  // Парсинг типа жилья
  const categorySeo = @json($seoCategory ?? null);
  const typeParam = urlParams.get('type') || (['apartments', 'cottages', 'hourly'].includes(categorySeo) ? categorySeo : null);
  if (typeParam) {
    const typeLabels = {
      'all': 'Любой тип',
      'apartments': 'Квартиры',
      'cottages': 'Коттеджи',
      'hourly': 'Почасово',
      'apartment': 'Квартира',
      'house': 'Дом',
      'cottage': 'Коттедж',
      'agrotourism': 'Агроусадьба'
    };
    const labelText = typeLabels[typeParam];
    if (labelText) {
      const searchTypeLabel = document.getElementById('search-type-label');
      if (searchTypeLabel) searchTypeLabel.textContent = labelText;
    }
    
    // Set active dropdown item active state
    document.querySelectorAll('#search-type-dropdown .search-dropdown-item').forEach(item => {
      if (item.getAttribute('data-type') === typeParam) {
        item.classList.add('active');
      } else {
        item.classList.remove('active');
      }
    });

    // Sync advanced filter tab
    var modalTabType = 'apartments';
    if (typeParam === 'cottages' || typeParam === 'cottage' || typeParam === 'agrotourism' || typeParam === 'house') modalTabType = 'cottages';
    else if (typeParam === 'studio' || typeParam === 'apartments' || typeParam === 'apartment' || typeParam === 'hourly') {
      modalTabType = (typeParam === 'studio') ? 'studio' : 'apartments';
    }
    
    activeObjectType = typeParam;
    document.querySelectorAll('.ot-tab-btn').forEach(btn => {
      if (btn.getAttribute('data-type') === modalTabType) {
        btn.classList.add('active');
      } else {
        btn.classList.remove('active');
      }
    });
    const filterCard = document.getElementById('filter-modal-card');
    if (filterCard) {
      filterCard.setAttribute('data-active-type', modalTabType);
    }

    // Запускаем фильтрацию и синхронизируем с шапкой
    document.querySelectorAll('.nav-cat-item').forEach(item => {
      const href = item.getAttribute('href') || '';
      if (href.endsWith('/' + typeParam) || href.includes('type=' + typeParam)) {
        item.classList.add('active');
      } else {
        item.classList.remove('active');
      }
    });
    activeQuickCategory = typeParam;
  }

  // Парсинг комнат
  const roomsParam = urlParams.get('rooms');
  if (roomsParam) {
    const roomsCount = parseInt(roomsParam);
    if (!isNaN(roomsCount)) {
      filterBedrooms = roomsCount;
      searchRooms = roomsCount;
      const labelText = roomsCount === 0 ? 'Любое' : (roomsCount === 4 ? '4+ комнат' : roomsCount + ' ' + (roomsCount === 1 ? 'комната' : 'комнаты'));
      const searchRoomsLabel = document.getElementById('search-rooms-label');
      if (searchRoomsLabel) searchRoomsLabel.textContent = labelText;
      
      // Update active state in search bar dropdown
      document.querySelectorAll('#search-rooms-dropdown .search-dropdown-item').forEach(item => {
        if (parseInt(item.getAttribute('data-rooms')) === roomsCount || (roomsCount === 0 && item.getAttribute('data-rooms') === 'all')) {
          item.classList.add('active');
        } else {
          item.classList.remove('active');
        }
      });

      // Update active state in modal buttons
      const btn = document.querySelector(`.filter-room-btn[data-val="${roomsCount}"]`);
      if (btn) {
        document.querySelectorAll('.filter-room-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
      } else if (roomsCount === 0) {
        const anyBtn = document.querySelector('.filter-room-btn[data-val="0"]');
        if (anyBtn) {
          document.querySelectorAll('.filter-room-btn').forEach(b => b.classList.remove('active'));
          anyBtn.classList.add('active');
        }
      }

      // Update active state in quick filter buttons
      const qfBtn = document.querySelector(`.qf-room-btn[data-val="${roomsCount}"]`);
      if (qfBtn) {
        document.querySelectorAll('.qf-room-btn').forEach(b => b.classList.remove('active'));
        qfBtn.classList.add('active');
        const qfText = document.querySelector('#qf-pill-bedrooms span');
        if (qfText) {
          qfText.textContent = roomsCount === 0 ? 'Любое' : roomsCount + (roomsCount === 1 ? ' спальня' : (roomsCount <= 4 ? ' спальни' : ' спален'));
        }
        const qfPill = document.getElementById('qf-pill-bedrooms');
        if (qfPill) {
          if (roomsCount > 0) qfPill.classList.add('active');
          else qfPill.classList.remove('active');
        }
      }
    }
  }

  // Парсинг цен
  const priceMinParam = urlParams.get('price_min');
  const priceMaxParam = urlParams.get('price_max');
  if (priceMinParam || priceMaxParam) {
    searchPriceMin = priceMinParam || '';
    searchPriceMax = priceMaxParam || '';
    
    const minInput = document.getElementById('search-price-min');
    const maxInput = document.getElementById('search-price-max');
    if (minInput && priceMinParam) minInput.value = priceMinParam;
    if (maxInput && priceMaxParam) maxInput.value = priceMaxParam;
    
    // Set sidebar values if they exist
    const sidebarMin = document.getElementById('price-min');
    const sidebarMax = document.getElementById('price-max');
    if (sidebarMin && priceMinParam) sidebarMin.value = priceMinParam;
    if (sidebarMax && priceMaxParam) sidebarMax.value = priceMaxParam;
    
    let label = 'Любая';
    if (priceMinParam && priceMaxParam) {
      label = priceMinParam + ' - ' + priceMaxParam + ' Б̶';
    } else if (priceMinParam) {
      label = 'от ' + priceMinParam + ' Б̶';
    } else if (priceMaxParam) {
      label = 'до ' + priceMaxParam + ' Б̶';
    }
    const searchPriceLabel = document.getElementById('search-price-label');
    if (searchPriceLabel) searchPriceLabel.textContent = label;
  }

  if (urlParams.get('open_filters') === '1') {
    if (typeof openFilterModal === 'function') {
      openFilterModal();
    }
  }

  applyFilters();
});

// Advanced Filter Logic

function selectObjectType(type, event) {
  if (event) event.preventDefault();
  activeObjectType = type;
  
  // Update tabs active state
  document.querySelectorAll('.ot-tab-btn').forEach(btn => {
    if (btn.getAttribute('data-type') === type) {
      btn.classList.add('active');
    } else {
      btn.classList.remove('active');
    }
  });

  // Set attribute on modal card for CSS visibility toggle
  const card = document.getElementById('filter-modal-card');
  if (card) {
    card.setAttribute('data-active-type', type);
  }
}

function setBanquetFilter(capacity, btn) {
  filterBanquet = capacity;
  const parent = document.getElementById('row-banquet');
  if (parent) {
    parent.querySelectorAll('.rb-btn').forEach(b => b.classList.remove('active'));
  }
  btn.classList.add('active');
}

function setBathroomFilter(value, btn) {
  filterBathroomType = value;
  const parent = document.getElementById('row-bathroom-type');
  if (parent) {
    parent.querySelectorAll('.rb-btn').forEach(b => b.classList.remove('active'));
  }
  btn.classList.add('active');
}

function setBalconyFilter(value, btn) {
  filterBalconyType = value;
  const parent = document.getElementById('row-balcony-type');
  if (parent) {
    parent.querySelectorAll('.rb-btn').forEach(b => b.classList.remove('active'));
  }
  btn.classList.add('active');
}

function openFilterModal() {
  document.getElementById('filter-modal').classList.add('show');
}

function closeFilterModal() {
  document.getElementById('filter-modal').classList.remove('show');
}

function openTariffsModal() {
  const modal = document.getElementById('tariffs-modal');
  if (modal) {
    modal.classList.add('show');
  }
}

function closeTariffsModal() {
  const modal = document.getElementById('tariffs-modal');
  if (modal) {
    modal.classList.remove('show');
  }
}

function goToCreateListing() {
  window.location.href = "{{ route('listings.create') }}";
}

function changeGuestsFilter(val) {
  if (selectedGuests === null) {
    selectedGuests = 1;
  } else {
    selectedGuests += val;
    if (selectedGuests < 1) selectedGuests = null;
    if (selectedGuests > 10) selectedGuests = 10;
  }
  document.getElementById('guests-filter-val').textContent = selectedGuests === null ? 'Любое' : selectedGuests + '+';
  
  // Синхронизируем верхнюю поисковую строку
  const labelText = selectedGuests === null ? 'Добавить' : selectedGuests + ' ' + getGuestsPlural(selectedGuests);
  const countText = selectedGuests === null ? 'Любое' : selectedGuests + '+';
  
  const searchGuestsLabel = document.getElementById('search-guests-label');
  const searchGuestsVal = document.getElementById('search-guests-val');
  if (searchGuestsLabel) searchGuestsLabel.textContent = labelText;
  if (searchGuestsVal) searchGuestsVal.textContent = countText;
  
  // Обновляем глобальную переменную в layouts/app.blade.php
  searchGuestsCount = selectedGuests;
}

function setRoomFilter(type, val, btn) {
  // Установка состояния
  if (type === 'bedrooms') {
    filterBedrooms = val;
    searchRooms = val;
    
    // Update top search bar label and active state
    const labelText = val === 0 ? 'Любое' : (val === 4 ? '4+ комнат' : val + ' ' + (val === 1 ? 'комната' : 'комнаты'));
    const searchRoomsLabel = document.getElementById('search-rooms-label');
    if (searchRoomsLabel) searchRoomsLabel.textContent = labelText;
    
    document.querySelectorAll('#search-rooms-dropdown .search-dropdown-item').forEach(item => {
      const itemRooms = item.getAttribute('data-rooms');
      if ((val === 0 && itemRooms === 'all') || parseInt(itemRooms) === val) {
        item.classList.add('active');
      } else {
        item.classList.remove('active');
      }
    });

    // Update quick filter buttons (qf-room-btn) too
    const qfBtn = document.querySelector(`.qf-room-btn[data-val="${val}"]`);
    if (qfBtn) {
      document.querySelectorAll('.qf-room-btn').forEach(b => b.classList.remove('active'));
      qfBtn.classList.add('active');
      const qfText = document.querySelector('#qf-pill-bedrooms span');
      if (qfText) {
        qfText.textContent = val === 0 ? 'Любое' : val + (val === 1 ? ' спальня' : (val <= 4 ? ' спальни' : ' спален'));
      }
      const qfPill = document.getElementById('qf-pill-bedrooms');
      if (qfPill) {
        if (val > 0) qfPill.classList.add('active');
        else qfPill.classList.remove('active');
      }
    }
  } else if (type === 'beds') {
    filterBeds = val;
  } else if (type === 'bathrooms') {
    filterBathrooms = val;
  }

  // Переключение активного класса у кнопок
  const parent = btn.parentElement;
  if (parent) {
    parent.querySelectorAll('.rb-btn').forEach(b => b.classList.remove('active'));
  }
  btn.classList.add('active');
}

var currentPage = {{ $currentPage ?? 1 }};
const itemsPerPage = 20;

function resetAdvancedFilters() {
  selectedGuests = null;
  filterBedrooms = 0;
  filterBeds = 0;
  filterBathrooms = 0;
  activeObjectType = 'apartments';
  filterBanquet = 0;
  filterBathroomType = '';
  filterBalconyType = '';
  
  document.getElementById('price-min').value = '';
  document.getElementById('price-max').value = '';

  // Сброс слайдера цен
  const rangeMin = document.getElementById('price-range-min');
  const rangeMax = document.getElementById('price-range-max');
  if (rangeMin && rangeMax) {
    rangeMin.value = rangeMin.min;
    rangeMax.value = rangeMax.max;
    updateSliderTrack();
  }
  
  document.querySelectorAll('.filter-checkbox-item').forEach(cb => cb.checked = false);
  document.querySelectorAll('.filter-bed-type-item').forEach(cb => cb.checked = false);
  
  if (document.getElementById('pay-cash')) document.getElementById('pay-cash').checked = false;
  if (document.getElementById('pay-card')) document.getElementById('pay-card').checked = false;
  if (document.getElementById('pay-erip')) document.getElementById('pay-erip').checked = false;
  if (document.getElementById('pay-invoice')) document.getElementById('pay-invoice').checked = false;
  if (document.getElementById('apt-not-first')) document.getElementById('apt-not-first').checked = false;
  if (document.getElementById('apt-not-last')) document.getElementById('apt-not-last').checked = false;
  if (document.getElementById('cot-parking-type')) document.getElementById('cot-parking-type').value = '';
  
  document.querySelectorAll('.filter-row').forEach(row => {
    row.querySelectorAll('.rb-btn').forEach((btn, idx) => {
      if (idx === 0) btn.classList.add('active');
      else btn.classList.remove('active');
    });
  });
  
  selectObjectType('apartments');
  
  const rowBanquet = document.getElementById('row-banquet');
  if (rowBanquet) {
    rowBanquet.querySelectorAll('.rb-btn').forEach((btn, idx) => {
      if (idx === 0) btn.classList.add('active');
      else btn.classList.remove('active');
    });
  }
  
  const rowBathroomType = document.getElementById('row-bathroom-type');
  if (rowBathroomType) {
    rowBathroomType.querySelectorAll('.rb-btn').forEach(btn => btn.classList.remove('active'));
  }
  
  const rowBalconyType = document.getElementById('row-balcony-type');
  if (rowBalconyType) {
    rowBalconyType.querySelectorAll('.rb-btn').forEach(btn => btn.classList.remove('active'));
  }

  const modalValEl = document.getElementById('guests-filter-val');
  if (modalValEl) modalValEl.textContent = 'Любое';
  
  const searchGuestsLabel = document.getElementById('search-guests-label');
  const searchGuestsVal = document.getElementById('search-guests-val');
  if (searchGuestsLabel) searchGuestsLabel.textContent = 'Добавить';
  if (searchGuestsVal) searchGuestsVal.textContent = 'Любое';

  // Reset search rooms state
  searchRooms = 0;
  const searchRoomsLabel = document.getElementById('search-rooms-label');
  if (searchRoomsLabel) searchRoomsLabel.textContent = 'Любое';
  document.querySelectorAll('#search-rooms-dropdown .search-dropdown-item').forEach(item => {
    if (item.getAttribute('data-rooms') === 'all') {
      item.classList.add('active');
    } else {
      item.classList.remove('active');
    }
  });

  // Reset quick filter room buttons and pill
  const qfText = document.querySelector('#qf-pill-bedrooms span');
  if (qfText) {
    qfText.textContent = 'Любое';
  }
  const qfPill = document.getElementById('qf-pill-bedrooms');
  if (qfPill) {
    qfPill.classList.remove('active');
  }
  document.querySelectorAll('.qf-room-btn').forEach(b => {
    if (b.getAttribute('data-val') === '0') b.classList.add('active');
    else b.classList.remove('active');
  });
  
  applyFilters(true, true);
}

function applyAdvancedFiltersAndClose() {
  applyFilters(true, true);
  closeFilterModal();
}

function applyFilters(resetPage = true, fitMapBounds = false, checkMapBounds = false) {
  // Update visibility of quick tags based on active category
  const childTag = document.getElementById('qf-tag-children');
  const saunaTag = document.getElementById('qf-tag-sauna');
  const poolTag = document.getElementById('qf-tag-pool');
  if (childTag && saunaTag && poolTag) {
    const path = window.location.pathname.toLowerCase();
    const search = window.location.search.toLowerCase();
    let isApts = (activeQuickCategory === 'apartments' || activeQuickCategory === 'hourly');
    
    if (path.endsWith('/apartments') || path.endsWith('/hourly') || 
        path.includes('/apartments/') || path.includes('/hourly/') ||
        search.includes('type=apartments') || search.includes('type=hourly')) {
      isApts = true;
    }
    
    const aptSlugs = [
      'dzhakuzi', 'jacuzzi', 'odnokomnatnaya', '1-room', 
      'dvuhkomnatnaya', '2-room', 'trehkomnatnaya', '3-room', 
      'chetyrehkomnatnaya', '4-room', 'vokzal', 'near-station', 
      'metro', 'near-metro'
    ];
    for (const slug of aptSlugs) {
      if (path.endsWith('/' + slug) || path.includes('/' + slug + '/')) {
        isApts = true;
      }
    }
    
    const activeHeaderItem = document.querySelector('.nav-cat-item.active');
    if (activeHeaderItem) {
      const text = activeHeaderItem.textContent.trim().toLowerCase();
      if (text.includes('квартиры') || text.includes('почасово')) {
        isApts = true;
      }
    }

    if (isApts) {
      childTag.style.display = 'inline-block';
      saunaTag.style.display = 'none';
      poolTag.style.display = 'none';
    } else {
      childTag.style.display = 'none';
      saunaTag.style.display = 'inline-block';
      poolTag.style.display = 'inline-block';
    }
  }

  if (typeof syncQfTags === 'function') {
    syncQfTags();
  }
  if (resetPage) {
    currentPage = 1;
  }

  const currentCurrency = localStorage.getItem('flatly_currency') || 'BYN';
  const rate = EXCHANGE_RATES[currentCurrency] || 1.0;

  var priceMin = parseFloat(document.getElementById('price-min').value) || 0;
  var priceMax = parseFloat(document.getElementById('price-max').value) || 999999;

  // Считываем выбранные типы кроватей
  var checkedBedTypes = [];
  document.querySelectorAll('.filter-bed-type-item:checked').forEach(function(checkbox) {
    checkedBedTypes.push(checkbox.value.toLowerCase().trim());
  });

  // Считываем динамические чекбоксы удобств и правил
  var checkedTags = [];
  document.querySelectorAll('.filter-checkbox-item:checked').forEach(function(checkbox) {
    var isAptOnly = checkbox.closest('.apt-only');
    var isCottageOnly = checkbox.closest('.cottage-only');
    
    // Пропускаем чекбоксы из скрытых секций в зависимости от выбранного типа объекта
    if (isAptOnly && activeObjectType === 'cottages') {
      return;
    }
    if (isCottageOnly && (activeObjectType === 'apartments' || activeObjectType === 'studio')) {
      return;
    }
    
    var tag = checkbox.getAttribute('data-tag');
    if (tag) {
      checkedTags.push(tag.toLowerCase().trim());
    }
  });

  // Считываем способы оплаты
  var needCash = document.getElementById('pay-cash') ? document.getElementById('pay-cash').checked : false;
  var needCard = document.getElementById('pay-card') ? document.getElementById('pay-card').checked : false;
  var needErip = document.getElementById('pay-erip') ? document.getElementById('pay-erip').checked : false;
  var needInvoice = document.getElementById('pay-invoice') ? document.getElementById('pay-invoice').checked : false;

  // Считываем этажи
  var needNotFirst = document.getElementById('apt-not-first') ? document.getElementById('apt-not-first').checked : false;
  var needNotLast = document.getElementById('apt-not-last') ? document.getElementById('apt-not-last').checked : false;

  // Считываем тип парковки для коттеджей
  var selectedCotParking = document.getElementById('cot-parking-type') ? document.getElementById('cot-parking-type').value : '';

  var checkBounds = checkMapBounds || (typeof window.isMapBoundsFilterActive !== 'undefined' && window.isMapBoundsFilterActive);
  var bounds = (checkBounds && myMap) ? myMap.getBounds() : null;

  var checkPolygon = (typeof window.isPolygonFilterActive !== 'undefined' && window.isPolygonFilterActive && window.activeDrawnPolygonCoords);

  var cards = document.querySelectorAll('.card');
  var matchingCards = [];
  var matchingIds = new Set();

  cards.forEach(function(card) {
    var cat = card.getAttribute('data-category');
    var basePrice = parseFloat(card.getAttribute('data-price')) || 0;
    var price = basePrice * rate; // пересчет цены карты для соответствия выбранному диапазону
    
    var guests = parseInt(card.getAttribute('data-guests')) || 0;
    
    // Считываем спальни, кровати, ванные, банкетный зал
    var cardBedrooms = parseInt(card.getAttribute('data-bedrooms')) || 0;
    var cardBeds = parseInt(card.getAttribute('data-beds')) || 0;
    var cardBathrooms = parseInt(card.getAttribute('data-bathrooms')) || 0;
    var cardBanquet = parseInt(card.getAttribute('data-banquet')) || 0;

    var cardBalcony = card.getAttribute('data-balcony') === '1';
    var cardBalconyType = card.getAttribute('data-balcony-type') || 'none';
    var cardFloor = parseInt(card.getAttribute('data-floor')) || null;
    var cardTotalFloors = parseInt(card.getAttribute('data-total-floors')) || null;
    var cardParkingType = card.getAttribute('data-parking-type') || '';
    var cardLayout = card.getAttribute('data-layout') || '';

    var amsStr = card.getAttribute('data-amenities') || '';
    var ams = amsStr ? amsStr.split(',').map(s => s.toLowerCase().trim()) : [];

    var cardBedTypesStr = card.getAttribute('data-bed-types') || '';
    var cardBedTypes = cardBedTypesStr ? cardBedTypesStr.split(',').map(s => s.toLowerCase().trim()) : [];

    // Фильтр по активному типу объекта
    var typeMatch = true;
    if (activeObjectType !== 'all') {
      var cardType = card.getAttribute('data-type') || '';
      var cardTitle = card.getAttribute('data-title') || '';
      if (activeObjectType === 'apartments' || activeObjectType === 'apartment') {
        typeMatch = (cat === 'apartments' || cat === 'hourly' || cardType === 'apartment');
      } else if (activeObjectType === 'cottages' || activeObjectType === 'cottage') {
        typeMatch = (cat === 'cottages' && cardType === 'cottage');
      } else if (activeObjectType === 'agrotourism') {
        typeMatch = (cardType === 'agrotourism');
      } else if (activeObjectType === 'house') {
        typeMatch = (cat === 'cottages' && cardType !== 'agrotourism');
      } else if (activeObjectType === 'studio') {
        typeMatch = (cat === 'apartments');
      }
    }

    var priceMatch = price >= priceMin && price <= priceMax;
    var guestsMatch = selectedGuests === null || guests >= selectedGuests;
    
    // Фильтр комнат, кроватей, банкетного зала
    var bedroomsMatch = false;
    if (filterBedrooms === 0) {
      bedroomsMatch = true;
    } else if (filterBedrooms === 4) {
      bedroomsMatch = cardBedrooms >= 4;
    } else {
      bedroomsMatch = cardBedrooms === filterBedrooms;
    }
    var bedsMatch = filterBeds === 0 || cardBeds >= filterBeds;
    var bathroomsMatch = filterBathrooms === 0 || cardBathrooms >= filterBathrooms;
    var banquetMatch = filterBanquet === 0 || cardBanquet >= filterBanquet;

    // Фильтрация параметров квартир
    var bathroomTypeMatch = !filterBathroomType || cardLayout === filterBathroomType;
    
    var balconyTypeMatch = true;
    if (filterBalconyType) {
      if (filterBalconyType === 'balcony') {
        balconyTypeMatch = cardBalcony || cardBalconyType === 'balcony';
      } else {
        balconyTypeMatch = cardBalconyType === filterBalconyType;
      }
    }
    
    var notFirstMatch = !needNotFirst || (cardFloor !== null && cardFloor !== 1);
    var notLastMatch = !needNotLast || (cardFloor !== null && cardTotalFloors !== null && cardFloor !== cardTotalFloors);

    // Фильтрация параметров коттеджей
    var parkingTypeMatch = !selectedCotParking || cardParkingType === selectedCotParking;

    var aptParamsMatch = (activeObjectType === 'apartments' || activeObjectType === 'studio') ? (bathroomTypeMatch && balconyTypeMatch && notFirstMatch && notLastMatch) : true;
    var cotParamsMatch = (activeObjectType === 'cottages') ? parkingTypeMatch : true;

    // Фильтр по городу
    var cardCity = card.getAttribute('data-city') || 'Минск';
    var cityMatch = !activeCity || cardCity.toLowerCase() === activeCity.toLowerCase();

    // Фильтр по быстрой категории
    var quickCategoryMatch = true;
    if (activeQuickCategory !== 'all') {
      if (activeQuickCategory === 'out-of-town') {
        quickCategoryMatch = (cat === 'cottages' || cardCity.toLowerCase() !== 'минск');
      } else {
        quickCategoryMatch = (cat === activeQuickCategory);
      }
    }

    // Фильтр удобств и правил по выбранным тегам (checkedTags)
    var tagsMatch = true;
    for (var i = 0; i < checkedTags.length; i++) {
      var tag = checkedTags[i];
      var found = ams.some(function(am) {
        return am === tag || am.includes(tag) || tag.includes(am);
      });
      if (!found) {
        tagsMatch = false;
        break;
      }
    }

    // Фильтр способов оплаты
    var paymentMatch = true;
    if (needCash || needCard || needErip || needInvoice) {
      paymentMatch = false;
      var cardPayments = card.getAttribute('data-payments') ? card.getAttribute('data-payments').split(',') : [];
      if (needCash && cardPayments.includes('cash')) paymentMatch = true;
      if (needCard && cardPayments.includes('card')) paymentMatch = true;
      if (needErip && cardPayments.includes('erip')) paymentMatch = true;
      if (needInvoice && cardPayments.includes('invoice')) paymentMatch = true;
    }

    // Фильтр по Избранному
    var favMatch = true;
    if (document.body.classList.contains('showing-only-favorites')) {
      var id = parseInt(card.getAttribute('data-id'), 10);
      favMatch = getFavorites().includes(id);
    }

    // Фильтр по типам кроватей
    var bedTypesMatch = true;
    for (var i = 0; i < checkedBedTypes.length; i++) {
      if (!cardBedTypes.includes(checkedBedTypes[i])) {
        bedTypesMatch = false;
        break;
      }
    }

    var filterMatch = quickCategoryMatch &&
      typeMatch && priceMatch && guestsMatch && cityMatch &&
      bedroomsMatch && bedsMatch && bathroomsMatch && banquetMatch &&
      aptParamsMatch && cotParamsMatch &&
      tagsMatch && paymentMatch && favMatch && bedTypesMatch;

    var boundsMatch = true;
    if (checkBounds && bounds) {
      var id = card.getAttribute('data-id');
      var apt = (typeof apartmentsData !== 'undefined') ? apartmentsData[id] : null;
      var cardLat = apt ? apt.coords[0] : parseFloat(card.getAttribute('data-lat'));
      var cardLng = apt ? apt.coords[1] : parseFloat(card.getAttribute('data-lng'));
      if (!isNaN(cardLat) && !isNaN(cardLng)) {
        boundsMatch = (cardLat >= bounds[0][0] && cardLat <= bounds[1][0] &&
                       cardLng >= bounds[0][1] && cardLng <= bounds[1][1]);
      }
    }

    var polygonMatch = true;
    if (checkPolygon && window.activeDrawnPolygonCoords) {
      var id = card.getAttribute('data-id');
      var apt = (typeof apartmentsData !== 'undefined') ? apartmentsData[id] : null;
      var cardLat = apt ? apt.coords[0] : parseFloat(card.getAttribute('data-lat'));
      var cardLng = apt ? apt.coords[1] : parseFloat(card.getAttribute('data-lng'));
      if (!isNaN(cardLat) && !isNaN(cardLng)) {
        polygonMatch = isPointInPolygon([cardLat, cardLng], window.activeDrawnPolygonCoords);
      }
    }

    if (filterMatch) {
      if (boundsMatch && polygonMatch) {
        matchingIds.add(card.getAttribute('data-id')); // ID matching bounds & polygon & filters goes to map & grid
        matchingCards.push(card); // goes to listings grid
      } else {
        card.style.display = 'none';
      }
    } else {
      card.style.display = 'none';
    }
  });

  const visibleCount = matchingCards.length;

  // Обновляем счетчик вариантов на странице
  const countEl = document.getElementById('listings-count');
  if (countEl) {
    countEl.textContent = getPluralVariants(visibleCount);
  }

  // Вычисляем и применяем пагинацию
  const totalPages = Math.ceil(visibleCount / itemsPerPage);
  if (currentPage > totalPages) {
    currentPage = Math.max(1, totalPages);
  }

  const startIdx = (currentPage - 1) * itemsPerPage;
  const endIdx = currentPage * itemsPerPage;

  matchingCards.forEach(function(card, index) {
    if (index >= startIdx && index < endIdx) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });

  // Dynamic visibility of the advertising banner based on active page and filter results count
  const adBanners = document.querySelectorAll('.ad-banner');
  adBanners.forEach(function(banner) {
    const bannerPage = parseInt(banner.getAttribute('data-page') || '1', 10);
    if (visibleCount > 0 && currentPage === bannerPage) {
      banner.style.setProperty('display', 'flex', 'important');
    } else {
      banner.style.setProperty('display', 'none', 'important');
    }
  });

  renderPaginationControls(totalPages);

  // Обновляем маркеры на Яндекс.Картах (через Кластеризатор)
  if (myMap) {
    if (myClusterer) {
      myClusterer.removeAll();
      var matchingPlacemarks = [];
      Object.keys(placemarks).forEach(function(id) {
        if (matchingIds.has(String(id))) {
          matchingPlacemarks.push(placemarks[id]);
        }
      });
      myClusterer.add(matchingPlacemarks);
    } else if (myMap.geoObjects) {
      myMap.geoObjects.removeAll();
      Object.keys(placemarks).forEach(function(id) {
        var placemark = placemarks[id];
        if (matchingIds.has(String(id))) {
          myMap.geoObjects.add(placemark);
        }
      });
    }

    // Автоматическое масштабирование карты (fit bounds) по отфильтрованным маркерам
    if (fitMapBounds && matchingIds.size > 0) {
      var canFitBounds = true;
      if (window.innerWidth <= 991 && !isMobileMapVisible) {
        canFitBounds = false;
      }

      if (canFitBounds) {
        var bounds = myClusterer ? myClusterer.getBounds() : myMap.geoObjects.getBounds();
        if (bounds) {
          if (matchingIds.size === 1) {
            var singlePlacemarkId = Array.from(matchingIds)[0];
            var singlePlacemark = placemarks[singlePlacemarkId];
            if (singlePlacemark) {
              myMap.setCenter(singlePlacemark.geometry.getCoordinates(), 15, {
                checkZoomRange: true,
                duration: 300
              });
            }
          } else {
            myMap.setBounds(bounds, {
              checkZoomRange: true,
              zoomMargin: 40,
              duration: 300
            });
          }
        }
      }
    }
  }
}

function getPageUrl(page) {
  const parsedPage = parseInt(page, 10);
  const pathname = window.location.pathname;
  const search = window.location.search;
  const cleanPath = pathname.replace(/\/page-\d+/, '');
  
  let targetUrl = '';
  if (isNaN(parsedPage) || parsedPage <= 1) {
    targetUrl = cleanPath || '/';
  } else {
    const base = cleanPath.endsWith('/') ? cleanPath.slice(0, -1) : cleanPath;
    targetUrl = base + '/page-' + parsedPage;
  }
  
  return targetUrl + search;
}

function renderPaginationControls(totalPages) {
  const pagContainer = document.getElementById('listings-pagination');
  if (!pagContainer) return;
  
  if (totalPages <= 1) {
    pagContainer.innerHTML = '';
    return;
  }
  
  let html = '';
  
  // Кнопка Назад
  const prevDisabled = currentPage === 1 ? 'disabled' : '';
  const prevUrl = currentPage === 1 ? '#' : getPageUrl(currentPage - 1);
  const prevOnClick = currentPage === 1 ? 'onclick="event.preventDefault()"' : `onclick="changePage(event, ${currentPage - 1})"`;
  html += `<a href="${prevUrl}" class="page-btn ${prevDisabled}" ${prevOnClick} aria-label="Предыдущая страница">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--slate-900)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
  </a>`;
  
  // Кнопки страниц с усечением (truncation)
  let startPage = Math.max(1, currentPage - 2);
  let endPage = Math.min(totalPages, currentPage + 2);
  
  if (currentPage <= 3) {
    endPage = Math.min(totalPages, 5);
  }
  if (currentPage >= totalPages - 2) {
    startPage = Math.max(1, totalPages - 4);
  }
  
  if (startPage > 1) {
    html += `<a href="${getPageUrl(1)}" class="page-btn" onclick="changePage(event, 1)">1</a>`;
    if (startPage > 2) {
      html += `<span class="page-ellipsis">...</span>`;
    }
  }
  
  for (let i = startPage; i <= endPage; i++) {
    const activeClass = i === currentPage ? 'active' : '';
    html += `<a href="${getPageUrl(i)}" class="page-btn ${activeClass}" onclick="changePage(event, ${i})">${i}</a>`;
  }
  
  if (endPage < totalPages) {
    if (endPage < totalPages - 1) {
      html += `<span class="page-ellipsis">...</span>`;
    }
    html += `<a href="${getPageUrl(totalPages)}" class="page-btn" onclick="changePage(event, ${totalPages})">${totalPages}</a>`;
  }
  
  // Кнопка Вперед
  const nextDisabled = currentPage === totalPages ? 'disabled' : '';
  const nextUrl = currentPage === totalPages ? '#' : getPageUrl(currentPage + 1);
  const nextOnClick = currentPage === totalPages ? 'onclick="event.preventDefault()"' : `onclick="changePage(event, ${currentPage + 1})"`;
  html += `<a href="${nextUrl}" class="page-btn ${nextDisabled}" ${nextOnClick} aria-label="Следующая страница">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--slate-900)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
  </a>`;
  
  pagContainer.innerHTML = html;
}

function updateSeoTagsClientSide(page) {
  // 1. Update document.title
  let title = document.title;
  title = title.replace(/\s*—\s*Страница\s*\d+/g, '');
  if (page > 1) {
    title += ` — Страница ${page}`;
  }
  document.title = title;

  // 2. Update H1
  const h1El = document.getElementById('page-main-title');
  if (h1El) {
    let h1Text = h1El.textContent.trim();
    h1Text = h1Text.replace(/\s*—\s*Страница\s*\d+/g, '');
    if (page > 1) {
      h1Text += ` — Страница ${page}`;
    }
    h1El.textContent = h1Text;
  }

  // 3. Update Meta Description
  const metaDesc = document.querySelector('meta[name="description"]');
  if (metaDesc) {
    let descText = metaDesc.getAttribute('content');
    descText = descText.replace(/\s*Страница\s*\d+\s*списка\s*предложений\.*/gi, '');
    descText = descText.replace(/\s*—\s*Страница\s*\d+/gi, '');
    if (page > 1) {
      descText = descText.trim().replace(/\.$/, '') + ` — Страница ${page}.`;
    }
    metaDesc.setAttribute('content', descText);
  }
}

function changePage(event, page) {
  if (event) event.preventDefault();
  currentPage = page;
  
  // Обновляем адресную строку динамически без перезагрузки
  const targetUrl = getPageUrl(page);
  window.history.pushState({ page: page }, '', targetUrl);
  
  applyFilters(false);
  updateSeoTagsClientSide(page);
  
  const listingsEl = document.querySelector('.listings');
  if (listingsEl) {
    listingsEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

// Раскрытие панели контактов на карточке
function toggleCardContact(event, id) {
  event.stopPropagation(); // Предотвращаем переход на страницу деталей при клике на контакты
  
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

var isMobileMapVisible = false;

function toggleMobileMap() {
  const listingsEl = document.querySelector('.listings');
  const mapColEl = document.querySelector('.map-col');
  const toggleBtn = document.getElementById('mobile-map-toggle');
  
  if (!toggleBtn || !listingsEl || !mapColEl) return;
  
  const btnText = toggleBtn.querySelector('.btn-text');
  const mapIcon = toggleBtn.querySelector('.map-icon');
  const listIcon = toggleBtn.querySelector('.list-icon');
  
  isMobileMapVisible = !isMobileMapVisible;
  
  if (isMobileMapVisible) {
    // Показываем карту, скрываем список
    listingsEl.style.display = 'none';
    mapColEl.style.display = 'block';
    
    // Переопределяем стили колонки с картой на мобильных
    mapColEl.classList.add('mobile-active-map');
    
    // Обновляем кнопку
    btnText.textContent = 'Список';
    mapIcon.style.display = 'none';
    listIcon.style.display = 'inline-block';
    
    // После показа карты Яндекса на мобильном нужно триггернуть авто-ресайз, чтобы плитки прогрузились корректно
    if (myMap) {
      myMap.container.fitToViewport();
      
      // Масштабируем карту по видимым отфильтрованным маркерам при открытии карты на мобильном
      var bounds = myClusterer ? myClusterer.getBounds() : myMap.geoObjects.getBounds();
      var visibleCount = myClusterer ? myClusterer.getGeoObjects().length : 0;
      
      if (bounds && visibleCount > 0) {
        if (visibleCount === 1) {
          var singlePlacemark = myClusterer.getGeoObjects()[0];
          if (singlePlacemark) {
            myMap.setCenter(singlePlacemark.geometry.getCoordinates(), 15);
          }
        } else {
          myMap.setBounds(bounds, {
            checkZoomRange: true,
            zoomMargin: 40
          });
        }
      } else {
        myMap.setCenter([53.9006, 27.5590], 8);
      }
    }
  } else {
    // Скрываем карту, показываем список
    listingsEl.style.display = '';
    mapColEl.style.display = '';
    
    mapColEl.classList.remove('mobile-active-map');
    
    // Обновляем кнопку
    btnText.textContent = 'Карта';
    mapIcon.style.display = 'inline-block';
    listIcon.style.display = 'none';
  }
}

// Центрирование карты на выбранном объявлении
function centerMapOnListing(event, lat, lng, id) {
  event.stopPropagation(); // Предотвращаем переход на страницу деталей
  
  // Если это мобильное устройство и карта сейчас скрыта, открываем её
  if (window.innerWidth <= 991 && !isMobileMapVisible) {
    toggleMobileMap();
  }
  
  if (myMap) {
    myMap.setCenter([lat, lng], 16);
    // Показываем превью на карте
    showMapPreview(id);
  }
}

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
  track.style.transform = `translateX(-${targetIndex * 100}%)`;
  
  const dots = carousel.querySelectorAll('.carousel-dot');
  dots.forEach((dot, idx) => {
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

// === HOMEPAGE HERO SEARCH BAR LOGIC ===
var heroGuestsCount = null; // null = Любое

function toggleHeroDropdown(type, event) {
  if (event) event.stopPropagation();
  
  const guestsDropdown = document.getElementById('hero-guests-dropdown');
  
  if (type === 'guests') {
    if (guestsDropdown) {
      const isShow = guestsDropdown.classList.contains('show');
      closeAllHeroDropdowns();
      if (!isShow) guestsDropdown.classList.add('show');
    }
  }
}

function closeAllHeroDropdowns() {
  const guestsDropdown = document.getElementById('hero-guests-dropdown');
  if (guestsDropdown) guestsDropdown.classList.remove('show');
}

// Close hero dropdowns when clicking outside
document.addEventListener('click', function(event) {
  if (!event.target.closest('#hero-seg-guests') && !event.target.closest('#hero-guests-dropdown')) {
    const guestsDropdown = document.getElementById('hero-guests-dropdown');
    if (guestsDropdown) guestsDropdown.classList.remove('show');
  }
});

// City Modal Functions
function openCityModal(event) {
  if (event) event.stopPropagation();
  
  const modal = document.getElementById('city-modal');
  const modalBody = modal ? modal.querySelector('.filter-modal-body') : null;
  if (modal) {
    modal.classList.add('show');
    if (modalBody) modalBody.scrollTop = 0;
    
    // Clear search input and show all items when opening
    const searchInput = document.getElementById('city-modal-search-input');
    if (searchInput) {
      searchInput.value = '';
      filterModalCities();
    }
    
    // Highlight the active city
    const valEl = document.getElementById('hero-city-val');
    const currentCity = valEl ? valEl.textContent.trim() : 'Вся Беларусь';
    
    const norm = s => s.toLowerCase().replace(/ё/g, 'е').trim();
    const targetNorm = norm(currentCity);
    
    document.querySelectorAll('.city-modal-item').forEach(item => {
      if (norm(item.textContent) === targetNorm) {
        item.classList.add('active');
      } else {
        item.classList.remove('active');
      }
    });
  }
}

function closeCityModal() {
  const modal = document.getElementById('city-modal');
  if (modal) modal.classList.remove('show');
}

function filterModalCities() {
  const query = document.getElementById('city-modal-search-input').value.toLowerCase().replace(/ё/g, 'е').trim();
  const items = document.querySelectorAll('.city-modal-all-item');
  const sections = document.querySelectorAll('.city-modal-popular-section, .city-modal-regions-section');
  const otherSectionHeader = document.querySelector('.city-modal-all-section-title');

  if (query.length > 0) {
    sections.forEach(sec => sec.style.display = 'none');
    if (otherSectionHeader) {
      otherSectionHeader.textContent = 'Результаты поиска';
    }
    items.forEach(item => {
      const cityName = item.textContent.toLowerCase().replace(/ё/g, 'е');
      if (cityName.includes(query)) {
        item.style.display = 'block';
      } else {
        item.style.display = 'none';
      }
    });
  } else {
    sections.forEach(sec => sec.style.display = 'block');
    if (otherSectionHeader) {
      otherSectionHeader.textContent = 'Остальные города';
    }
    items.forEach(item => {
      if (item.classList.contains('city-special-item')) {
        item.style.display = 'none';
      } else {
        item.style.display = 'block';
      }
    });
  }
}

function selectModalCity(city) {
  const valEl = document.getElementById('hero-city-val');
  if (valEl) {
    valEl.textContent = city;
  }
  
  // Normalize string for comparison
  const norm = s => s.toLowerCase().replace(/ё/g, 'е').trim();
  const targetNorm = norm(city);
  
  // Update active class in modal
  document.querySelectorAll('.city-modal-item').forEach(item => {
    if (norm(item.textContent) === targetNorm) {
      item.classList.add('active');
    } else {
      item.classList.remove('active');
    }
  });
  
  closeCityModal();
}

function changeHeroGuests(val, event) {
  if (event) event.stopPropagation();
  
  if (heroGuestsCount === null) {
    heroGuestsCount = 1;
  } else {
    heroGuestsCount += val;
    if (heroGuestsCount < 1) heroGuestsCount = null;
    if (heroGuestsCount > 10) heroGuestsCount = 10;
  }
  
  const labelText = heroGuestsCount === null ? 'Любое количество' : heroGuestsCount + ' ' + getGuestsPlural(heroGuestsCount);
  const countText = heroGuestsCount === null ? 'Любое' : heroGuestsCount + '+';
  
  const heroGuestsVal = document.getElementById('hero-guests-val');
  const heroGuestsCountEl = document.getElementById('hero-guests-count');
  
  if (heroGuestsVal) heroGuestsVal.textContent = labelText;
  if (heroGuestsCountEl) heroGuestsCountEl.textContent = countText;
}

function submitHeroSearch(event) {
  if (event) event.stopPropagation();
  if (event) event.preventDefault();
  
  const cityEl = document.getElementById('hero-city-val');
  const city = cityEl ? cityEl.textContent.trim() : 'Вся Беларусь';
  const guests = heroGuestsCount;
  
  // Format query params
  const params = [];
  if (guests) params.push('guests=' + encodeURIComponent(guests));
  const queryString = params.length > 0 ? '?' + params.join('&') : '';
  
  const currentCategory = @json($seoCategory ?? null);
  const isValidCategory = currentCategory && ['apartments', 'cottages', 'hourly'].includes(currentCategory);

  // Determine target route path
  let targetPath = '/';
  if (city && city !== 'Вся Беларусь') {
    const slug = window.FLATLY_CITIES_SLUGS ? window.FLATLY_CITIES_SLUGS[city] : null;
    if (slug) {
      if (isValidCategory) {
        targetPath = '/city/' + slug + '/' + currentCategory;
      } else {
        targetPath = '/city/' + slug;
      }
    }
  } else {
    if (isValidCategory) {
      targetPath = '/' + currentCategory;
    }
  }
  
  window.location.href = targetPath + queryString;
}

// ===== HOMEPAGE HERO CAROUSEL LOGIC =====
const carouselSlidesData = [
  {
    title: 'Аренда квартир и коттеджей посуточно в Беларуси',
    subtitle: 'Без посредников, комиссий и переплат — напрямую от хозяев'
  },
  {
    title: 'Комфортные коттеджи и загородные дома',
    subtitle: 'Уютный отдых на природе, баня, барбекю и живописные виды'
  },
  {
    title: 'Стильное и уникальное жилье для отдыха',
    subtitle: 'Лофты, дизайнерские студии и уютные квартиры напрямую от собственников'
  }
];

let heroCurrentSlide = 0;
let heroCarouselTimer = null;

function initHeroCarousel() {
  const slides = document.querySelectorAll('.hero-slide');
  if (!slides || slides.length === 0) return;
  
  const titleEl = document.getElementById('hero-carousel-title');
  const subtitleEl = document.getElementById('hero-carousel-subtitle');
  
  if (titleEl && subtitleEl) {
    titleEl.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
    subtitleEl.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
  }
  
  startHeroCarouselTimer();
}

function showHeroSlide(index) {
  const slides = document.querySelectorAll('.hero-slide');
  const dots = document.querySelectorAll('.hero-carousel-dot');
  if (!slides || slides.length === 0) return;
  
  let targetIndex = index;
  if (index >= slides.length) targetIndex = 0;
  if (index < 0) targetIndex = slides.length - 1;
  
  if (targetIndex === heroCurrentSlide) return;
  
  const titleEl = document.getElementById('hero-carousel-title');
  const subtitleEl = document.getElementById('hero-carousel-subtitle');
  
  // Fade out text content briefly to change it
  if (titleEl && subtitleEl) {
    titleEl.style.opacity = '0';
    titleEl.style.transform = 'translateY(-8px)';
    subtitleEl.style.opacity = '0';
    subtitleEl.style.transform = 'translateY(-8px)';
  }
  
  // Update slides opacity
  slides[heroCurrentSlide].classList.remove('active');
  if (dots[heroCurrentSlide]) dots[heroCurrentSlide].classList.remove('active');
  
  heroCurrentSlide = targetIndex;
  
  slides[heroCurrentSlide].classList.add('active');
  if (dots[heroCurrentSlide]) dots[heroCurrentSlide].classList.add('active');
  
  // Fade in new text content
  setTimeout(() => {
    if (titleEl && subtitleEl && carouselSlidesData[heroCurrentSlide]) {
      titleEl.textContent = carouselSlidesData[heroCurrentSlide].title;
      subtitleEl.textContent = carouselSlidesData[heroCurrentSlide].subtitle;
      
      titleEl.style.opacity = '1';
      titleEl.style.transform = 'translateY(0)';
      subtitleEl.style.opacity = '1';
      subtitleEl.style.transform = 'translateY(0)';
    }
  }, 300);
  
  startHeroCarouselTimer();
}

function moveHeroCarousel(direction, event) {
  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }
  showHeroSlide(heroCurrentSlide + direction);
}

function setHeroCarouselSlide(index, event) {
  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }
  showHeroSlide(index);
}

function startHeroCarouselTimer() {
  if (heroCarouselTimer) clearInterval(heroCarouselTimer);
  heroCarouselTimer = setInterval(() => {
    showHeroSlide(heroCurrentSlide + 1);
  }, 6000);
}

window.customPageTitle = @json($seoH1 ?? null);

// Auto init if elements exist
document.addEventListener('DOMContentLoaded', () => {
  initHeroCarousel();
  
  // Auto-activate UI filters based on SEO category
  const category = @json($seoCategory ?? null);
  if (category) {
    if (category === 'dzhakuzi' || category === 'jacuzzi') {
      if (typeof selectObjectType === 'function') selectObjectType('apartments');
      const chk = document.querySelector('input[data-tag="джакузи"]');
      if (chk) { chk.checked = true; }
    } else if (['odnokomnatnye', '1-komnatnye', 'odnokomnatnaya', '1-room'].includes(category)) {
      if (typeof selectObjectType === 'function') selectObjectType('apartments');
      filterBedrooms = 1;
      searchRooms = 1;
      const searchRoomsLabel = document.getElementById('search-rooms-label');
      if (searchRoomsLabel) searchRoomsLabel.textContent = '1 комната';
      document.querySelectorAll('#search-rooms-dropdown .search-dropdown-item').forEach(item => {
        const itemRooms = item.getAttribute('data-rooms');
        if (parseInt(itemRooms) === 1) item.classList.add('active');
        else item.classList.remove('active');
      });
      const btn = document.querySelector('.filter-room-btn[data-val="1"]');
      if (btn) {
        document.querySelectorAll('.filter-room-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
      }
      const qfBtn = document.querySelector('.qf-room-btn[data-val="1"]');
      if (qfBtn) {
        document.querySelectorAll('.qf-room-btn').forEach(b => b.classList.remove('active'));
        qfBtn.classList.add('active');
        const qfText = document.querySelector('#qf-pill-bedrooms span');
        if (qfText) qfText.textContent = '1 спальня';
        const qfPill = document.getElementById('qf-pill-bedrooms');
        if (qfPill) qfPill.classList.add('active');
      }
    } else if (['dvuhkomnatnye', '2-komnatnye', 'dvuhkomnatnaya', '2-room'].includes(category)) {
      if (typeof selectObjectType === 'function') selectObjectType('apartments');
      filterBedrooms = 2;
      searchRooms = 2;
      const searchRoomsLabel = document.getElementById('search-rooms-label');
      if (searchRoomsLabel) searchRoomsLabel.textContent = '2 комнаты';
      document.querySelectorAll('#search-rooms-dropdown .search-dropdown-item').forEach(item => {
        const itemRooms = item.getAttribute('data-rooms');
        if (parseInt(itemRooms) === 2) item.classList.add('active');
        else item.classList.remove('active');
      });
      const btn = document.querySelector('.filter-room-btn[data-val="2"]');
      if (btn) {
        document.querySelectorAll('.filter-room-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
      }
      const qfBtn = document.querySelector('.qf-room-btn[data-val="2"]');
      if (qfBtn) {
        document.querySelectorAll('.qf-room-btn').forEach(b => b.classList.remove('active'));
        qfBtn.classList.add('active');
        const qfText = document.querySelector('#qf-pill-bedrooms span');
        if (qfText) qfText.textContent = '2 спальни';
        const qfPill = document.getElementById('qf-pill-bedrooms');
        if (qfPill) qfPill.classList.add('active');
      }
    } else if (['trehkomnatnye', '3-komnatnye', 'trehkomnatnaya', '3-room'].includes(category)) {
      if (typeof selectObjectType === 'function') selectObjectType('apartments');
      filterBedrooms = 3;
      searchRooms = 3;
      const searchRoomsLabel = document.getElementById('search-rooms-label');
      if (searchRoomsLabel) searchRoomsLabel.textContent = '3 комнаты';
      document.querySelectorAll('#search-rooms-dropdown .search-dropdown-item').forEach(item => {
        const itemRooms = item.getAttribute('data-rooms');
        if (parseInt(itemRooms) === 3) item.classList.add('active');
        else item.classList.remove('active');
      });
      const btn = document.querySelector('.filter-room-btn[data-val="3"]');
      if (btn) {
        document.querySelectorAll('.filter-room-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
      }
      const qfBtn = document.querySelector('.qf-room-btn[data-val="3"]');
      if (qfBtn) {
        document.querySelectorAll('.qf-room-btn').forEach(b => b.classList.remove('active'));
        qfBtn.classList.add('active');
        const qfText = document.querySelector('#qf-pill-bedrooms span');
        if (qfText) qfText.textContent = '3 спальни';
        const qfPill = document.getElementById('qf-pill-bedrooms');
        if (qfPill) qfPill.classList.add('active');
      }
    } else if (['chetyrehkomnatnye', '4-komnatnye', 'chetyrehkomnatnaya', '4-room'].includes(category)) {
      if (typeof selectObjectType === 'function') selectObjectType('apartments');
      filterBedrooms = 4;
      searchRooms = 4;
      const searchRoomsLabel = document.getElementById('search-rooms-label');
      if (searchRoomsLabel) searchRoomsLabel.textContent = '4+ комнат';
      document.querySelectorAll('#search-rooms-dropdown .search-dropdown-item').forEach(item => {
        const itemRooms = item.getAttribute('data-rooms');
        if (parseInt(itemRooms) === 4) item.classList.add('active');
        else item.classList.remove('active');
      });
      const btn = document.querySelector('.filter-room-btn[data-val="4"]');
      if (btn) {
        document.querySelectorAll('.filter-room-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
      }
      const qfBtn = document.querySelector('.qf-room-btn[data-val="4"]');
      if (qfBtn) {
        document.querySelectorAll('.qf-room-btn').forEach(b => b.classList.remove('active'));
        qfBtn.classList.add('active');
        const qfText = document.querySelector('#qf-pill-bedrooms span');
        if (qfText) qfText.textContent = '4+ спальни';
        const qfPill = document.getElementById('qf-pill-bedrooms');
        if (qfPill) qfPill.classList.add('active');
      }
    } else if (category === 's-baney' || category === 'with-sauna') {
      if (typeof selectObjectType === 'function') selectObjectType('cottages');
      const chk1 = document.querySelector('input[data-tag="русская баня"]');
      if (chk1) { chk1.checked = true; }
      const chk2 = document.querySelector('input[data-tag="финская сауна"]');
      if (chk2) { chk2.checked = true; }
      const tag = document.querySelector('.qf-tag[data-tag="русская баня"]');
      if (tag) { tag.classList.add('active'); }
    } else if (category === 's-basseynom' || category === 'with-pool') {
      if (typeof selectObjectType === 'function') selectObjectType('cottages');
      const chk1 = document.querySelector('input[data-tag="бассейн"]');
      if (chk1) { chk1.checked = true; }
      const chk2 = document.querySelector('input[data-tag="купель"]');
      if (chk2) { chk2.checked = true; }
      const tag = document.querySelector('.qf-tag[data-tag="бассейн"]');
      if (tag) { tag.classList.add('active'); }
    }
  }
});

function toggleSeoSpoiler(event) {
  if (event) event.stopPropagation();
  const container = document.querySelector('.seo-spoiler-container');
  const content = document.getElementById('seo-spoiler-content');
  if (!container || !content) return;
  
  const isActive = container.classList.toggle('active');
  if (isActive) {
    content.style.maxHeight = content.scrollHeight + 'px';
  } else {
    content.style.maxHeight = '0';
  }
}
</script>
@endsection
