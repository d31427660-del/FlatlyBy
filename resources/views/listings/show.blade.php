@extends('layouts.app')

@section('canonical_url', route('listings.show', ['id' => $listing->id, 'slug' => $listing->slug]))
@section('title', ($listing->title ?? 'Детали объявления') . ' в г. ' . ($listing->city ?? 'Минск') . ' — Flatly.by')
@section('meta_description', Str::limit(strip_tags($listing->description ?? 'Аренда жилья без посредников на Flatly.by'), 155))
@section('og_image', $listing->image && strlen($listing->image) > 4 ? asset($listing->image) : asset('opla.png'))

@section('head')
<style>
:root {
  --whatsapp: #25D366;
  --viber: #7360F2;
  --telegram: #0088cc;
}
/* ===== TWO-COLUMN SPLIT ===== */
.container {
  max-width: 1240px;
  margin: 0 auto;
  padding: 30px 20px 80px;
}

/* ===== TITLE SECTION ===== */
.title-section {
  margin-bottom: 24px;
}
.title-row {
  display: flex; align-items: flex-start; justify-content: space-between;
  gap: 20px;
}
.title-left h1 {
  font-size: 28px; font-weight: 700; color: var(--slate-900);
  letter-spacing: -0.5px; margin-bottom: 8px;
}
.meta-row {
  display: flex; align-items: center; gap: 12px;
  font-size: 14px; font-weight: 500;
}
.map-link {
  display: flex; align-items: center; gap: 4px;
  color: var(--slate-900); font-weight: 600; text-decoration: underline;
}
.map-link svg { width: 16px; height: 16px; fill: var(--amber); }

.fav-link {
  display: flex; align-items: center; gap: 6px;
  color: var(--gray-text); font-weight: 600; font-size: 14px;
  background: none; border: none; padding: 0;
  transition: color .15s;
}
.fav-link:hover { color: var(--amber-dark); }
.fav-link svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 2; }
.fav-link.active { color: var(--red); }
.fav-link.active svg { fill: var(--red); stroke: var(--red); }

/* ===== GALLERY (CAROUSEL) ===== */
.gallery-wrapper {
  margin-bottom: 30px;
}
.main-photo-container {
  width: 100%; height: 480px;
  border-radius: 16px; overflow: hidden;
  position: relative;
  background: var(--slate-900);
  box-shadow: 0 4px 15px rgba(0,0,0,0.06);
}
.main-photo-fake {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  font-size: 140px; color: #FFF;
  transition: background 0.3s;
}
.thumbnails-row {
  display: flex; gap: 10px; margin-top: 12px;
  overflow-x: auto; scrollbar-width: none;
}
.thumbnails-row::-webkit-scrollbar { display: none; }
.thumb-item {
  width: 80px; height: 60px; border-radius: 8px;
  overflow: hidden; cursor: pointer; flex-shrink: 0;
  border: 2px solid transparent; opacity: 0.6;
  transition: all .2s;
}
.thumb-item.active {
  border-color: var(--amber); opacity: 1;
}
.thumb-fake-img {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  font-size: 24px; color: #FFF;
}

/* ===== TWO-COLUMN SPLIT ===== */
.split-layout {
  display: grid;
  grid-template-columns: 2fr 360px;
  gap: 30px;
  align-items: start;
}

/* ===== LEFT COLUMN ===== */
.left-col {
  display: flex; flex-direction: column; gap: 24px;
}

/* Section Card */
.section-card {
  background: var(--white);
  border-radius: 14px; border: 1px solid var(--border);
  padding: 28px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.02);
  scroll-margin-top: 160px;
}
.section-card h2, .section-card h3 {
  font-size: 20px; font-weight: 700; color: var(--slate-900);
  margin-bottom: 20px;
}

/* Badges strip */
.badges-card {
  display: grid; grid-template-columns: repeat(5, 1fr);
  padding: 20px 0; text-align: center;
  border-bottom: 1px solid var(--border);
  background: var(--white);
  border-radius: 14px; border: 1px solid var(--border);
}
.badge-col {
  display: flex; flex-direction: column; align-items: center; gap: 4px;
  position: relative;
}
.badge-col:not(:last-child)::after {
  content: ''; position: absolute; right: 0; top: 10%; height: 80%; width: 1px;
  background: var(--border);
}
.badge-val { font-size: 18px; font-weight: 700; color: var(--slate-900); }
.badge-lbl { font-size: 12px; color: var(--gray-text); font-weight: 500; }

/* Beds Card */
.beds-card {
  background: var(--white);
  border-radius: 14px; border: 1px solid var(--border);
  padding: 24px;
}
.beds-title {
  font-size: 15px; font-weight: 700; color: var(--slate-900);
  margin-bottom: 14px;
}
.beds-grid { display: flex; flex-wrap: wrap; gap: 12px; }
.bed-item {
  display: flex; align-items: center; gap: 10px;
  background: var(--slate-50);
  border: 1px solid var(--border);
  border-radius: 10px; padding: 12px 18px;
  font-size: 13px; font-weight: 600;
}
.bed-item svg { width: 18px; height: 18px; fill: var(--gray-text); }
.bed-count { color: var(--amber-dark); font-weight: 700; margin-left: auto; padding-left: 8px; }

/* Tabs Strip */
.tab-strip {
  display: flex; background: var(--white);
  border-radius: 10px; border: 1px solid var(--border);
  height: 52px; align-items: center; padding: 0 10px;
  position: sticky; top: 96px; z-index: 900;
  box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}
.tab-btn {
  flex: 1; height: 100%; border: none; background: none;
  font-size: 13px; font-weight: 600; color: var(--gray-text);
  display: flex; align-items: center; justify-content: center;
  position: relative; transition: color .15s;
}
.tab-btn:hover { color: var(--slate-900); }
.tab-btn.active {
  color: var(--amber-dark);
}
.tab-btn.active::after {
  content: ''; position: absolute; bottom: 0; left: 15%; width: 70%; height: 3px;
  background: var(--amber); border-radius: 2px;
}

/* Описание */
.desc-text {
  font-size: 14px; line-height: 1.65; color: var(--slate-900);
  white-space: pre-line;
}

/* Характеристики */
.char-grid {
  display: grid; grid-template-columns: 1fr 1fr; gap: 20px;
}
.char-block {
  display: flex; flex-direction: column; gap: 8px;
}
.char-label { font-size: 12px; font-weight: 600; color: var(--gray-text); text-transform: uppercase; letter-spacing: 0.5px; }
.char-value {
  display: flex; align-items: center; gap: 10px;
  background: var(--slate-50);
  border: 1px solid var(--border);
  border-radius: 8px; padding: 12px;
  font-size: 13px; font-weight: 600;
}
.char-value svg { width: 16px; height: 16px; stroke: var(--gray-text); fill: none; stroke-width: 2; flex-shrink: 0; }
.char-value span { color: var(--slate-900); }

/* Удобства */
.amenities-group {
  margin-bottom: 24px;
}
.amenities-group:last-child { margin-bottom: 0; }
.amenity-lbl-sub { font-size: 13px; font-weight: 700; color: var(--gray-text); margin-bottom: 12px; }
.amenity-items-grid {
  display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
}
.amenity-card {
  display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 500;
}
.amenity-card svg { width: 18px; height: 18px; stroke: var(--gray-text); fill: none; stroke-width: 2; }
.all-amenities-btn {
  margin-top: 10px; background: var(--amber); color: var(--slate-900);
  border: none; border-radius: 20px; padding: 10px 24px;
  font-size: 13px; font-weight: 700; transition: background .15s, color .15s;
}
.all-amenities-btn:hover { background: var(--amber-dark); color: #FFF; }

/* Правила и Условия */
.rules-times {
  display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;
}
.time-card {
  background: var(--amber-pale);
  border: 1px solid rgba(245, 166, 35, 0.15);
  border-radius: 10px; padding: 14px 20px;
}
.time-card span { font-size: 11px; font-weight: 700; color: var(--amber-dark); text-transform: uppercase; letter-spacing: 0.8px; }
.time-card h4 { font-size: 16px; font-weight: 700; color: var(--slate-900); margin-top: 4px; }

.rules-split {
  display: grid; grid-template-columns: 1fr 1.2fr; gap: 24px;
}
.rules-column h4 { font-size: 14px; font-weight: 700; color: var(--slate-900); margin-bottom: 14px; }
.rule-list { list-style: none; display: flex; flex-direction: column; gap: 10px; }
.rule-list-item { display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 500; }
.rule-list-item svg { width: 16px; height: 16px; fill: none; stroke: var(--gray-text); stroke-width: 2; }
.rule-list-item.ok svg { stroke: var(--amber-dark); }

/* Оплата */
.payment-cards {
  display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;
}
.pay-card {
  background: var(--slate-50);
  border: 1px solid var(--border);
  border-radius: 10px; padding: 18px 12px; text-align: center;
  font-size: 13px; font-weight: 700; color: var(--slate-900);
  display: flex; flex-direction: column; align-items: center; gap: 8px;
}
.pay-card svg { width: 22px; height: 22px; fill: var(--gray-text); }

/* Отзывы */
.reviews-header {
  display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;
}
.reviews-header h3 { margin-bottom: 0; }
.write-review-btn {
  background: var(--amber); color: var(--slate-900); border: none;
  border-radius: 20px; padding: 10px 20px; font-size: 13px; font-weight: 700;
  transition: background .15s, color .15s;
}
.write-review-btn:hover { background: var(--amber-dark); color: #FFF; }

.review-item {
  border-left: 3px solid var(--amber);
  background: var(--slate-50);
  border-radius: 0 10px 10px 0; padding: 16px; margin-bottom: 14px;
  position: relative;
}
.review-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
.rev-author { display: flex; align-items: center; gap: 10px; }
.rev-avatar {
  width: 36px; height: 36px; border-radius: 50%;
  background: var(--slate-900); color: #FFF;
  display: flex; align-items: center; justify-content: center; font-size: 16px;
}
.rev-name { font-size: 13px; font-weight: 700; color: var(--slate-900); }
.rev-date { font-size: 11px; color: var(--gray-text); margin-top: 1px; }

.rev-rating-box { text-align: right; }
.rev-stars { display: flex; align-items: center; gap: 4px; font-size: 13px; font-weight: 700; color: var(--slate-900); }
.rev-stars svg { width: 14px; height: 14px; fill: var(--amber); }
.rev-elapsed { font-size: 10px; color: var(--gray-text); margin-top: 2px; }
.rev-content { font-size: 13px; line-height: 1.6; color: var(--slate-900); }

/* Карта */
.detail-map-wrap {
  height: 340px; border-radius: 16px; overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.08); margin-top: 12px;
  box-shadow: 0 4px 16px rgba(26,26,46,0.04);
  position: relative;
}
#detail-map ymaps img {
  filter: grayscale(85%) brightness(104%) contrast(95%) sepia(3%);
}
.map-external-link {
  position: absolute;
  bottom: 16px;
  left: 16px;
  z-index: 100;
  background: var(--white);
  border: 1px solid rgba(0, 0, 0, 0.12);
  border-radius: 20px;
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 700;
  color: var(--slate-900);
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  display: inline-flex;
  align-items: center;
  gap: 6px;
  text-decoration: none;
  transition: transform 0.15s, background-color 0.15s;
}
.map-external-link:hover {
  transform: scale(1.03);
  background: var(--slate-50);
  color: var(--slate-900);
}

