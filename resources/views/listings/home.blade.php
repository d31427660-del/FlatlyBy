@extends('layouts.app')

@section('title', 'Flatly.by — квартиры и коттеджи посуточно в Беларуси')
@section('meta_description', 'Посуточная аренда квартир и домов по всей Беларуси. Карта объектов, цены за сутки и прямой контакт с хозяином без комиссии сервиса.')
@section('canonical_url', 'https://flatly.by/')

@section('head')
<style>
/* ===== HERO BANNER ===== */
.home-carousel {
  position: relative;
  width: 100%;
  max-width: 1400px;
  margin: 0 auto 48px;
  overflow: hidden;
  border-radius: 24px;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
  background: #0B1E36;
}
.home-carousel-slide {
  width: 100%;
  min-height: 380px;
  position: relative;
  background-size: cover;
  background-position: center right;
  display: flex;
  align-items: center;
}
.home-carousel-overlay {
  width: 100%;
  height: 100%;
  min-height: 380px;
  background: linear-gradient(90deg, rgba(11, 30, 54, 0.92) 0%, rgba(11, 30, 54, 0.75) 45%, rgba(11, 30, 54, 0.2) 100%);
  display: flex;
  align-items: center;
  padding: 40px 64px;
  box-sizing: border-box;
}
.home-carousel-content {
  max-width: 580px;
  text-align: left;
  color: #FFFFFF;
  z-index: 2;
}
.home-carousel-tag {
  background: var(--amber);
  color: var(--slate-900);
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  padding: 5px 14px;
  border-radius: 20px;
  display: inline-block;
  margin-bottom: 14px;
  box-shadow: 0 2px 10px rgba(245, 166, 35, 0.3);
  letter-spacing: 1.2px;
}
.home-carousel-title {
  font-family: 'Outfit', 'Syne', sans-serif;
  font-size: 36px;
  font-weight: 800;
  line-height: 1.2;
  margin: 0 0 14px 0;
  letter-spacing: -0.5px;
  color: #FFFFFF;
  text-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.home-carousel-desc {
  font-size: 15.5px;
  color: rgba(255, 255, 255, 0.92);
  margin-bottom: 28px;
  line-height: 1.55;
  font-weight: 500;
  text-shadow: 0 1px 4px rgba(0,0,0,0.2);
}
.home-carousel-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--amber);
  color: var(--slate-900);
  padding: 13px 32px;
  border-radius: 30px;
  font-weight: 800;
  font-size: 15px;
  transition: all 0.25s ease;
  text-decoration: none;
  box-shadow: 0 6px 20px rgba(245, 166, 35, 0.4);
}
.home-carousel-btn:hover {
  background: #F59E0B;
  color: #0F172A;
  transform: translateY(-2px);
  box-shadow: 0 10px 24px rgba(245, 166, 35, 0.5);
}

/* Tablet & Mobile responsive banner */
@media (max-width: 1024px) {
  .home-carousel {
    border-radius: 18px;
    margin-bottom: 36px;
  }
  .home-carousel-slide,
  .home-carousel-overlay {
    min-height: 320px;
    padding: 32px 40px;
  }
  .home-carousel-overlay {
    background: linear-gradient(90deg, rgba(11, 30, 54, 0.94) 0%, rgba(11, 30, 54, 0.8) 60%, rgba(11, 30, 54, 0.35) 100%);
  }
  .home-carousel-title {
    font-size: 28px;
  }
  .home-carousel-desc {
    font-size: 14px;
    margin-bottom: 22px;
  }
}

@media (max-width: 768px) {
  .home-carousel {
    border-radius: 16px;
    margin-bottom: 28px;
  }
  .home-carousel-slide,
  .home-carousel-overlay {
    min-height: auto;
    padding: 24px 20px;
  }
  .home-carousel-overlay {
    background: linear-gradient(180deg, rgba(11, 30, 54, 0.88) 0%, rgba(11, 30, 54, 0.78) 100%);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
  }
  .home-carousel-content {
    max-width: 100%;
  }
  .home-carousel-tag {
    font-size: 10px;
    padding: 4px 10px;
    margin-bottom: 10px;
  }
  .home-carousel-title {
    font-size: 22px;
    margin-bottom: 10px;
    line-height: 1.25;
  }
  .home-carousel-desc {
    font-size: 13px;
    margin-bottom: 18px;
    line-height: 1.45;
  }
  .home-carousel-btn {
    padding: 10px 22px;
    font-size: 13.5px;
    width: 100%;
    box-sizing: border-box;
    text-align: center;
  }
}

@media (max-width: 480px) {
  .home-carousel {
    border-radius: 14px;
    margin-bottom: 20px;
  }
  .home-carousel-overlay {
    padding: 20px 16px;
  }
  .home-carousel-title {
    font-size: 19px;
  }
  .home-carousel-desc {
    font-size: 12.5px;
  }
}

/* ===== SECTION LAYOUT ===== */
.section-container {
  max-width: 1400px;
  margin: 0 auto 64px;
  padding: 0 32px;
}
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 24px;
}
.section-title-wrap {
  flex: 1;
  text-align: left;
}
.section-title {
  font-family: 'Syne', sans-serif;
  font-size: 26px;
  font-weight: 800;
  color: var(--slate-900);
  line-height: 1.2;
  letter-spacing: -0.5px;
}
.section-subtitle {
  font-size: 14px;
  color: var(--gray-text);
  margin-top: 4px;
}
.section-link {
  font-weight: 700;
  color: var(--slate-900);
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 14px;
  transition: color 0.15s;
  text-decoration: none;
}
.section-link:hover {
  color: var(--amber-dark);
}

/* ===== RECOMMENDED LISTINGS GRID ===== */
.listings-home-grid {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 16px;
  width: 100%;
  box-sizing: border-box;
}

@media (max-width: 1200px) {
  .listings-home-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 900px) {
  .listings-home-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 600px) {
  .listings-home-grid { grid-template-columns: 1fr; }
}

.recommended-section {
  max-width: 1400px !important;
  margin: 0 auto 48px !important;
  padding: 0;
  box-sizing: border-box;
}

.vip-dark-card {
  background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
  border-radius: 28px;
  padding: 36px 32px;
  color: #FFFFFF;
  box-shadow: 0 20px 40px rgba(15, 23, 42, 0.25);
  position: relative;
  overflow: hidden;
}

.vip-glow-sphere {
  position: absolute;
  width: 400px;
  height: 400px;
  border-radius: 50%;
  pointer-events: none;
  z-index: 1;
}
.vip-glow-1 {
  top: -150px;
  right: -100px;
  background: radial-gradient(circle, rgba(245, 166, 35, 0.15) 0%, rgba(245, 166, 35, 0) 70%);
  filter: blur(60px);
}
.vip-glow-2 {
  bottom: -150px;
  left: -100px;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, rgba(99, 102, 241, 0) 70%);
  filter: blur(60px);
}

.vip-badge-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 14px;
  border-radius: 20px;
  background: rgba(245, 166, 35, 0.18);
  border: 1px solid rgba(245, 166, 35, 0.35);
  color: #F5A623;
  font-size: 12px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 10px;
}

.recommended-header-flex {
  display: flex !important;
  align-items: flex-end !important;
  justify-content: space-between !important;
  gap: 20px;
  margin-bottom: 20px;
  width: 100%;
  position: relative;
  z-index: 2;
}

.vip-carousel-track-wrap {
  width: 100%;
  overflow-x: auto;
  scroll-behavior: smooth;
  scrollbar-width: none;
  -ms-overflow-style: none;
  padding: 8px 4px 16px;
  position: relative;
  z-index: 2;
  scroll-snap-type: x mandatory;
}
.vip-carousel-track-wrap::-webkit-scrollbar {
  display: none;
}

.vip-carousel-track {
  display: flex;
  gap: 20px;
}

.vip-carousel-slide {
  flex: 0 0 calc(25% - 15px);
  min-width: 290px;
  scroll-snap-align: start;
  display: flex;
}
.vip-carousel-slide .card {
  width: 100%;
}

@media (max-width: 1280px) {
  .vip-carousel-slide {
    flex: 0 0 calc(33.333% - 14px);
  }
}
@media (max-width: 900px) {
  .vip-carousel-slide {
    flex: 0 0 calc(50% - 10px);
  }
}
@media (max-width: 580px) {
  .vip-carousel-slide {
    flex: 0 0 85%;
    min-width: 270px;
  }
  .vip-dark-card {
    padding: 24px 16px;
    border-radius: 20px;
  }
}

.vip-arrow-btn {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.18);
  color: #FFFFFF;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  transition: all 0.2s ease;
}

.vip-howto-mobile {
  display: none !important;
}

@media (max-width: 768px) {
  .vip-subtitle-desktop {
    display: none !important;
  }
  .vip-howto-desktop {
    display: none !important;
  }
  .vip-howto-mobile {
    display: inline-flex !important;
  }
}

.vip-arrow-btn:hover {
  background: #F5A623;
  border-color: #F5A623;
  color: #0F172A;
  transform: scale(1.05);
  box-shadow: 0 4px 16px rgba(245, 166, 35, 0.4);
}
.vip-arrow-btn:active {
  transform: scale(0.95);
}