/* ===== RIGHT COLUMN (STICKY WIDGET) ===== */
.right-col {
  position: sticky; top: 96px;
  background: var(--white);
  border: 1px solid var(--border);
  box-shadow: 0 4px 20px rgba(0,0,0,0.04);
  border-radius: 16px;
  padding: 24px;
}
.widget-address-box {
  display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px;
}
.widget-addr {
  display: flex; align-items: flex-start; gap: 8px;
  font-size: 14px; font-weight: 700; color: var(--slate-900);
}
.widget-addr svg { width: 16px; height: 16px; fill: var(--gray-text); margin-top: 2px; flex-shrink: 0; }
.widget-metro {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; font-weight: 600; color: var(--gray-text);
  padding-left: 24px;
}
.widget-metro::before {
  content: 'M'; background: #00A859; color: #FFF; font-size: 10px; font-weight: 800;
  width: 14px; height: 14px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
}

/* Gray price box */
.widget-price-card {
  background: var(--slate-50);
  border: 1px solid var(--border);
  border-radius: 12px; padding: 16px;
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 16px; gap: 8px;
}
.price-part {
  flex: 1; display: flex; flex-direction: column; gap: 2px;
}
.price-part:first-child {
  border-right: 1px solid var(--border); padding-right: 10px;
}
.price-part:last-child {
  padding-left: 10px;
}
.price-part-title {
  display: flex; align-items: center; gap: 4px;
  font-size: 10px; font-weight: 700; color: var(--gray-text);
  text-transform: uppercase;
}
.price-part-title svg { width: 12px; height: 12px; fill: none; stroke: currentColor; stroke-width: 2.2; cursor: help; }
.price-part-val { font-size: 13px; color: var(--gray-text); }
.price-part-val strong { font-size: 16px; font-weight: 800; }
.price-part-val.amber strong { color: var(--amber-dark); }
.price-part-val.black strong { color: var(--slate-900); }

/* Badges pills */
.widget-pills {
  display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px;
}
.widget-pill {
  background: var(--white);
  border: 1px solid var(--border);
  padding: 6px 12px; border-radius: 20px;
  font-size: 12px; font-weight: 600; color: var(--gray-text);
}

/* Host Panel */
.widget-host-box {
  background: var(--slate-50);
  border: 1px solid var(--border);
  border-radius: 12px; padding: 16px;
}
.widget-host-name {
  font-size: 15px; font-weight: 700; color: var(--slate-900); margin-bottom: 12px;
}
.widget-host-contacts {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 14px;
}
.widget-host-lbl { font-size: 12px; font-weight: 600; color: var(--gray-text); }
.widget-host-phone { font-size: 14px; font-weight: 700; color: var(--slate-900); font-family: monospace; }

/* Messenger circles */
.widget-messengers-row {
  display: flex; align-items: center; justify-content: space-between; gap: 8px;
}
.messenger-circle {
  width: 44px; height: 44px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  color: #FFF; transition: filter .15s;
  flex-shrink: 0;
}
.messenger-circle:hover { filter: brightness(0.9); }
.messenger-circle.wa { background: var(--whatsapp); }
.messenger-circle.vb { background: var(--viber); }
.messenger-circle.tg { background: var(--telegram); }
.messenger-circle.ph { background: var(--amber); color: var(--slate-900); }
.messenger-circle.ph:hover { color: #FFF; }
.messenger-circle svg { width: 20px; height: 20px; fill: currentColor; flex-shrink: 0; }

/* ===== LIGHTBOX MODAL OVERLAY ===== */
.lightbox-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(10, 10, 16, 0.96);
  backdrop-filter: blur(12px);
  z-index: 9999;
  display: none;
  align-items: center;
  justify-content: center;
  user-select: none;
}
.lightbox-overlay.show {
  display: flex;
}
.lightbox-close {
  position: absolute;
  top: 24px; right: 24px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 50%;
  width: 48px; height: 48px;
  display: flex; align-items: center; justify-content: center;
  color: var(--white); font-size: 24px;
  cursor: pointer; transition: all 0.2s ease;
  z-index: 10005;
}
.lightbox-close:hover {
  background: var(--amber);
  color: var(--slate-900);
  border-color: var(--amber);
  transform: rotate(90deg);
}
.lightbox-counter {
  position: absolute;
  top: 28px; left: 28px;
  color: rgba(255, 255, 255, 0.7);
  font-size: 15px; font-weight: 600;
  letter-spacing: 0.5px;
  z-index: 10005;
}
.lightbox-arrow {
  position: absolute;
  top: 50%; transform: translateY(-50%);
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 50%;
  width: 56px; height: 56px;
  display: flex; align-items: center; justify-content: center;
  color: var(--white); cursor: pointer;
  transition: all 0.25s ease;
  z-index: 10005;
}
.lightbox-arrow:hover {
  background: var(--white);
  color: var(--slate-900);
  border-color: var(--white);
  transform: translateY(-50%) scale(1.08);
  box-shadow: 0 8px 24px rgba(255, 255, 255, 0.15);
}
.lightbox-arrow.prev { left: 32px; }
.lightbox-arrow.next { right: 32px; }
.lightbox-arrow svg { width: 22px; height: 22px; fill: none; stroke: currentColor; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

.lightbox-content {
  width: 90vw;
  max-width: 820px;
  aspect-ratio: 1.25;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 24px 70px rgba(0,0,0,0.6);
  border: 1.5px solid rgba(255, 255, 255, 0.08);
  position: relative;
  background: #11111a;
  animation: lightboxScaleIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.15) forwards;
}
@keyframes lightboxScaleIn {
  from { opacity: 0; transform: scale(0.92); }
  to { opacity: 1; transform: scale(1); }
}
.lightbox-img-wrap {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  font-size: 200px;
  transition: background 0.3s ease;
}

/* ===== STICKY BOTTOM BAR (MOBILE-FIRST) ===== */
.mobile-sticky-bar {
  display: none;
  position: fixed;
  bottom: 0; left: 0; right: 0;
  background: var(--white);
  border-top: 1px solid var(--border);
  box-shadow: 0 -4px 16px rgba(0,0,0,0.06);
  padding: 16px 20px;
  align-items: center;
  justify-content: space-between;
  z-index: 1999;
}
.msb-price-box {
  display: flex;
  flex-direction: column;
}
.msb-price-label {
  font-size: 11px;
  color: var(--gray-text);
  font-weight: 600;
  text-transform: uppercase;
}
.msb-price-val {
  font-size: 14px;
  color: var(--slate-900);
}
.msb-price-val strong {
  font-size: 18px;
  font-weight: 800;
  color: var(--amber-dark);
}
.msb-contact-btn {
  background: var(--amber);
  color: var(--slate-900);
  border: none;
  border-radius: 24px;
  padding: 12px 28px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: background-color 0.15s;
}
.msb-contact-btn:hover {
  background: var(--amber-dark);
  color: var(--white);
}