/* ===== CARD STYLES COPIED FROM INDEX.BLADE.PHP ===== */
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
  background: linear-gradient(135deg, #F5A623, #D4890A); /* changed blue gradient to theme gold/amber */
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

/* ===== CARD DETAILS ===== */
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

/* ===== NEWS CARDS ===== */
.news-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}
.news-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.02);
  transition: all 0.25s ease;
  display: flex;
  flex-direction: column;
}
.news-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 28px rgba(0,0,0,0.06);
  border-color: var(--slate-300);
}
.news-img-wrap {
  width: 100%;
  aspect-ratio: 1.5;
  overflow: hidden;
  background: var(--slate-100);
}
.news-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}
.news-card:hover .news-img {
  transform: scale(1.04);
}
.news-body {
  padding: 18px;
  display: flex;
  flex-direction: column;
  flex: 1;
  text-align: left;
}
.news-card-title {
  font-size: 14.5px;
  font-weight: 700;
  color: var(--slate-900);
  line-height: 1.4;
  margin-bottom: 16px;
  flex: 1;
}
.news-card-link {
  font-size: 13px;
  font-weight: 700;
  color: var(--amber-dark); /* changed generic blue to amber dark */
  display: flex;
  align-items: center;
  gap: 4px;
  text-decoration: none;
  transition: color 0.15s;
}
.news-card-link:hover {
  color: var(--amber);
  text-decoration: underline;
}

@media (max-width: 1024px) {
  .news-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
  .news-grid { grid-template-columns: 1fr; }
}

/* ===== FAQ ACCORDION ===== */
.faq-section {
  background: var(--slate-50);
  padding: 64px 32px;
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
  margin-bottom: 64px;
}
.faq-container {
  max-width: 900px;
  margin: 0 auto;
}
.faq-header {
  text-align: center;
  margin-bottom: 40px;
}
.faq-title {
  font-family: 'Syne', sans-serif;
  font-size: 28px;
  font-weight: 800;
  color: var(--slate-900);
  letter-spacing: -0.5px;
}
.faq-subtitle {
  font-size: 14.5px;
  color: var(--gray-text);
  margin-top: 6px;
}
.faq-wrap {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.faq-item {
  border: 1px solid var(--border);
  border-radius: 12px;
  background: var(--white);
  overflow: hidden;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.faq-item:hover {
  border-color: var(--slate-300);
}
.faq-trigger {
  width: 100%;
  background: none;
  border: none;
  padding: 18px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  text-align: left;
  cursor: pointer;
  outline: none;
}
.faq-question {
  font-size: 15px;
  font-weight: 700;
  color: var(--slate-900);
}
.faq-icon {
  width: 20px;
  height: 20px;
  stroke: var(--slate-900);
  stroke-width: 2.5;
  fill: none;
  transition: transform 0.25s ease;
  flex-shrink: 0;
  margin-left: 12px;
}
.faq-panel {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.faq-content {
  padding: 0 24px 20px;
  font-size: 14px;
  color: var(--gray-text);
  line-height: 1.6;
  text-align: left;
}
.faq-item.active {
  border-color: var(--slate-900);
  box-shadow: 0 4px 12px rgba(26,26,46,0.03);
}
.faq-item.active .faq-icon {
  transform: rotate(180deg);
}

/* ===== HOST CTA BANNER ===== */
.host-cta-section {
  max-width: 1400px;
  margin: 0 auto 80px;
  padding: 0 32px;
}
.host-cta-banner {
  width: 100%;
  height: 300px;
  border-radius: 24px;
  overflow: hidden;
  position: relative;
  background-size: cover;
  background-position: center;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
}
.host-cta-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, rgba(26, 26, 46, 0.85) 0%, rgba(26, 26, 46, 0.4) 100%);
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 0 64px;
  color: var(--white);
  text-align: left;
}
.host-cta-title {
  font-family: 'Syne', sans-serif;
  font-size: 32px;
  font-weight: 800;
  margin-bottom: 12px;
  letter-spacing: -0.5px;
}
.host-cta-desc {
  font-size: 16px;
  opacity: 0.9;
  margin-bottom: 24px;
  max-width: 500px;
  line-height: 1.5;
}
.host-cta-btn {
  display: inline-block;
  background: var(--white);
  color: var(--slate-900); /* changed generic blue to slate dark to match theme */
  font-weight: 700;
  font-size: 14px;
  padding: 12px 28px;
  border-radius: 30px;
  align-self: flex-start;
  transition: all 0.2s ease;
  text-decoration: none;
}
.host-cta-btn:hover {
  background: var(--amber);
  color: var(--slate-900);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(245, 166, 35, 0.2);
}

/* ===== COLLAPSIBLE SECTIONS ===== */
.collapsed-link.collapsed {
  display: none !important;
}
.show-more-links-btn {
  display: inline-flex !important;
  background: none;
  border: none;
  color: var(--amber-dark);
  font-size: 13.5px;
  font-weight: 700;
  cursor: pointer;
  padding: 4px 0;
  margin-top: 10px;
  align-items: center;
  gap: 4px;
  width: 100%;
  text-align: left;
}
.mobile-collapsed-district.collapsed {
  display: flex;
}
.show-more-districts-wrapper {
  display: none;
}

@media (max-width: 768px) {
  .mobile-collapsed-district.collapsed {
    display: none !important;
  }
  .show-more-districts-wrapper {
    display: flex !important;
  }
}
</style>
@endsection

@section('content')
<h1 style="position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0,0,0,0); border: 0;">Аренда квартир и коттеджей в Беларуси</h1>

<!-- HERO BANNER (FREE PLACEMENT) -->
<div class="home-carousel">
  <div class="home-carousel-slide" style="background-image: url('/images/banners/flatly_free_placement_banner.png?v={{ time() }}');">
    <div class="home-carousel-overlay">
      <div class="home-carousel-content">
        <span class="home-carousel-tag">Арендодателям</span>
        <h2 class="home-carousel-title">Размещение за 0 Б̶ без комиссий</h2>
        <p class="home-carousel-desc">Сдавайте ваши квартиры, коттеджи и усадьбы гостям напрямую. Никаких скрытых платежей, комиссионных сборов и переплат. Только прямой контакт!</p>
        <a href="{{ route('listings.create') }}" class="home-carousel-btn">Подать объявление</a>
      </div>
    </div>
  </div>
</div>

<!-- RECOMMENDED LISTINGS (VIP CLUB DARK THEME) -->
<div class="section-container recommended-section vip-dark-card">
  <div class="vip-glow-sphere vip-glow-1"></div>
  <div class="vip-glow-sphere vip-glow-2"></div>

  <div class="recommended-header-flex">
    <div class="section-title-wrap">
      <div class="vip-badge-pill">
        <span>🌟</span> Премиум выбор
      </div>
      <h2 class="section-title" style="margin: 0; color: #FFFFFF;">Рекомендуемые предложения</h2>
      <p class="section-subtitle vip-subtitle-desktop" style="margin-top: 4px; color: #94A3B8;">Выбранные объявления с премиум-размещением на главной странице</p>
      <button onclick="openVipHowtoModal()" class="vip-howto-btn vip-howto-mobile" style="margin-top: 10px; background: rgba(245, 166, 35, 0.15); border: 1px solid rgba(245, 166, 35, 0.4); color: #F5A623; border-radius: 20px; padding: 8px 16px; font-size: 13px; font-weight: 700; cursor: pointer; align-items: center; gap: 6px; transition: all 0.2s ease;">
        <x-solar-question-circle-linear width="16" height="16" />
        Как сюда попасть?
      </button>
    </div>

    <!-- Carousel Navigation Arrows & How-to Button -->
    <div class="vip-carousel-nav" style="display: flex; align-items: center; gap: 12px; flex-shrink: 0; margin-bottom: 4px;">
      <button onclick="openVipHowtoModal()" class="vip-howto-btn vip-howto-desktop" style="background: rgba(245, 166, 35, 0.15); border: 1px solid rgba(245, 166, 35, 0.4); color: #F5A623; border-radius: 20px; padding: 8px 16px; font-size: 13px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease;">
        <x-solar-question-circle-linear width="16" height="16" />
        Как сюда попасть?
      </button>
      <button class="vip-arrow-btn prev" onclick="scrollVipCarousel(-1)" aria-label="Предыдущие объявления">
        <x-solar-alt-arrow-left-linear width="20" height="20" />
      </button>
      <button class="vip-arrow-btn next" onclick="scrollVipCarousel(1)" aria-label="Следующие объявления">
        <x-solar-alt-arrow-right-linear width="20" height="20" />
      </button>
    </div>
  </div>

  <div class="vip-carousel-track-wrap" id="vip-carousel-track-wrap">
    <div class="vip-carousel-track" id="vip-carousel-track">
      @foreach($featuredListings as $item)
        <div class="vip-carousel-slide">
          @include('listings.partials.card', ['item' => $item, 'visibleOnLoad' => true])
        </div>
      @endforeach
    </div>
  </div>
</div>

<!-- POPULAR SELECTIONS SECTION -->
<div class="section-container" style="margin-bottom: 64px;">
  <div class="section-header">
    <div class="section-title-wrap">
      <h2 class="section-title">Популярные подборки жилья</h2>
      <p class="section-subtitle">Быстрый выбор квартир посуточно по фильтрам и городам</p>
    </div>
  </div>
  
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
    @php
      $seoPagesConfig = config('seo_landing_pages');
      $citiesRussianNames = [
          'minsk' => 'Минск',
          'brest' => 'Брест',
          'grodno' => 'Гродно',
          'gomel' => 'Гомель',
          'vitebsk' => 'Витебск',
          'mogilev' => 'Могилёв'
      ];
    @endphp
    @foreach($citiesRussianNames as $citySlug => $cityName)
      @if(isset($seoPagesConfig[$citySlug]))
        @php
          $filteredCityPages = collect($seoPagesConfig[$citySlug])->reject(function($pageData, $tagSlug) {
              return in_array($tagSlug, ['_main', 'u-vokzala', 'kvartiry-na-sutki-u-metro']);
          });
        @endphp
        <div style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 24px; text-align: left; box-shadow: 0 4px 16px rgba(0,0,0,0.01);">
          <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; font-weight: 800; color: var(--slate-900); margin: 0 0 16px 0; border-bottom: 2px solid var(--amber); padding-bottom: 8px; display: inline-block;">
            <a href="/{{ $citySlug }}" style="color: inherit; text-decoration: none;">{{ $cityName }}</a>
          </h3>
          <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px;">
            @foreach($filteredCityPages as $tagSlug => $pageData)
              <li class="{{ $loop->iteration > 3 ? 'collapsed-link collapsed' : '' }}">
                <a href="/{{ $citySlug }}/{{ $tagSlug }}" style="font-size: 14px; font-weight: 600; color: var(--slate-700); text-decoration: none; display: flex; align-items: center; gap: 6px; transition: color 0.15s;" onmouseover="this.style.color='var(--amber-dark)'" onmouseout="this.style.color='var(--slate-700)'">
                  <span style="color: var(--amber-dark); font-size: 12px;" aria-hidden="true">⚡</span>
                  {{ $pageData['h1'] }}
                </a>
              </li>
            @endforeach
          </ul>
          @if($filteredCityPages->count() > 3)
            <button type="button" class="show-more-links-btn" aria-expanded="false" onclick="toggleMobileLinks(this, '{{ $citySlug }}')">
              Показать все ({{ $filteredCityPages->count() }})
            </button>
          @endif
        </div>
      @endif
    @endforeach
  </div>
</div>

<!-- MINSK DISTRICTS SECTION -->
<div class="section-container" style="margin-bottom: 64px;">
  <div class="section-header">
    <div class="section-title-wrap">
      <h2 class="section-title">Популярные районы Минска</h2>
      <p class="section-subtitle">Быстрый поиск посуточного жилья в микрорайонах столицы</p>
    </div>
  </div>
  
  <div style="background: var(--white); border: 1px solid var(--border); border-radius: 24px; padding: 32px; box-shadow: 0 4px 16px rgba(0,0,0,0.01); text-align: left;">
    @php
      $minskDistrictsList = [
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
      $districtIndex = 0;
    @endphp
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px;">
      @foreach($minskDistrictsList as $slug => $name)
        @php
          $districtIndex++;
          $isDistrictHiddenOnMobile = $districtIndex > 6;
        @endphp
        <a href="/minsk/{{ $slug }}" class="{{ $isDistrictHiddenOnMobile ? 'mobile-collapsed-district collapsed' : '' }}" style="font-size: 14.5px; font-weight: 700; color: var(--slate-700); text-decoration: none; display: flex; align-items: center; gap: 8px; padding: 12px 18px; border: 1.5px solid var(--border); border-radius: 16px; transition: all 0.2s; background: var(--white);" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'; this.style.background='#fffbeb';" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'; this.style.background='var(--white)';">
          <span style="color: var(--amber-dark); font-size: 14px;">📍</span>
          {{ $name }}
        </a>
      @endforeach
    </div>
    @if(count($minskDistrictsList) > 6)
      <div class="show-more-districts-wrapper" style="justify-content: center; margin-top: 24px;">
        <button class="show-more-districts-btn" onclick="toggleMobileDistricts(this)" style="background: rgba(245, 166, 35, 0.1); border: 1.5px solid var(--amber); color: var(--slate-900); border-radius: 16px; padding: 12px 24px; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
          Показать все районы Минска ({{ count($minskDistrictsList) }})
        </button>
      </div>
    @endif
  </div>
</div>

<!-- NEWS SECTION -->
<div class="section-container">
  @php
    try {
        $blogPosts = \App\Models\BlogPost::where(function($q) {
            $q->where('source_name', '!=', '34travel.me')
              ->orWhereNull('source_name');
        })
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();
    } catch (\Exception $e) {
        $blogPosts = collect();
    }

    if ($blogPosts->isEmpty()) {
        $blogPosts = collect([
            (object)[
                'title' => 'Как безопасно снять квартиру посуточно в Минске: советы арендаторам',
                'slug' => 'kak-bezopasno-snyat-kvartiru-posutochno-minsk',
                'image' => '/images/news_belarus_mvd.png',
            ],
            (object)[
                'title' => 'Какие правила заселения при посуточной аренде?',
                'slug' => 'kakie-pravila-zaseleniya-pri-posutochnoy-arende',
                'image' => '/images/banners/luxury_loft.png',
            ],
            (object)[
                'title' => 'Как подготовить квартиру к посуточной аренде?',
                'slug' => 'kak-podgotovit-kvartiru-k-posutochnoy-arende',
                'image' => '/images/news_apartment_appeal.png',
            ],
            (object)[
                'title' => 'Какие ошибки допускают владельцы посуточной аренды?',
                'slug' => 'kakie-oshibki-dopuskayut-vladelcy-posutochnoy-arendy',
                'image' => '/images/banners/minsk_apartment.png',
            ]
        ]);
    }
  @endphp

  <div class="section-header">
    <div class="section-title-wrap">
      <h2 class="section-title">Гид по Беларуси: полезное для путешественников и арендодателей</h2>
      <p class="section-subtitle">Статьи, советы и лайфхаки о краткосрочной аренде</p>
    </div>
    <a href="{{ route('blog.index') }}" class="section-link">Смотреть все →</a>
  </div>
  
  <div class="news-grid">
    @foreach($blogPosts as $post)
      <a href="{{ route('blog.show', $post->slug) }}" class="news-card" style="text-decoration: none; color: inherit;">
        <div class="news-img-wrap">
          <img class="news-img" src="{{ $post->image ?: '/images/news_apartment_appeal.png' }}" alt="{{ $post->title }}" loading="lazy">
        </div>
        <div class="news-body">
          <h3 class="news-card-title">{{ $post->title }}</h3>
          <span class="news-card-link">Подробнее ›</span>
        </div>
      </a>
    @endforeach
  </div>
</div>

<!-- SEO ARTICLE SECTION -->
@php
  $homeSeo = config('seo_landing_pages._home._main');
@endphp
@if(!empty($homeSeo['content_html']))
<div class="section-container" style="margin-bottom: 48px;">
  <div class="seo-article-card" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 32px; box-shadow: 0 4px 16px rgba(0,0,0,0.02); text-align: left; line-height: 1.7; color: var(--slate-700);">
    {!! str_replace(['\n', '\r'], ["\n", ''], $homeSeo['content_html']) !!}
  </div>
</div>
@endif

<!-- FAQ SECTION -->
<div class="faq-section">
  <div class="faq-container">
    <div class="faq-header">
      <h2 class="faq-title">Часто задаваемые вопросы</h2>
      <p class="faq-subtitle">Отвечаем на главные вопросы гостей и арендодателей</p>
    </div>
    
    <div class="faq-wrap">
      <!-- FAQ 1 -->
      <div class="faq-item">
        <button type="button" class="faq-trigger" aria-expanded="false" onclick="toggleFaq(this)">
          <span class="faq-question">Как забронировать жилье на Flatly.by?</span>
          <x-solar-alt-arrow-down-linear class="faq-icon" aria-hidden="true" />
        </button>
        <div class="faq-panel">
          <div class="faq-content">
            Выберите понравившийся вариант на интерактивной карте или в каталоге, откройте контакты владельца и свяжитесь с ним напрямую. Вы можете позвонить по телефону или написать в мессенджеры (Telegram, Viber, WhatsApp) для мгновенного обсуждения и бронирования.
          </div>
        </div>
      </div>

      <!-- FAQ 2 -->
      <div class="faq-item">
        <button type="button" class="faq-trigger" aria-expanded="false" onclick="toggleFaq(this)">
          <span class="faq-question">Правда ли, что размещение объявлений бесплатно?</span>
          <x-solar-alt-arrow-down-linear class="faq-icon" aria-hidden="true" />
        </button>
        <div class="faq-panel">
          <div class="faq-content">
            Да! Сейчас на Flatly.by действует акция — размещение любых квартир и коттеджей абсолютно бесплатно (0 Б̶). Вы можете сдать любое количество объектов без комиссий и абонентской платы.
          </div>
        </div>
      </div>

      <!-- FAQ 3 -->
      <div class="faq-item">
        <button type="button" class="faq-trigger" aria-expanded="false" onclick="toggleFaq(this)">
          <span class="faq-question">Какая комиссия сервиса для гостей и хозяев?</span>
          <x-solar-alt-arrow-down-linear class="faq-icon" aria-hidden="true" />
        </button>
        <div class="faq-panel">
          <div class="faq-content">
            Комиссия составляет 0%. Наш сервис работает по принципу «прямого контакта» без посредников. Гости платят ровно столько, сколько указал хозяин в объявлении, а хозяева получают 100% стоимости аренды.
          </div>
        </div>
      </div>

      <!-- FAQ 4 -->
      <div class="faq-item">
        <button type="button" class="faq-trigger" aria-expanded="false" onclick="toggleFaq(this)">
          <span class="faq-question">Как связаться с владельцем выбранного жилья?</span>
          <x-solar-alt-arrow-down-linear class="faq-icon" aria-hidden="true" />
        </button>
        <div class="faq-panel">
          <div class="faq-content">
            Нажмите кнопку «Показать контакты» на карточке или странице объявления. Система мгновенно откроет номер телефона и прямые ссылки на чаты в Telegram, Viber и WhatsApp. Вы сможете продолжить общение в любом удобном формате.
          </div>
        </div>
      </div>

      <!-- FAQ 5 -->
      <div class="faq-item">
        <button type="button" class="faq-trigger" aria-expanded="false" onclick="toggleFaq(this)">
          <span class="faq-question">Что делать, если владелец не отвечает на звонки или сообщения?</span>
          <x-solar-alt-arrow-down-linear class="faq-icon" aria-hidden="true" />
        </button>
        <div class="faq-panel">
          <div class="faq-content">
            Если владелец долго не отвечает, попробуйте написать ему в другой мессенджер или выбрать другие похожие объявления в этом же районе на карте. Наша база ежедневно пополняется новыми объектами.
          </div>
        </div>
      </div>

      <!-- FAQ 6 -->
      <div class="faq-item">
        <button type="button" class="faq-trigger" aria-expanded="false" onclick="toggleFaq(this)">
          <span class="faq-question">Какие документы необходимы при заселении?</span>
          <x-solar-alt-arrow-down-linear class="faq-icon" aria-hidden="true" />
        </button>
        <div class="faq-panel">
          <div class="faq-content">
            При заселении в жилье на сутки в Беларуси вам понадобится паспорт для заключения договора найма. Мы настоятельно рекомендуем всегда заключать письменный договор с хозяином и сохранять чек об оплате.
          </div>
        </div>
      </div>

      <!-- FAQ 7 -->
      <div class="faq-item">
        <button type="button" class="faq-trigger" aria-expanded="false" onclick="toggleFaq(this)">
          <span class="faq-question">Что такое верификация по УНП для арендодателей?</span>
          <x-solar-alt-arrow-down-linear class="faq-icon" aria-hidden="true" />
        </button>
        <div class="faq-panel">
          <div class="faq-content">
            Верификация по УНП (Учетному номеру плательщика) подтверждает легальность работы арендодателя на территории Республики Беларусь. Объявления с верифицированным УНП вызывают больше доверия у гостей и отмечаются особым статусом безопасности.
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- VIP HOW-TO MODAL -->
<div class="lightbox-overlay" id="vip-howto-modal" role="dialog" aria-modal="true" aria-labelledby="vip-howto-title" onclick="closeVipHowtoModal(event)" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(6px); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
  <div class="modal-card-dialog" onclick="event.stopPropagation()" style="background: #ffffff; border-radius: 24px; max-width: 520px; width: 100%; padding: 32px; box-shadow: 0 25px 50px rgba(0,0,0,0.3); position: relative; color: var(--text-main); animation: modalPop 0.25s cubic-bezier(0.16, 1, 0.3, 1);">
    <button type="button" onclick="closeVipHowtoModal(event)" aria-label="Закрыть" style="position: absolute; top: 20px; right: 20px; background: #F1F5F9; border: none; width: 34px; height: 34px; border-radius: 50%; font-size: 20px; cursor: pointer; color: var(--slate-900); display: flex; align-items: center; justify-content: center; transition: background 0.2s;">&times;</button>
    
    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 20px;">
      <div style="width: 48px; height: 48px; border-radius: 16px; background: rgba(245, 166, 35, 0.15); color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;" aria-hidden="true">
        🌟
      </div>
      <div>
        <h3 id="vip-howto-title" style="font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 800; color: var(--slate-900); margin: 0; line-height: 1.2;">Как попасть в «Рекомендуемые»?</h3>
        <p style="font-size: 13px; color: var(--gray-text); margin: 4px 0 0 0;">Премиум-размещение в самом верху главной страницы</p>
      </div>
    </div>

    <div style="background: #F8FAFC; border: 1px solid var(--border); border-radius: 16px; padding: 20px; margin-bottom: 24px;">
      <div style="display: flex; gap: 14px; margin-bottom: 16px;">
        <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--amber); color: var(--slate-900); font-weight: 800; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">1</div>
        <div style="font-size: 14px; line-height: 1.45; color: var(--slate-900);">
          Перейдите в ваш <strong>Личный кабинет</strong> во вкладку <strong>«Мои объявления»</strong>.
        </div>
      </div>

      <div style="display: flex; gap: 14px; margin-bottom: 16px;">
        <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--amber); color: var(--slate-900); font-weight: 800; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">2</div>
        <div style="font-size: 14px; line-height: 1.45; color: var(--slate-900);">
          Нажмите <strong>«На главной»</strong> напротив нужной квартиры.
        </div>
      </div>

      <div style="display: flex; gap: 14px;">
        <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--amber); color: var(--slate-900); font-weight: 800; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">3</div>
        <div style="font-size: 14px; line-height: 1.45; color: var(--slate-900);">
          Подключите услугу. Ваше жильё займет эксклюзивное место в карусели из <strong>10 рекомендуемых объектов</strong> и получит до <strong>5 раз больше просмотров и звонков</strong>!
        </div>
      </div>
    </div>

    <a href="/dashboard" class="primary-action-btn" style="display: block; text-align: center; text-decoration: none; padding: 14px; font-size: 15px; font-weight: 700; background: var(--amber); color: var(--slate-900); border-radius: 14px; box-shadow: 0 4px 14px rgba(245, 166, 35, 0.3); transition: all 0.2s ease;">
      Перейти в личный кабинет ➔
    </a>
  </div>