/* ===== MOBILE BOTTOM DRAWER ===== */
.drawer-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(26, 26, 46, 0.4);
  backdrop-filter: blur(8px);
  z-index: 2800;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s cubic-bezier(0.19, 1, 0.22, 1);
}
.drawer-overlay.show {
  opacity: 1;
  pointer-events: auto;
}
.contact-drawer {
  position: fixed;
  bottom: 0; left: 0; right: 0;
  background: var(--white);
  border-top-left-radius: 24px;
  border-top-right-radius: 24px;
  z-index: 2900;
  padding: 20px 24px 32px;
  box-shadow: 0 -8px 32px rgba(26, 26, 46, 0.15);
  transform: translateY(100%);
  transition: transform 0.3s cubic-bezier(0.19, 1, 0.22, 1);
}
.contact-drawer.show {
  transform: translateY(0);
}
.drawer-grab-bar {
  width: 40px;
  height: 4px;
  background: var(--slate-200);
  border-radius: 2px;
  margin: -8px auto 16px;
}
.drawer-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}
.drawer-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--slate-900);
}
.drawer-close-btn {
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
.drawer-close-btn:hover {
  background: var(--slate-100);
}
.drawer-body {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.drawer-host-name {
  font-size: 15px;
  font-weight: 700;
  color: var(--slate-900);
  margin-bottom: 4px;
}
.drawer-contact-channels {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.drawer-channel {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 12px 18px;
  border-radius: 14px;
  text-decoration: none;
  transition: transform 0.15s, background-color 0.15s;
  border: 1px solid var(--border);
}
.drawer-channel:active {
  transform: scale(0.98);
}
.drawer-channel svg {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
}
.channel-info {
  display: flex;
  flex-direction: column;
}
.channel-lbl {
  font-size: 11px;
  color: var(--gray-text);
  font-weight: 600;
  text-transform: uppercase;
}
.channel-val {
  font-size: 14px;
  font-weight: 700;
  color: var(--slate-900);
}
.drawer-channel.phone {
  background: rgba(245, 166, 35, 0.08);
  border-color: rgba(245, 166, 35, 0.15);
  color: var(--amber-dark);
}
.drawer-channel.phone .channel-val {
  color: var(--amber-dark);
}
.drawer-channel.whatsapp {
  background: rgba(37, 211, 102, 0.08);
  border-color: rgba(37, 211, 102, 0.15);
  color: #25D366;
}
.drawer-channel.viber {
  background: rgba(115, 96, 242, 0.08);
  border-color: rgba(115, 96, 242, 0.15);
  color: #7360F2;
}
.drawer-channel.telegram {
  background: rgba(0, 136, 204, 0.08);
  border-color: rgba(0, 136, 204, 0.15);
  color: #0088cc;
}

/* ===== RESPONSIVE MEDIA QUERIES ===== */
@media (max-width: 991px) {
  .split-layout {
    grid-template-columns: 1fr;
    gap: 30px;
  }
  .right-col {
    position: static;
    width: 100%;
    box-sizing: border-box;
  }
  .container {
    padding: 20px 16px 100px;
  }
  .main-photo-container {
    height: 380px;
  }
  .mobile-sticky-bar {
    display: flex;
  }
}

@media (max-width: 768px) {
  .title-row {
    flex-direction: column;
    gap: 12px;
  }
  .title-right {
    width: 100%;
    display: flex;
    justify-content: flex-start;
  }
  .badges-card {
    grid-template-columns: repeat(3, 1fr);
    gap: 16px 0;
  }
  .badge-col:nth-child(3)::after, .badge-col:nth-child(5)::after {
    display: none;
  }
  .char-grid {
    grid-template-columns: 1fr;
    gap: 12px;
  }
  .amenity-items-grid {
    grid-template-columns: 1fr;
  }
  .rules-times {
    grid-template-columns: 1fr;
  }
  .rules-split {
    grid-template-columns: 1fr;
  }
  .payment-cards {
    grid-template-columns: 1fr 1fr;
  }
}

@media (max-width: 576px) {
  .title-left h1 {
    font-size: 22px;
  }
  .main-photo-container {
    height: 240px;
  }
  .thumb-item {
    width: 60px;
    height: 45px;
  }
  .section-card {
    padding: 20px;
  }
  .badges-card {
    grid-template-columns: 1fr 1fr;
    gap: 12px 0;
    border-radius: 12px;
  }
  .badge-col::after {
    display: none !important;
  }
  .tab-strip {
    display: none; /* скрываем липкую навигацию на мобильном */
  }
  .payment-cards {
    grid-template-columns: 1fr;
  }
  .reviews-header {
    flex-direction: column;
    align-items: stretch;
    gap: 12px;
  }
  .write-review-btn {
    text-align: center;
  }
  .review-top {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  .rev-rating-box {
    text-align: left;
  }
}
/* ===== GLOBAL TEXT SIZE INCREASE OVERRIDES ===== */
.listing-title { font-size: 29px !important; }
.badge-val { font-size: 19px !important; }
.badge-lbl { font-size: 13px !important; }
.descr-text { font-size: 15px !important; }
.sect-title { font-size: 21px !important; }
.amenity-lbl { font-size: 14px !important; }
.widget-host-phone { font-size: 15px !important; }
.widget-host-name { font-size: 16px !important; }
.rev-name { font-size: 14px !important; }
.rev-content { font-size: 14px !important; }
.char-label { font-size: 13px !important; }
.time-card h4 { font-size: 17px !important; }
.rules-column h4 { font-size: 15px !important; }
.rule-list-item { font-size: 14px !important; }

/* ===== SIMILAR LISTINGS SECTION ===== */
.similar-section {
  margin-top: 50px;
  padding-top: 30px;
  border-top: 1px solid var(--border);
  width: 100%;
}
.similar-title {
  font-family: 'Syne', sans-serif;
  font-size: 24px;
  font-weight: 800;
  color: var(--slate-900);
  margin-bottom: 24px;
  letter-spacing: -0.5px;
}
.similar-listings-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 28px;
}
@media (max-width: 1024px) {
  .similar-listings-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
}
@media (max-width: 768px) {
  .similar-listings-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 480px) {
  .similar-listings-grid { grid-template-columns: 1fr; }
}

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
  text-align: left;
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
</style>
<script type="text/javascript">
  // Dynamically load Yandex Maps after page load to prevent main thread blocking (TBT/FID optimization)
  window.addEventListener('load', function() {
    setTimeout(function() {
      const script = document.createElement('script');
      script.type = 'text/javascript';
      script.src = "https://api-maps.yandex.ru/2.1/?apikey={{ env('YANDEX_MAPS_API_KEY') }}&lang=ru_RU&onload=initDetailMap";
      script.async = true;
      script.defer = true;
      document.head.appendChild(script);
    }, 1500);
  });
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@type": "Accommodation",
  "name": "{{ $listing->title }}",
  "description": "{{ Str::limit(strip_tags($listing->description ?? ''), 200) }}",
  "image": "{{ $listing->image && strlen($listing->image) > 4 ? asset($listing->image) : asset('opla.png') }}",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "{{ $listing->city ?? 'Минск' }}",
    "streetAddress": "{{ $listing->address ?? '' }}"
  },
  "offers": {
    "@type": "Offer",
    "price": "{{ $listing->price }}",
    "priceCurrency": "BYN",
    "priceSpecification": {
      "@type": "UnitPriceSpecification",
      "price": "{{ $listing->price }}",
      "priceCurrency": "BYN",
      "referenceQuantity": {
        "@type": "QuantitativeValue",
        "value": "1",
        "unitCode": "DAY"
      }
    }
  }
}
</script>
@endsection

@section('content')
@php
  $typeTranslations = [
      'apartment' => 'Квартира',
      'studio' => 'Студия',
      'apartments' => 'Квартира',
      'cottage' => 'Коттедж',
      'agrotourism' => 'Коттедж',
      'hourly' => 'Почасово',
      'студия' => 'Студия',
      'квартира' => 'Квартира',
      'апартаменты' => 'Квартира',
      'коттедж' => 'Коттедж',
      'агроусадьба' => 'Коттедж',
      'почасово' => 'Почасово'
  ];
@endphp
<!-- MAIN CONTAINER -->
<main class="container">
  
  <!-- TITLE BLOCK -->
  <div class="title-section">
    <div class="title-row">
      <div class="title-left">
        <h1 id="listing-title">{{ $listing->title ?? '' }}</h1>
        <div class="meta-row">
          <a href="#section-map" class="map-link">
            <x-solar-map-point-linear width="16" height="16" />
            <span id="listing-address">{{ $listing->address ?? '' }}</span>
          </a>
        </div>
      </div>
      <button class="fav-link" id="fav-btn" onclick="toggleFav()">
        <x-solar-heart-linear width="18" height="18" />
        <span>Добавить в избранное</span>
      </button>
    </div>
  </div>

  <!-- GALLERY -->
  <div class="gallery-wrapper">
    <div class="main-photo-container" onclick="openLightbox()" style="cursor: pointer;">
      <div class="main-photo-fake" id="main-photo">🏢</div>
    </div>
    <!-- Previews slider -->
    <div class="thumbnails-row" id="thumbnails-row">
      <!-- Injected by JS -->
    </div>
  </div>

  <!-- SPLIT GRID -->
  <div class="split-layout">
    
    <!-- LEFT COLUMN -->
    <div class="left-col">
      
      <!-- Badges Card -->
      <div class="badges-card">
        @if(in_array($listing->type, ['cottage', 'agrotourism']))
          <div class="badge-col">
            <span class="badge-val">
              @php
                $displayType = 'Коттедж';
              @endphp
              {{ $displayType }}
            </span>
            <span class="badge-lbl">тип жилья</span>
          </div>
          <div class="badge-col">
            <span class="badge-val">{{ $listing->floors_in_house ?? '1' }}</span>
            <span class="badge-lbl">этажей в доме</span>
          </div>
          <div class="badge-col">
            <span class="badge-val">{{ $listing->area ?? '0' }} м²</span>
            <span class="badge-lbl">площадь дома</span>
          </div>
          <div class="badge-col">
            <span class="badge-val">{{ $listing->plot_area ?? '0' }} сот.</span>
            <span class="badge-lbl">участок</span>
          </div>
          <div class="badge-col">
            <span class="badge-val">{{ $listing->guests ?? '2' }}</span>
            <span class="badge-lbl">гостей (спальных мест: {{ $listing->sleeping_places ?? '2' }})</span>
          </div>
        @else
          <div class="badge-col">
            <span class="badge-val" id="badge-type">
              @php
                $displayType = $typeTranslations[$listing->apartment_type] ?? $typeTranslations[$listing->type] ?? $listing->apartment_type ?? $listing->type ?? 'Квартира';
                $displayType = mb_convert_case($displayType, MB_CASE_TITLE, "UTF-8");
              @endphp
              {{ $displayType }}
            </span>
            <span class="badge-lbl">тип жилья</span>
          </div>
          <div class="badge-col">
            <span class="badge-val" id="badge-floor">{{ $listing->floor ?? '1' }}/{{ $listing->total_floors ?? '5' }}</span>
            <span class="badge-lbl">этаж</span>
          </div>
          <div class="badge-col">
            <span class="badge-val" id="badge-size">{{ $listing->area ?? $listing->total_area ?? '35' }} м²</span>
            <span class="badge-lbl">площадь</span>
          </div>
          <div class="badge-col">
            <span class="badge-val" id="badge-guests">{{ $listing->guests ?? '2' }}</span>
            <span class="badge-lbl">гостей</span>
          </div>
          <div class="badge-col">
            <span class="badge-val" id="badge-beds">{{ $listing->sleeping_places ?? '2' }}</span>
            <span class="badge-lbl">спальных мест</span>
          </div>
        @endif
      </div>

      <!-- Beds Card -->
      <div class="beds-card">
        <div class="beds-title">Спальные места</div>
        <div class="beds-grid" id="beds-grid">
          <!-- Injected by JS -->
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="tab-strip">
        <a href="#section-desc" class="tab-btn active" onclick="activateTab(this)">Описание</a>
        <a href="#section-chars" class="tab-btn" onclick="activateTab(this)">Характеристики</a>
        <a href="#section-amenities" class="tab-btn" onclick="activateTab(this)">Удобства</a>
        <a href="#section-rules" class="tab-btn" onclick="activateTab(this)">Условия</a>
        <a href="#section-payment" class="tab-btn" onclick="activateTab(this)">Оплата</a>
        <a href="#section-reviews" class="tab-btn" onclick="activateTab(this)">Отзывы</a>
        <a href="#section-map" class="tab-btn" onclick="activateTab(this)">Карта</a>
      </div>

      <!-- Описание -->
      <div class="section-card" id="section-desc">
        <h2>Описание</h2>
        <div class="desc-text" id="listing-desc">
          {{ $listing->description ?? '' }}
        </div>
      </div>

      <!-- Характеристики -->
      <div class="section-card" id="section-chars">
        <h2>Характеристики</h2>
        @if(in_array($listing->type, ['cottage', 'agrotourism']))
          <div class="char-grid">
            <div class="char-block">
              <span class="char-label">Площадь дома</span>
              <div class="char-value">
                <x-solar-ruler-linear width="18" height="18" />
                <span>{{ $listing->area ?? '0' }} м²</span>
              </div>
            </div>
            
            <div class="char-block">
              <span class="char-label">Площадь участка</span>
              <div class="char-value">
                <x-solar-map-linear width="18" height="18" />
                <span>{{ $listing->plot_area ?? '0' }} соток</span>
              </div>
            </div>

            <div class="char-block">
              <span class="char-label">Этажей в доме</span>
              <div class="char-value">
                <x-solar-buildings-2-linear width="18" height="18" />
                <span>{{ $listing->floors_in_house ?? '1' }}</span>
              </div>
            </div>

            @if(!empty($listing->cottage_material))
            <div class="char-block">
              <span class="char-label">Материал дома</span>
              <div class="char-value">
                <x-solar-home-2-linear width="18" height="18" />
                <span>
                  @php
                    $materials = [
                      'brick' => 'Кирпич',
                      'wood' => 'Дерево',
                      'block' => 'Блоки',
                      'panel' => 'Панель',
                      'monolith' => 'Монолит',
                      'кирпич' => 'Кирпич',
                      'дерево' => 'Дерево',
                      'блоки' => 'Блоки'
                    ];
                  @endphp
                  {{ $materials[$listing->cottage_material] ?? $listing->cottage_material }}
                </span>
              </div>
            </div>
            @endif

            <div class="char-block">
              <span class="char-label">Тип санузла</span>
              <div class="char-value">
                <x-solar-bath-linear width="18" height="18" />
                <span>{{ $listing->layout ?: (($listing->bathrooms_count > 1) ? 'Раздельный ('.$listing->bathrooms_count.' шт.)' : 'Совмещенный') }}</span>
              </div>
            </div>

            @if($listing->banquet_hall_capacity > 0)
            <div class="char-block">
              <span class="char-label">Вместимость банкетного зала</span>
              <div class="char-value">
                <x-solar-users-group-two-rounded-linear width="18" height="18" />
                <span>До {{ $listing->banquet_hall_capacity }} человек</span>
              </div>
            </div>
            @endif

            @if(!empty($listing->parking_type))
            <div class="char-block">
              <span class="char-label">Парковка</span>
              <div class="char-value">
                <x-solar-garage-linear width="18" height="18" />
                <span>
                  @php
                    $parkings = [
                      'free_onsite' => 'Бесплатная на территории',
                      'paid_onsite' => 'Платная на территории',
                      'street' => 'Уличная рядом',
                      'none' => 'Нет парковки',
                      'бесплатная' => 'Бесплатная на территории',
                      'платная' => 'Платная на территории'
                    ];
                  @endphp
                  {{ $parkings[$listing->parking_type] ?? $listing->parking_type }}
                </span>
              </div>
            </div>
            @endif

            @if(!empty($listing->video_surveillance) && is_array($listing->video_surveillance))
            <div class="char-block" style="grid-column: span 2;">
              <span class="char-label">Видеонаблюдение</span>
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                @foreach($listing->video_surveillance as $surv)
                  <div class="char-value">
                    <x-solar-videocamera-linear width="18" height="18" />
                    <span>{{ $surv }}</span>
                  </div>
                @endforeach
              </div>
            </div>
            @endif

            @if(!empty($listing->neighborhood) && is_array($listing->neighborhood))
            <div class="char-block" style="grid-column: span 2;">
              <span class="char-label">Окрестности / Рядом</span>
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                @foreach($listing->neighborhood as $n)
                  <div class="char-value">
                    <x-solar-streets-linear width="18" height="18" />
                    <span>{{ $n }}</span>
                  </div>
                @endforeach
              </div>
            </div>
            @endif

            @if(!empty($listing->object_purpose) && is_array($listing->object_purpose))
            <div class="char-block" style="grid-column: span 2;">
              <span class="char-label">Назначение объекта</span>
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                @foreach($listing->object_purpose as $p)
                  <div class="char-value">
                    <x-solar-check-circle-linear width="18" height="18" />
                    <span>{{ $p }}</span>
                  </div>
                @endforeach
              </div>
            </div>
            @endif
          </div>
        @else
          <div class="char-grid">
            <div class="char-block">
              <span class="char-label">Тип санузла</span>
              <div class="char-value" id="char-bathroom">
                <x-solar-bath-linear width="18" height="18" />
                <span>Совмещённый</span>
              </div>
            </div>
            <div class="char-block">
              <span class="char-label">Балкон</span>
              <div class="char-value" id="char-balcony">
                <x-solar-window-frame-linear width="18" height="18" />
                <span>{{ ($listing->has_balcony ?? false) ? 'Есть балкон' : 'Нет балкона' }}</span>
              </div>
            </div>
            <div class="char-block">
              <span class="char-label">Парковка</span>
              <div class="char-value" id="char-parking">
                <x-solar-garage-linear width="18" height="18" />
                <span>Бесплатная во дворе</span>
              </div>
            </div>
            <div class="char-block">
              <span class="char-label">Вид из окна</span>
              <div class="char-value" id="char-view">
                <x-solar-eye-linear width="18" height="18" />
                <span>Во двор</span>
              </div>
            </div>
            <div class="char-block" style="grid-column: span 2;">
              <span class="char-label">На территории / Доп. удобства</span>
              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;" id="char-territory-container">
                <!-- Injected by JS -->
              </div>
            </div>
          </div>
        @endif
      </div>

      <!-- Удобства -->
      <div class="section-card" id="section-amenities">
        <h2>Удобства</h2>
        <div id="amenities-groups-container">
          <!-- Injected by JS -->
        </div>
        <button class="all-amenities-btn" onclick="showToast('Все удобства показаны в списке!')">
          Все удобства
        </button>
      </div>

      <!-- Условия заселения -->
      <div class="section-card" id="section-rules">
        <h2>Правила и условия заселения</h2>
        <div class="rules-times">
          <div class="time-card">
            <span>Заселение</span>
            <h4 id="time-checkin">{{ $listing->check_in_time ?? 'с 14:00' }}</h4>
          </div>
          <div class="time-card">
            <span>Выселение</span>
            <h4 id="time-checkout">{{ $listing->check_out_time ?? 'до 12:00' }}</h4>
          </div>
        </div>
        <div class="rules-split">
          <div class="rules-column">
            <h4>Правила размещения</h4>
            <ul class="rule-list" id="rules-hosting-list">
              <!-- Injected by JS -->
            </ul>
          </div>
          <div class="rules-column">
            <h4>Условия заселения</h4>
            <ul class="rule-list" id="rules-checking-list">
              <!-- Injected by JS -->
            </ul>
          </div>
        </div>
      </div>

      <!-- Оплата -->
      <div class="section-card" id="section-payment">
        <h2>Способы оплаты</h2>
        <div class="payment-cards" id="payment-container">
          <!-- Injected by JS -->
        </div>
      </div>

      <!-- Отзывы -->
      <div class="section-card" id="section-reviews">
        <div class="reviews-header">
          <h3>Все отзывы</h3>
          <button class="write-review-btn" onclick="openReviewModal()">Оставить отзыв</button>
        </div>
        <div id="reviews-list-container">
          <!-- Injected by JS -->
        </div>
      </div>

      <!-- Карта -->
      <div class="section-card" id="section-map">
        <h2 id="map-title-address">{{ $listing->address ?? '' }}</h2>
        <p style="color: var(--gray-text); font-size: 13px; margin-top: -10px; margin-bottom: 15px;">Точный адрес и ориентиры расположения квартиры на интерактивной карте.</p>
        <div class="detail-map-wrap">
          <div id="detail-map" style="width:100%; height:100%;"></div>
          <a id="yandex-open-btn" class="map-external-link" href="#" target="_blank">
            <x-solar-plain-linear width="14" height="14" />
            Открыть в Яндекс.Картах
          </a>
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN (STICKY CONTACT PANEL) -->
    <div class="right-col">
      
      <!-- Address Box -->
      <div class="widget-address-box">
        <div class="widget-addr">
          <x-solar-home-2-linear width="16" height="16" />
          <span id="widget-address-text">{{ $listing->address ?? '' }}</span>
        </div>
        @if(!empty($listing->nearest_metro) && $listing->nearest_metro !== 'Нет' && $listing->nearest_metro !== 'Метро не указано')
        <div class="widget-metro" id="widget-metro-text">
          {{ $listing->nearest_metro }}
        </div>
        @endif
      </div>

      <!-- Price Box -->
      <div class="widget-price-card">
        <div class="price-part">
          <div class="price-part-title">
            за сутки
            <x-solar-info-circle-linear width="14" height="14" style="color: var(--gray-text); vertical-align: middle;" />
          </div>
          <div class="price-part-val amber">
            от <strong id="price-daily" data-base-price="{{ $listing->price ?? '65' }}">{{ $listing->price ?? '65' }}</strong> <strong class="details-currency-symbol">Б̶</strong>
          </div>
        </div>
        <div class="price-part">
          <div class="price-part-title">
            за часы
            <x-solar-info-circle-linear width="14" height="14" style="color: var(--gray-text); vertical-align: middle;" />
          </div>
          <div class="price-part-val black">
            @if(isset($listing->weekend_price) && $listing->weekend_price > 0)
              <strong id="price-hourly" data-base-price="{{ $listing->weekend_price }}">{{ $listing->weekend_price }}</strong> <strong class="details-currency-symbol">Б̶</strong>
            @else
              <span id="price-hourly-na" style="font-size:12px; font-weight:700; color:var(--gray-text);">на часы не сдается</span>
            @endif
          </div>
        </div>
      </div>

      <!-- Pills Row -->
      <div class="widget-pills">
        <div class="widget-pill" id="pill-size">{{ $listing->area ?? '35' }} м²</div>
        @if(in_array($listing->type, ['cottage', 'agrotourism']))
          <div class="widget-pill" id="pill-plot">{{ $listing->plot_area ?? '0' }} сот.</div>
        @endif
        @php
          $g = $listing->guests ?? 2;
          $gText = 'гостей';
          if ($g % 10 == 1 && $g % 100 != 11) {
              $gText = 'гость';
          } elseif (in_array($g % 10, [2, 3, 4]) && !in_array($g % 100, [12, 13, 14])) {
              $gText = 'гостя';
          }
        @endphp
        <div class="widget-pill" id="pill-guests">{{ $g }} {{ $gText }}</div>
        <div class="widget-pill" id="pill-type">
          @php
            $displayPillType = $typeTranslations[$listing->apartment_type] ?? $typeTranslations[$listing->type] ?? $listing->apartment_type ?? $listing->type ?? 'Квартира';
            $displayPillType = mb_convert_case($displayPillType, MB_CASE_LOWER, "UTF-8");
          @endphp
          {{ $displayPillType }}
        </div>
      </div>

      <!-- Host Panel -->
      <div class="widget-host-box">
        <div class="widget-host-name" id="widget-host-name">{{ $listing->host_name ?? 'Частный владелец' }}</div>
        <div class="widget-host-contacts">
          <span class="widget-host-lbl">Контакты</span>
          <span class="widget-host-phone" id="widget-host-phone">{{ $listing->phone ?? '+375 29 ХХХ-ХХ-ХХ' }}</span>
        </div>
        
        <!-- Messenger icons -->
        @php
          $tgLink = \App\Helpers\ContactHelper::formatTelegramUrl($listing->telegram);
          $hasTg = !empty($tgLink);

          $waLink = \App\Helpers\ContactHelper::formatWhatsappUrl($listing->whatsapp);
          $hasWa = !empty($waLink);

          $vbLink = \App\Helpers\ContactHelper::formatViberUrl($listing->viber);
          $hasVb = !empty($vbLink);
        @endphp

        <div class="widget-messengers-row">
          @if($hasWa)
            <a href="{{ $waLink }}" class="messenger-circle wa" id="link-wa" target="_blank" onclick="revealMessenger('WhatsApp')" aria-label="Связаться через WhatsApp">
              <svg viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
            </a>
          @endif
          @if($hasVb)
            <a href="{{ $vbLink }}" class="messenger-circle vb" id="link-vb" target="_blank" onclick="revealMessenger('Viber')" aria-label="Связаться через Viber">
              <svg viewBox="0 0 24 24"><path d="M11.398.002C9.473.028 5.331.344 3.014 2.467 1.294 4.177.693 6.698.623 9.82c-.06 3.11-.13 8.95 5.5 10.541v2.42s-.038.97.602 1.17c.79.25 1.24-.499 1.99-1.299l1.4-1.58c3.85.32 6.8-.419 7.14-.529.78-.25 5.181-.811 5.901-6.652.74-6.031-.36-9.831-2.34-11.551l-.01-.002c-.6-.55-3-2.3-8.37-2.32 0 0-.396-.025-1.038-.016zm.067 1.697c.545-.003.88.02.88.02 4.54.01 6.711 1.38 7.221 1.84 1.67 1.429 2.528 4.856 1.9 9.892-.6 4.88-4.17 5.19-4.83 5.4-.28.09-2.88.73-6.152.52 0 0-2.439 2.941-3.199 3.701-.12.13-.26.17-.35.15-.13-.03-.17-.19-.16-.41l.02-4.019c-4.771-1.32-4.491-6.302-4.441-8.902.06-2.6.55-4.732 2-6.172 1.957-1.77 5.475-2.01 7.11-2.02zm.36 2.6a.299.299 0 0 0-.3.299.3.3 0 0 0 .3.3 5.631 5.631 0 0 1 4.03 1.59c1.09 1.06 1.621 2.48 1.641 4.34a.3.3 0 0 0 .3.3v-.009a.3.3 0 0 0 .3-.3 6.451 6.451 0 0 0-1.81-4.76c-1.19-1.16-2.692-1.76-4.462-1.76zm-3.954.69a.955.955 0 0 0-.615.12h-.012c-.41.24-.788.54-1.148.94-.27.32-.421.639-.461.949a1.24 1.24 0 0 0 .05.541l.02.01a13.722 13.722 0 0 0 1.2 2.6 15.383 15.383 0 0 0 2.32 3.171l.03.04.04.03.03.03.03.03a15.603 15.603 0 0 0 3.18 2.33c1.32.72 2.122 1.06 2.602 1.2v.01c.14.04.268.06.398.06a1.84 1.84 0 0 0 1.102-.472c.39-.35.7-.738.93-1.148v-.01c.23-.43.15-.841-.18-1.121a13.632 13.632 0 0 0-2.15-1.54c-.51-.28-1.03-.11-1.24.17l-.45.569c-.23.28-.65.24-.65.24l-.012.01c-3.12-.8-3.95-3.959-3.95-3.959s-.04-.43.25-.65l.56-.45c.27-.22.46-.74.17-1.25a13.522 13.522 0 0 0-1.54-2.15.843.843 0 0 0-.504-.3zm4.473.89a.3.3 0 0 0 .002.6 3.78 3.78 0 0 1 2.65 1.15 3.5 3.5 0 0 1 .9 2.57.3.3 0 0 0 .3.299l.01.012a.3.3 0 0 0 .3-.301c.03-1.19-.34-2.19-1.07-2.99-.73-.8-1.75-1.25-3.05-1.34a.3.3 0 0 0-.042 0zm.49 1.619a.305.305 0 0 0-.018.611c.99.05 1.47.55 1.53 1.58a.3.3 0 0 0 .3.29h.01a.3.3 0 0 0 .29-.32c-.07-1.34-.8-2.091-2.1-2.161a.305.305 0 0 0-.012 0z"/></svg>
            </a>
          @endif
          @if($hasTg)
            <a href="{{ $tgLink }}" class="messenger-circle tg" id="link-tg" target="_blank" onclick="revealMessenger('Telegram')" aria-label="Связаться через Telegram">
              <svg viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12l-6.87 4.326-2.96-.924c-.643-.204-.658-.643.136-.953l11.57-4.46c.537-.204.997.12.825.861z"/></svg>
            </a>
          @endif
          @if(!empty($listing->phone))
            <a href="tel:{{ $listing->phone }}" class="messenger-circle ph" id="link-phone" onclick="revealPhoneCall()" aria-label="Позвонить: {{ $listing->phone }}">
              <x-solar-phone-calling-linear width="20" height="20" />
            </a>
          @endif
        </div>
      </div>

    </div>

  </div>

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
    
    $matchedDistrictSlug = null;
    $matchedDistrictName = null;
    if (mb_strtolower($listing->city) === 'минск') {
        $addressLower = mb_strtolower($listing->address . ' ' . $listing->district . ' ' . $listing->title . ' ' . $listing->description);
        foreach ($minskDistrictsList as $slug => $name) {
            $keyword = mb_strtolower($name);
            if ($slug === 'kamennaya-gorka') $keyword = 'каменн';
            elseif ($slug === 'nemiga') $keyword = 'немиг';
            elseif ($slug === 'suharevo') $keyword = 'сухарев';
            elseif ($slug === 'zelenyy-lug') $keyword = 'зелен';
            elseif ($slug === 'malinovka') $keyword = 'малиновк';
            elseif ($slug === 'serebryanka') $keyword = 'серебрянк';
            elseif ($slug === 'vesnyanka') $keyword = 'веснянк';
            elseif ($slug === 'kuntsevschina') $keyword = 'кунцевщин';
            
            if (mb_strpos($addressLower, $keyword) !== false) {
                $matchedDistrictSlug = $slug;
                $matchedDistrictName = $name;
                break;
            }
        }
    }
  @endphp

  @if($similarListings->isNotEmpty())
    <section class="similar-section">
      <h2 class="similar-title">
        @if($matchedDistrictName)
          Похожие объявления в районе {{ $matchedDistrictName }}
        @else
          Похожие объявления в городе {{ $listing->city }}
        @endif
      </h2>
      <div class="similar-listings-grid">
        @foreach($similarListings as $similar)
          @include('listings.partials.card', ['item' => $similar])
        @endforeach
      </div>
      @if($matchedDistrictSlug)
        <div style="text-align: center; margin-top: 28px;">
          <a href="/minsk/{{ $matchedDistrictSlug }}" class="primary-action-btn" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; padding: 12px 32px; font-size: 15px; font-weight: 700; width: auto; max-width: 320px; margin: 0 auto; box-shadow: 0 4px 14px rgba(245, 166, 35, 0.25);">
            Смотреть все квартиры в {{ $matchedDistrictName === 'Немига' || $matchedDistrictName === 'Минск Мир' ? ($matchedDistrictName === 'Немига' ? 'районе Немиги' : 'Минск Мире') : 'районе ' . $matchedDistrictName }}
            <x-solar-arrow-right-linear width="16" height="16" />
          </a>
        </div>
      @endif
    </section>
  @endif

  @php
    $currentCitySlug = \App\Helpers\CityHelper::slugify($listing->city);
    $seoPagesConfig = config('seo_landing_pages');
    $cityLandingPages = $seoPagesConfig[$currentCitySlug] ?? [];
  @endphp

  @if(!empty($cityLandingPages))
    <section class="popular-queries-section" style="margin-top: 48px; border-top: 1px solid var(--border); padding-top: 40px; text-align: left;">
      <h2 class="similar-title" style="margin-bottom: 20px;">Популярные подборки жилья в г. {{ $listing->city }}</h2>
      <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 24px;">
        @foreach($cityLandingPages as $tagSlug => $pageData)
          <a href="/{{ $currentCitySlug }}/{{ $tagSlug }}" style="background: var(--white); border: 1.5px solid var(--border); border-radius: 20px; padding: 8px 18px; font-size: 13.5px; font-weight: 600; color: var(--slate-700); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.background='#fffbeb'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.background='var(--white)'; this.style.color='var(--slate-700)'">
            <span style="color: var(--amber-dark);">⚡</span>
            {{ $pageData['h1'] }}
          </a>
        @endforeach
      </div>
    </section>
  @endif

</main>

<!-- Sticky Bottom Bar for Mobile -->
<div class="mobile-sticky-bar" id="mobile-sticky-bar">
  <div class="msb-price-box">
    <div class="msb-price-label">за сутки</div>
    <div class="msb-price-val">от <strong id="price-daily-mobile" data-base-price="{{ $listing->price ?? '65' }}">{{ $listing->price ?? '65' }}</strong> <strong class="msb-currency-symbol">Б̶</strong></div>
  </div>
  <button class="msb-contact-btn" onclick="openContactDrawer()">Связаться</button>
</div>

<!-- Mobile Bottom Drawer for Contacts -->
<div class="drawer-overlay" id="contact-drawer-overlay" onclick="closeContactDrawer()"></div>
<div class="contact-drawer" id="contact-drawer">
  <div class="drawer-grab-bar"></div>
  <div class="drawer-header">
    <h3 class="drawer-title">Связаться с хозяином</h3>
    <button class="drawer-close-btn" onclick="closeContactDrawer()" aria-label="Закрыть">×</button>
  </div>
  <div class="drawer-body">
    <div class="drawer-host-name">{{ $listing->host_name ?? 'Частный владелец' }}</div>
    <div class="drawer-contact-channels">
      @if(!empty($listing->phone))
        <a href="tel:{{ $listing->phone }}" class="drawer-channel phone" onclick="revealPhoneCall();">
          <x-solar-phone-calling-linear width="22" height="22" />
          <div class="channel-info">
            <span class="channel-lbl">Позвонить по телефону</span>
            <span class="channel-val">{{ $listing->phone }}</span>
          </div>
        </a>
      @endif
      
      @if($hasWa)
        <a href="{{ $waLink }}" class="drawer-channel whatsapp" target="_blank" onclick="revealMessenger('WhatsApp');">
          <svg viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" fill="currentColor"/></svg>
          <div class="channel-info">
            <span class="channel-lbl">Написать в WhatsApp</span>
            <span class="channel-val">Открыть чат</span>
          </div>
        </a>
      @endif

      @if($hasVb)
        <a href="{{ $vbLink }}" class="drawer-channel viber" target="_blank" onclick="revealMessenger('Viber');">
          <svg viewBox="0 0 24 24"><path d="M11.398.002C9.473.028 5.331.344 3.014 2.467 1.294 4.177.693 6.698.623 9.82c-.06 3.11-.13 8.95 5.5 10.541v2.42s-.038.97.602 1.17c.79.25 1.24-.499 1.99-1.299l1.4-1.58c3.85.32 6.8-.419 7.14-.529.78-.25 5.181-.811 5.901-6.652.74-6.031-.36-9.831-2.34-11.551l-.01-.002c-.6-.55-3-2.3-8.37-2.32 0 0-.396-.025-1.038-.016zm.067 1.697c.545-.003.88.02.88.02 4.54.01 6.711 1.38 7.221 1.84 1.67 1.429 2.528 4.856 1.9 9.892-.6 4.88-4.17 5.19-4.83 5.4-.28.09-2.88.73-6.152.52 0 0-2.439 2.941-3.199 3.701-.12.13-.26.17-.35.15-.13-.03-.17-.19-.16-.41l.02-4.019c-4.771-1.32-4.491-6.302-4.441-8.902.06-2.6.55-4.732 2-6.172 1.957-1.77 5.475-2.01 7.11-2.02zm.36 2.6a.299.299 0 0 0-.3.299.3.3 0 0 0 .3.3 5.631 5.631 0 0 1 4.03 1.59c1.09 1.06 1.621 2.48 1.641 4.34a.3.3 0 0 0 .3.3v-.009a.3.3 0 0 0 .3-.3 6.451 6.451 0 0 0-1.81-4.76c-1.19-1.16-2.692-1.76-4.462-1.76zm-3.954.69a.955.955 0 0 0-.615.12h-.012c-.41.24-.788.54-1.148.94-.27.32-.421.639-.461.949a1.24 1.24 0 0 0 .05.541l.02.01a13.722 13.722 0 0 0 1.2 2.6 15.383 15.383 0 0 0 2.32 3.171l.03.04.04.03.03.03.03.03a15.603 15.603 0 0 0 3.18 2.33c1.32.72 2.122 1.06 2.602 1.2v.01c.14.04.268.06.398.06a1.84 1.84 0 0 0 1.102-.472c.39-.35.7-.738.93-1.148v-.01c.23-.43.15-.841-.18-1.121a13.632 13.632 0 0 0-2.15-1.54c-.51-.28-1.03-.11-1.24.17l-.45.569c-.23.28-.65.24-.65.24l-.012.01c-3.12-.8-3.95-3.959-3.95-3.959s-.04-.43.25-.65l.56-.45c.27-.22.46-.74.17-1.25a13.522 13.522 0 0 0-1.54-2.15.843.843 0 0 0-.504-.3zm4.473.89a.3.3 0 0 0 .002.6 3.78 3.78 0 0 1 2.65 1.15 3.5 3.5 0 0 1 .9 2.57.3.3 0 0 0 .3.299l.01.012a.3.3 0 0 0 .3-.301c.03-1.19-.34-2.19-1.07-2.99-.73-.8-1.75-1.25-3.05-1.34a.3.3 0 0 0-.042 0zm.49 1.619a.305.305 0 0 0-.018.611c.99.05 1.47.55 1.53 1.58a.3.3 0 0 0 .3.29h.01a.3.3 0 0 0 .29-.32c-.07-1.34-.8-2.091-2.1-2.161a.305.305 0 0 0-.012 0z" fill="currentColor"/></svg>
          <div class="channel-info">
            <span class="channel-lbl">Написать в Viber</span>
            <span class="channel-val">Открыть чат</span>
          </div>
        </a>
      @endif

      @if($hasTg)
        <a href="{{ $tgLink }}" class="drawer-channel telegram" target="_blank" onclick="revealMessenger('Telegram');">
          <svg viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12l-6.87 4.326-2.96-.924c-.643-.204-.658-.643.136-.953l11.57-4.46c.537-.204.997.12.825.861z" fill="currentColor"/></svg>
          <div class="channel-info">
            <span class="channel-lbl">Написать в Telegram</span>
            <span class="channel-val">Открыть чат</span>
          </div>
        </a>
      @endif
    </div>
  </div>
</div>

</main>

<!-- LIGHTBOX MODAL -->
<div class="lightbox-overlay" id="lightbox-modal" onclick="closeLightbox(event)">
  <div class="lightbox-counter" id="lightbox-counter">1 / 4</div>
  <button class="lightbox-close" onclick="closeLightbox(event)" aria-label="Закрыть полноэкранный просмотр">×</button>
  
  <button class="lightbox-arrow prev" onclick="changeLightboxPhoto(-1, event)" aria-label="Предыдущее фото">
    <x-solar-alt-arrow-left-linear width="24" height="24" />
  </button>
  
  <div class="lightbox-content" onclick="event.stopPropagation()">
    <div class="lightbox-img-wrap" id="lightbox-img-wrap">🏢</div>
  </div>
  
  <button class="lightbox-arrow next" onclick="changeLightboxPhoto(1, event)" aria-label="Следующее фото">
    <x-solar-alt-arrow-right-linear width="24" height="24" />
  </button>
</div>

<!-- REVIEW MODAL -->
<div class="lightbox-overlay" id="review-modal" onclick="closeReviewModal(event)">
  <div class="modal-card-dialog" onclick="event.stopPropagation()" style="background: #ffffff; border-radius: 20px; max-width: 480px; width: 90%; padding: 28px; box-shadow: 0 20px 40px rgba(0,0,0,0.25); position: relative;">
    <button onclick="closeReviewModal(event)" style="position: absolute; top: 18px; right: 18px; background: none; border: none; font-size: 24px; cursor: pointer; color: var(--gray-text);">&times;</button>
    <h3 style="font-size: 20px; font-weight: 800; color: var(--slate-900); margin: 0 0 6px 0;">Оставить отзыв</h3>
    <p style="font-size: 13.5px; color: var(--gray-text); margin: 0 0 20px 0;">Поделитесь вашими впечатлениями от проживания</p>

    <form id="review-form" onsubmit="submitReview(event)">
      <div style="margin-bottom: 16px;">
        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--slate-900); margin-bottom: 6px;">Ваше имя <span style="color: var(--amber-dark);">*</span></label>
        <input type="text" id="review-author-name" required placeholder="Введите ваше имя" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border); font-size: 14px; box-sizing: border-box;" value="{{ session('user_name', '') }}">
      </div>

      <div style="margin-bottom: 16px;">
        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--slate-900); margin-bottom: 6px;">Ваша оценка <span style="color: var(--amber-dark);">*</span></label>
        <div style="display: flex; gap: 8px; font-size: 28px; cursor: pointer; color: #F5A623;" id="star-rating-box">
          <span onclick="setReviewRating(1)" onmouseover="hoverReviewRating(1)" onmouseleave="resetReviewRating()">★</span>
          <span onclick="setReviewRating(2)" onmouseover="hoverReviewRating(2)" onmouseleave="resetReviewRating()">★</span>
          <span onclick="setReviewRating(3)" onmouseover="hoverReviewRating(3)" onmouseleave="resetReviewRating()">★</span>
          <span onclick="setReviewRating(4)" onmouseover="hoverReviewRating(4)" onmouseleave="resetReviewRating()">★</span>
          <span onclick="setReviewRating(5)" onmouseover="hoverReviewRating(5)" onmouseleave="resetReviewRating()">★</span>
        </div>
        <input type="hidden" id="review-rating-val" value="5">
      </div>

      <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--slate-900); margin-bottom: 6px;">Текст отзыва <span style="color: var(--amber-dark);">*</span></label>
        <textarea id="review-comment" required rows="4" placeholder="Расскажите подробнее о чистоте, уюте и общении с хозяином..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border); font-size: 14px; box-sizing: border-box; resize: vertical;"></textarea>
      </div>

      <button type="submit" id="submit-review-btn" class="write-review-btn" style="width: 100%; padding: 12px; font-size: 15px; font-weight: 700; background: var(--amber); color: var(--slate-900); border: none; border-radius: 12px; cursor: pointer; transition: background 0.15s;">
        Опубликовать отзыв
      </button>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
// Данные объявления
const data = {
  title: "{{ $listing->title ?? '' }}",
  coords: [{{ $listing->latitude ?? '53.902' }}, {{ $listing->longitude ?? '27.555' }}],
  phone: "{{ $listing->phone ?? '+375 29 ХХХ-ХХ-ХХ' }}",
  price: {{ $listing->price ?? 65 }},
  weekend_price: {{ $listing->weekend_price ?? 40 }},
  beds: {!! json_encode($listing->beds ?? [['name' => 'Двуспальная кровать', 'count' => 1, 'icon' => '🛏️']]) !!},
  photos: {!! 
    (!empty($listing->photos) && is_array($listing->photos)) 
      ? json_encode(array_map(function($p) { return ['image' => $p, 'bg' => '', 'emoji' => '']; }, $listing->photos))
      : (($listing->image ?? null) 
          ? json_encode([['image' => $listing->image, 'bg' => '', 'emoji' => '']])
          : json_encode([['bg' => 'linear-gradient(140deg,#2C3E50,#3D5A6B)', 'emoji' => '🏢'], ['bg' => 'linear-gradient(140deg,#34495E,#5D6D7E)', 'emoji' => '🛋️'], ['bg' => 'linear-gradient(140deg,#1F3A52,#2B4C7E)', 'emoji' => '🍳'], ['bg' => 'linear-gradient(140deg,#1A2B3C,#34495E)', 'emoji' => '🚿']]))
  !!},
  chars: {!! json_encode($listing->chars ?? ['bathroom' => 'Совмещённый', 'balcony' => 'Есть балкон', 'parking' => 'Бесплатная во дворе', 'view' => 'Во двор', 'territory' => ['Видеонаблюдение', 'Лифт']]) !!},
  amenities: {!! 
    (!empty($amenities) && is_array($amenities) && count($amenities) > 0)
      ? json_encode($amenities)
      : (in_array($listing->type, ['cottage', 'agrotourism'])
          ? json_encode([
              'Услуги на территории' => ['Мангал', 'Беседка', 'Парковка'],
              'Климат и коммуникации' => ['Отопление', 'Электричество', 'Горячая вода'],
              'Досуг и развлечения внутри' => ['Телевизор', 'Караоке', 'Настольные игры'],
              'Сервис и услуги' => ['Постельное белье', 'Полотенца'],
              'Развлечения' => ['Караоке']
            ])
          : json_encode([
              'Основные удобства' => ['Гладильная доска', 'Стиральная машина', 'Утюг', 'Шторы блэкаут'],
              'Кухонная зона' => ['Холодильник', 'Электрочайник', 'Посуда']
            ]))
  !!},
  rules: {!! json_encode($listing->rules ?? ['hosting' => [['text' => 'Нельзя с детьми', 'ok' => false], ['text' => 'Разрешено курить', 'ok' => true], ['text' => 'Мероприятия запрещены', 'ok' => false], ['text' => 'С животными нельзя', 'ok' => false]], 'checking' => [['text' => 'Бесконтактное заселение', 'ok' => true], ['text' => 'Отчётные документы', 'ok' => true]]]) !!},
  payments: {!! json_encode($listing->payments ?? ['Наличными', 'ЕРИП']) !!},
  reviews: {!! json_encode($listing->reviews ?? []) !!}
};