</div>

@endsection

@section('scripts')
<script>
// Carousel Slider Logic
let currentSlide = 0;

function showSlide(index) {
  const track = document.getElementById('home-carousel-track');
  const dots = document.querySelectorAll('.hc-dot');
  if (index < 0) index = 2;
  if (index > 2) index = 0;
  currentSlide = index;
  if (track) {
    track.style.transform = `translateX(-${currentSlide * 33.333}%)`;
  }
  dots.forEach((dot, i) => {
    dot.classList.toggle('active', i === currentSlide);
  });
}

function nextSlide() {
  showSlide(currentSlide + 1);
}

function prevSlide() {
  showSlide(currentSlide - 1);
}

// Auto-play interval
let autoplay = setInterval(nextSlide, 5000);

function selectSlide(index) {
  clearInterval(autoplay);
  showSlide(index);
  autoplay = setInterval(nextSlide, 5000);
}

// Mobile Touch Swipe Listener
document.addEventListener('DOMContentLoaded', () => {
  let touchStartX = 0;
  let touchEndX = 0;

  const carouselEl = document.getElementById('home-carousel');
  if (carouselEl) {
    carouselEl.addEventListener('touchstart', e => {
      touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });

    carouselEl.addEventListener('touchend', e => {
      touchEndX = e.changedTouches[0].screenX;
      const diff = touchEndX - touchStartX;
      if (Math.abs(diff) > 40) {
        clearInterval(autoplay);
        if (diff < 0) {
          nextSlide();
        } else {
          prevSlide();
        }
        autoplay = setInterval(nextSlide, 5000);
      }
    }, { passive: true });
  }
});

// FAQ Accordion Logic
function toggleFaq(button) {
  const item = button.parentElement;
  const panel = item.querySelector('.faq-panel');
  const isActive = item.classList.contains('active');
  
  // Close all other panels
  document.querySelectorAll('.faq-item').forEach(otherItem => {
    if (otherItem !== item) {
      otherItem.classList.remove('active');
      otherItem.querySelector('.faq-panel').style.maxHeight = null;
      const otherBtn = otherItem.querySelector('.faq-trigger');
      if (otherBtn) otherBtn.setAttribute('aria-expanded', 'false');
    }
  });
  
  if (isActive) {
    item.classList.remove('active');
    panel.style.maxHeight = null;
    button.setAttribute('aria-expanded', 'false');
  } else {
    item.classList.add('active');
    panel.style.maxHeight = panel.scrollHeight + "px";
    button.setAttribute('aria-expanded', 'true');
  }
}

// Intercept inline show contacts for mock listings in grid
function toggleCardContact(event, listingId) {
  if (event) event.stopPropagation();
  const phoneBtn = document.getElementById('phone-btn-' + listingId);
  const messengers = document.getElementById('messengers-' + listingId);
  if (phoneBtn && messengers) {
    if (messengers.style.display === 'none') {
      phoneBtn.style.display = 'none';
      messengers.style.display = 'flex';
    }
  }
}