// Загрузка спальных мест
const bedsGrid = document.getElementById('beds-grid');
data.beds.forEach(bed => {
  const el = document.createElement('div');
  el.className = 'bed-item';
  el.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M2 19V5m20 14V9m-8 6h8M2 15h12m0 0v-4c0-.943 0-1.414-.293-1.707S15.057 9 14.114 9H9.886c-.943 0-1.414 0-1.707.293S7.886 10.057 7.886 11v4z"/><circle cx="6" cy="10" r="2"/></g></svg> 
    <span>${bed.name}</span>
    <span class="bed-count">× ${bed.count}</span>`;
  bedsGrid.appendChild(el);
});

// Галерея (Карусель)
const mainPhoto = document.getElementById('main-photo');
const thumbsRow = document.getElementById('thumbnails-row');

// Индекс активной фотографии в галерее
var currentPhotoIdx = 0;
var lightboxActiveIdx = 0;

// Инициализация первой активной фотографии
if (data.photos[0].image) {
  mainPhoto.style.background = 'none';
  mainPhoto.innerHTML = `<img src="${data.photos[0].image}" alt="Главное фото: ${data.title}" style="width:100%;height:100%;object-fit:cover;border-radius:14px;">`;
} else {
  mainPhoto.style.background = data.photos[0].bg;
  mainPhoto.textContent = data.photos[0].emoji;
}

// Функция переключения активной фотографии на странице
function selectThumbnail(idx) {
  currentPhotoIdx = idx;
  const photo = data.photos[idx];
  
  document.querySelectorAll('.thumb-item').forEach((t, i) => {
    if (i === idx) t.classList.add('active');
    else t.classList.remove('active');
  });
  
  if (photo.image) {
    mainPhoto.style.background = 'none';
    mainPhoto.innerHTML = `<img src="${photo.image}" alt="Фото ${idx + 1}: ${data.title}" style="width:100%;height:100%;object-fit:cover;border-radius:14px;">`;
  } else {
    mainPhoto.style.background = photo.bg;
    mainPhoto.innerHTML = '';
    mainPhoto.textContent = photo.emoji;
  }
}

data.photos.forEach((photo, idx) => {
  const el = document.createElement('div');
  el.className = 'thumb-item' + (idx === 0 ? ' active' : '');
  if (photo.image) {
    el.innerHTML = `<div class="thumb-fake-img" style="background: none"><img src="${photo.image}" alt="Превью фото ${idx + 1}: ${data.title}" style="width:100%;height:100%;object-fit:cover;border-radius:8px;"></div>`;
  } else {
    el.innerHTML = `<div class="thumb-fake-img" style="background: ${photo.bg}">${photo.emoji}</div>`;
  }
  el.addEventListener('click', function() {
    selectThumbnail(idx);
    showToast('Фотография загружена');
  });
  thumbsRow.appendChild(el);
});

if (data.photos.length <= 1) {
  thumbsRow.style.display = 'none';
}

// Открытие лайтбокса
function openLightbox() {
  lightboxActiveIdx = currentPhotoIdx;
  renderLightboxPhoto();
  document.getElementById('lightbox-modal').classList.add('show');
  document.body.style.overflow = 'hidden'; // Запретить прокрутку
  
  document.addEventListener('keydown', handleLightboxKeydown);
}

// Закрытие лайтбокса
function closeLightbox(event) {
  if (event) event.stopPropagation();
  document.getElementById('lightbox-modal').classList.remove('show');
  document.body.style.overflow = ''; // Восстановить прокрутку
  
  document.removeEventListener('keydown', handleLightboxKeydown);
}

// Смена фотографии в лайтбоксе
function changeLightboxPhoto(direction, event) {
  if (event) event.stopPropagation();
  lightboxActiveIdx += direction;
  
  if (lightboxActiveIdx < 0) {
    lightboxActiveIdx = data.photos.length - 1;
  } else if (lightboxActiveIdx >= data.photos.length) {
    lightboxActiveIdx = 0;
  }
  
  renderLightboxPhoto();
  selectThumbnail(lightboxActiveIdx);
}

// Рендеринг активного изображения в лайтбоксе
function renderLightboxPhoto() {
  const photo = data.photos[lightboxActiveIdx];
  const imgWrap = document.getElementById('lightbox-img-wrap');
  
  if (photo.image || photo.url) {
    imgWrap.innerHTML = `<img src="${photo.image || photo.url}" alt="Полноэкранное фото ${lightboxActiveIdx + 1}: ${data.title}" style="width:100%; height:100%; object-fit:cover; border-radius:8px;" />`;
    imgWrap.style.background = 'none';
  } else {
    imgWrap.innerHTML = photo.emoji;
    imgWrap.style.background = photo.bg;
  }
  
  document.getElementById('lightbox-counter').textContent = `${lightboxActiveIdx + 1} / ${data.photos.length}`;
}

// Обработка клавиш в лайтбоксе
function handleLightboxKeydown(event) {
  if (event.key === 'ArrowLeft') {
    changeLightboxPhoto(-1);
  } else if (event.key === 'ArrowRight') {
    changeLightboxPhoto(1);
  } else if (event.key === 'Escape') {
    closeLightbox();
  }
}

// Дополнительные характеристики
const charBathroomEl = document.getElementById('char-bathroom');
if (charBathroomEl) charBathroomEl.querySelector('span').textContent = data.chars.bathroom || 'Совмещённый';

const charBalconyEl = document.getElementById('char-balcony');
if (charBalconyEl) charBalconyEl.querySelector('span').textContent = data.chars.balcony || 'Есть балкон';

const charParkingEl = document.getElementById('char-parking');
if (charParkingEl) charParkingEl.querySelector('span').textContent = data.chars.parking || 'Бесплатная во дворе';

const charViewEl = document.getElementById('char-view');
if (charViewEl) charViewEl.querySelector('span').textContent = data.chars.view || 'Во двор';

const territoryContainer = document.getElementById('char-territory-container');
if (territoryContainer && data.chars.territory && Array.isArray(data.chars.territory)) {
  data.chars.territory.forEach(item => {
    const el = document.createElement('div');
    el.className = 'char-value';
    el.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.5" d="m9 12l2 2l4-4m6 2a9 9 0 1 1-18 0a9 9 0 0 1 18 0"/></svg> <span>${item}</span>`;
    territoryContainer.appendChild(el);
  });
}