// Intercept detail page openings
function openDetails(listingId, slug) {
  window.location.href = '/listings/' + listingId + '-' + slug;
}

// Recommended listings VIP carousel scrolling
function scrollVipCarousel(direction) {
  const wrap = document.getElementById('vip-carousel-track-wrap');
  if (!wrap) return;
  const slide = wrap.querySelector('.vip-carousel-slide');
  const slideWidth = slide ? slide.offsetWidth : 300;
  const step = slideWidth + 20;
  
  let targetScroll = wrap.scrollLeft + (step * direction);
  const maxScroll = wrap.scrollWidth - wrap.clientWidth;
  
  if (targetScroll < 0) {
    targetScroll = 0;
  } else if (targetScroll > maxScroll) {
    targetScroll = maxScroll;
  }
  
  wrap.scrollTo({ left: targetScroll, behavior: 'smooth' });
}

// VIP How-to Modal logic
function openVipHowtoModal() {
  const modal = document.getElementById('vip-howto-modal');
  if (modal) {
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
}

function closeVipHowtoModal(event) {
  if (event) event.stopPropagation();
  const modal = document.getElementById('vip-howto-modal');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    const modal = document.getElementById('vip-howto-modal');
    if (modal && modal.style.display === 'flex') {
      closeVipHowtoModal();
    }
  }
});