// Удобства
const amenitiesContainer = document.getElementById('amenities-groups-container');
if (Object.keys(data.amenities).length === 0 || (Object.keys(data.amenities).every(k => data.amenities[k].length === 0))) {
  amenitiesContainer.innerHTML = '<div style="color:var(--gray-text); font-size:13px;">Удобства не указаны</div>';
} else {
  Object.keys(data.amenities).forEach(groupName => {
    if (data.amenities[groupName].length === 0) return;
    const groupEl = document.createElement('div');
    groupEl.className = 'amenities-group';
    groupEl.innerHTML = `<div class="amenity-lbl-sub">${groupName}</div>`;
    
    const itemsGrid = document.createElement('div');
    itemsGrid.className = 'amenity-items-grid';
    
    data.amenities[groupName].forEach(item => {
      const card = document.createElement('div');
      card.className = 'amenity-card';
      card.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.5" d="m9 12l2 2l4-4m6 2a9 9 0 1 1-18 0a9 9 0 0 1 18 0"/></svg> <span>${item}</span>`;
      itemsGrid.appendChild(card);
    });
    
    groupEl.appendChild(itemsGrid);
    amenitiesContainer.appendChild(groupEl);
  });
}

// Правила размещения
const rulesHostingList = document.getElementById('rules-hosting-list');
data.rules.hosting.forEach(rule => {
  const el = document.createElement('li');
  el.className = 'rule-list-item' + (rule.ok ? ' ok' : '');
  el.innerHTML = rule.ok ? 
    `<svg width="16" height="16" viewBox="0 0 24 24" style="color: var(--amber-dark);"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12l5 5L20 7"/></svg> <span>${rule.text}</span>` : 
    `<svg width="16" height="16" viewBox="0 0 24 24" style="color: var(--gray-text);"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m18 6l-12 12M6 6l12 12"/></svg> <span>${rule.text}</span>`;
  rulesHostingList.appendChild(el);
});

// Условия заселения
const rulesCheckingList = document.getElementById('rules-checking-list');
data.rules.checking.forEach(rule => {
  const el = document.createElement('li');
  el.className = 'rule-list-item ok';
  el.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" style="color: var(--amber-dark);"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12l5 5L20 7"/></svg> <span>${rule.text}</span>`;
  rulesCheckingList.appendChild(el);
});

// Способы оплаты
const paymentContainer = document.getElementById('payment-container');
data.payments.forEach(pay => {
  const el = document.createElement('div');
  el.className = 'pay-card';
  el.innerHTML = `<svg width="22" height="22" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.5"><rect width="20" height="14" x="2" y="5" rx="3"/><path stroke-linecap="round" d="M2 10h20m-16 4h4"/></g></svg> <span>${pay}</span>`;
  paymentContainer.appendChild(el);
});

// Отзывы
const reviewsContainer = document.getElementById('reviews-list-container');
if (data.reviews.length === 0) {
  reviewsContainer.innerHTML = `
    <div style="text-align: center; padding: 40px 20px; color: var(--gray-text);">
      <svg width="48" height="48" viewBox="0 0 24 24" style="margin: 0 auto 12px; display: block; opacity: 0.6;">
        <path fill="none" stroke="currentColor" stroke-width="1.5" d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2S2 6.477 2 12c0 1.821.487 3.53 1.338 5L2.5 21.5l4.5-.838A9.955 9.955 0 0 0 12 22"/>
      </svg>
      <div style="font-size: 14px; font-weight: 600;">Отзывов пока нет</div>
      <div style="font-size: 12px; margin-top: 4px;">Вы можете оставить первый отзыв после проживания!</div>
    </div>
  `;
} else {
  data.reviews.forEach(rev => {
    const el = document.createElement('div');
    el.className = 'review-item';
    el.innerHTML = `
      <div class="review-top">
        <div class="rev-author">
          <div class="rev-avatar">${rev.name.substring(0, 1).toUpperCase()}</div>
          <div>
            <div class="rev-name">${rev.name}</div>
            <div class="rev-date">${rev.date}</div>
          </div>
        </div>
        <div class="rev-rating-box">
          <div class="rev-stars">
            <svg width="14" height="14" viewBox="0 0 24 24"><path fill="currentColor" d="m12 2l3.09 6.26L22 9.27l-5 4.87l1.18 6.88L12 17.77l-6.18 3.25L7 14.14l-5-4.87l6.91-1.01z"/></svg>
            ${rev.rating}
          </div>
          <div class="rev-elapsed">${rev.elapsed}</div>
        </div>
      </div>
      <div class="rev-content">${rev.content}</div>
    `;
    reviewsContainer.appendChild(el);
  });
}