// Mobile sections toggle logic
function toggleMobileLinks(btn, citySlug) {
  const container = btn.previousElementSibling;
  const hiddenItems = container.querySelectorAll('.collapsed-link');
  const isCollapsed = hiddenItems[0].classList.contains('collapsed');
  
  hiddenItems.forEach(item => {
    if (isCollapsed) {
      item.classList.remove('collapsed');
    } else {
      item.classList.add('collapsed');
    }
  });
  
  if (isCollapsed) {
    btn.innerHTML = 'Свернуть ▴';
  } else {
    const totalCount = hiddenItems.length + 3;
    btn.innerHTML = `Показать все (${totalCount})`;
  }
}

function toggleMobileDistricts(btn) {
  const container = btn.parentElement.previousElementSibling;
  const hiddenItems = container.querySelectorAll('.mobile-collapsed-district');
  const isCollapsed = hiddenItems[0].classList.contains('collapsed');
  
  hiddenItems.forEach(item => {
    if (isCollapsed) {
      item.classList.remove('collapsed');
    } else {
      item.classList.add('collapsed');
    }
  });
  
  if (isCollapsed) {
    btn.innerHTML = 'Свернуть ▴';
  } else {
    const totalCount = hiddenItems.length + 6;
    btn.innerHTML = `Показать все районы Минска (${totalCount})`;
  }
}
</script>
@endsection