// Избранное
function toggleFav(btn) {
  if (btn) {
    event.stopPropagation();
    const card = btn.closest('.card');
    if (!card) return;
    const id = parseInt(card.getAttribute('data-id'), 10);
    const isAdded = toggleFavoriteId(id);
    
    btn.classList.toggle('active', isAdded);
    showToast(isAdded ? '❤️ Добавлено в избранное' : 'Удалено из избранного');
  } else {
    const mainBtn = document.getElementById('fav-btn');
    if (!mainBtn) return;
    const isAdded = toggleFavoriteId({{ $listing->id }});
    
    mainBtn.classList.toggle('active', isAdded);
    const textSpan = mainBtn.querySelector('span');
    if (textSpan) {
      textSpan.textContent = isAdded ? 'В избранном' : 'Добавить в избранное';
    }
    showToast(isAdded ? '❤️ Добавлено в избранное!' : 'Удалено из избранного');
  }
}

// Карточки: открытие деталей, контакты, карусель картинок
function openDetails(listingId, slug) {
  window.location.href = '/listings/' + listingId + '-' + slug;
}

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

function centerMapOnListing(event, lat, lng, id) {
  if (event) event.stopPropagation();
  const card = document.querySelector(`.card[data-id="${id}"]`);
  if (card) {
    const slug = card.getAttribute('data-slug');
    openDetails(id, slug);
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const btn = document.getElementById('fav-btn');
  const favs = getFavorites();
  if (btn) {
    const isFav = favs.includes({{ $listing->id }});
    btn.classList.toggle('active', isFav);
    const textSpan = btn.querySelector('span');
    if (textSpan) {
      textSpan.textContent = isFav ? 'В избранном' : 'Добавить в избранное';
    }
  }

  // Подсвечиваем похожие объявления, которые уже в избранном
  document.querySelectorAll('.similar-listings-grid .card').forEach(card => {
    const id = parseInt(card.getAttribute('data-id'), 10);
    if (favs.includes(id)) {
      const cardFavBtn = card.querySelector('.fav-btn');
      if (cardFavBtn) {
        cardFavBtn.classList.add('active');
      }
    }
  });

  // Инициализация каруселей и тач-событий (свайпов) для похожих объявлений
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

// Звонок
function trackContactClick(type) {
  fetch(`/listings/{{ $listing->id }}/track`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({ type: type })
  }).catch(err => console.error('Tracking error:', err));
}

function revealPhoneCall() {
  showToast('📞 Инициируем вызов на номер хозяина: {{ $listing->phone }}');
  trackContactClick('phone');
}

function revealMessenger(name) {
  showToast('💬 Открываем ' + name + '...');
  trackContactClick(name.toLowerCase());
}

// Таб-навигация
function activateTab(btn) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  
  // Если перешли на вкладку карты, корректируем размеры контейнера карты Яндекса
  if (btn.getAttribute('href') === '#section-map' && detailMap) {
    setTimeout(function() {
      detailMap.container.fitToViewport();
    }, 50);
  }
}

// Яндекс.Карта
let detailMap;
window.initDetailMap = function() {
  if (typeof ymaps !== 'undefined') {
    ymaps.ready(function() {
      if (data.coords) {
        const ymapsBtn = document.getElementById('yandex-open-btn');
        if (ymapsBtn) {
          ymapsBtn.href = `https://yandex.ru/maps/?text=${data.coords[0]},${data.coords[1]}`;
        }

        detailMap = new ymaps.Map("detail-map", {
          center: data.coords,
          zoom: 14,
          controls: ['zoomControl']
        });
        
        var myCircle = new ymaps.Circle([data.coords, 350], {
          balloonContent: "Приблизительная область расположения"
        }, {
          fillColor: "rgba(245, 166, 35, 0.15)",
          strokeColor: "#F5A623",
          strokeOpacity: 0.7,
          strokeWidth: 1.5
        });
        
        detailMap.geoObjects.add(myCircle);

        // Авто-ресайз карты при изменении размеров экрана
        window.addEventListener('resize', function() {
          if (detailMap) {
            detailMap.container.fitToViewport();
          }
        });
      }
    });
  }
};

// Отслеживание скролла для активации вкладок таб-бара (оптимизировано против Forced Reflow / Layout Thrashing)
window.addEventListener('scroll', function() {
  const scrollPos = window.scrollY + 160;
  const sections = ['section-desc', 'section-chars', 'section-amenities', 'section-rules', 'section-payment', 'section-reviews', 'section-map'];
  
  let activeSectionId = null;
  
  // Шаг 1: Сначала только читаем координаты (без записи стилей)
  for (let i = 0; i < sections.length; i++) {
    const el = document.getElementById(sections[i]);
    if (el) {
      const top = el.offsetTop;
      const height = el.offsetHeight;
      if (scrollPos >= top && scrollPos < top + height) {
        activeSectionId = sections[i];
        break;
      }
    }
  }
  
  // Шаг 2: Вносим изменения в DOM (только запись)
  if (activeSectionId) {
    document.querySelectorAll('.tab-strip a').forEach(a => {
      const isTarget = a.getAttribute('href') === '#' + activeSectionId;
      if (isTarget) {
        if (!a.classList.contains('active')) {
          a.classList.add('active');
        }
      } else {
        if (a.classList.contains('active')) {
          a.classList.remove('active');
        }
      }
    });
  }
});



// Глобальная функция обновления цен в соответствии с выбранной валютой
function updateCurrencyDisplay() {
  try {
    const currentCurrency = localStorage.getItem('flatly_currency') || 'BYN';
    const rate = EXCHANGE_RATES[currentCurrency] || 1.0;
    const symbol = CURRENCY_SYMBOLS[currentCurrency] || 'Б̶';
    
    const dailyEl = document.getElementById('price-daily');
    if (dailyEl && typeof data.price !== 'undefined') {
      dailyEl.textContent = Math.round(data.price * rate);
      const labelEl = dailyEl.nextElementSibling;
      if (labelEl) labelEl.textContent = symbol;
    }

    const dailyElMobile = document.getElementById('price-daily-mobile');
    if (dailyElMobile && typeof data.price !== 'undefined') {
      dailyElMobile.textContent = Math.round(data.price * rate);
      const labelElMobile = dailyElMobile.nextElementSibling;
      if (labelElMobile) labelElMobile.textContent = symbol;
    }
    
    const hourlyEl = document.getElementById('price-hourly');
    if (hourlyEl && data.weekend_price > 0) {
      hourlyEl.textContent = Math.round(data.weekend_price * rate);
      const labelEl = hourlyEl.nextElementSibling;
      if (labelEl) labelEl.textContent = symbol;
    }
  } catch (e) {
    console.error('Error updating currency display on details page', e);
  }
}

// Drawer functions
function openContactDrawer() {
  document.getElementById('contact-drawer-overlay').classList.add('show');
  document.getElementById('contact-drawer').classList.add('show');
  document.body.style.overflow = 'hidden';
}
function closeContactDrawer() {
  document.getElementById('contact-drawer-overlay').classList.remove('show');
  document.getElementById('contact-drawer').classList.remove('show');
  document.body.style.overflow = '';
}

// Запускаем конвертацию при загрузке страницы
updateCurrencyDisplay();

// ===== REVIEW MODAL & SUBMISSION LOGIC =====
let selectedReviewRating = 5;

function openReviewModal() {
  const modal = document.getElementById('review-modal');
  if (modal) {
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    setReviewRating(5);
  }
}

function closeReviewModal(event) {
  if (event) event.stopPropagation();
  const modal = document.getElementById('review-modal');
  if (modal) {
    modal.classList.remove('show');
    document.body.style.overflow = '';
  }
}

function setReviewRating(rating) {
  selectedReviewRating = rating;
  document.getElementById('review-rating-val').value = rating;
  renderReviewStars(rating);
}

function hoverReviewRating(rating) {
  renderReviewStars(rating);
}

function resetReviewRating() {
  renderReviewStars(selectedReviewRating);
}

function renderReviewStars(count) {
  const stars = document.querySelectorAll('#star-rating-box span');
  stars.forEach((star, index) => {
    if (index < count) {
      star.style.color = '#F5A623';
    } else {
      star.style.color = '#cbd5e1';
    }
  });
}

function submitReview(event) {
  event.preventDefault();
  const name = document.getElementById('review-author-name').value.trim();
  const rating = document.getElementById('review-rating-val').value;
  const comment = document.getElementById('review-comment').value.trim();
  const btn = document.getElementById('submit-review-btn');

  if (!name || !comment) {
    showToast('Пожалуйста, заполните все поля');
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Публикация...';

  fetch('{{ route("listings.reviews.store", $listing->id) }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      author_name: name,
      rating: parseInt(rating),
      comment: comment
    })
  })
  .then(res => res.json())
  .then(res => {
    btn.disabled = false;
    btn.textContent = 'Опубликовать отзыв';
    if (res.success) {
      closeReviewModal();
      showToast(res.message || 'Отзыв успешно опубликован!');
      
      // Append review to list
      const rev = res.review;
      const reviewsContainer = document.getElementById('reviews-list-container');
      const newEl = document.createElement('div');
      newEl.className = 'review-item';
      newEl.style.animation = 'fadeIn 0.4s ease';
      newEl.innerHTML = `
        <div class="review-top">
          <div class="rev-author">
            <div class="rev-avatar">${rev.name.substring(0, 1).toUpperCase()}</div>
            <div>
              <div class="rev-name">${rev.name}</div>
              <div class="rev-date">${rev.date}</div>
            </div>
          </div>
          <div class="rev-rating-box">
            <div class="rev-stars">
              <svg width="14" height="14" viewBox="0 0 24 24"><path fill="currentColor" d="m12 2l3.09 6.26L22 9.27l-5 4.87l1.18 6.88L12 17.77l-6.18 3.25L7 14.14l-5-4.87l6.91-1.01z"/></svg>
              ${rev.rating}
            </div>
            <div class="rev-elapsed">${rev.elapsed}</div>
          </div>
        </div>
        <div class="rev-content">${rev.content}</div>
      `;

      // Clear empty message if exists
      if (reviewsContainer.innerText.includes('Отзывов пока нет')) {
        reviewsContainer.innerHTML = '';
      }
      reviewsContainer.insertBefore(newEl, reviewsContainer.firstChild);

      // Reset comment field
      document.getElementById('review-comment').value = '';
    } else {
      showToast(res.message || 'Ошибка при сохранении отзыва');
    }
  })
  .catch(err => {
    btn.disabled = false;
    btn.textContent = 'Опубликовать отзыв';
    showToast('Ошибка сети при отправке отзыва');
  });
}
</script>
@endsection
