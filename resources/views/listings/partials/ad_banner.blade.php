@once
<style>
.ad-banner {
  grid-column: 1 / -1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  background: var(--white, #ffffff);
  border: 2px solid var(--amber, #F5A623);
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
  text-decoration: none;
  color: inherit;
  position: relative;
  overflow: hidden;
  transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, border-color 0.2s ease;
  min-height: 280px;
  box-sizing: border-box;
  margin-bottom: 8px;
}

.ad-banner:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 28px rgba(245, 166, 35, 0.12);
  border-color: var(--amber-dark, #C47D0C);
}

.ad-banner-top {
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

.ad-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--slate-900, #1A1A2E);
  color: var(--white, #ffffff);
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 1px;
  box-shadow: 0 4px 10px rgba(26, 26, 46, 0.15);
}

.ad-badge svg {
  width: 14px;
  height: 14px;
  stroke: var(--amber, #F5A623);
  fill: none;
}

.ad-banner-main {
  max-width: 100%;
  margin-top: 24px;
  margin-bottom: 32px;
  text-align: left;
}

.ad-banner-title {
  font-family: 'Syne', 'Inter', sans-serif;
  font-size: 38px;
  font-weight: 800;
  line-height: 1.15;
  color: var(--slate-900, #1A1A2E);
  letter-spacing: -1px;
  margin-bottom: 24px;
}

.ad-banner-title span {
  background: linear-gradient(135deg, var(--amber-dark, #C47D0C) 0%, var(--amber, #F5A623) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.ad-banner-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--slate-900, #1A1A2E);
  color: var(--white, #ffffff);
  font-size: 14px;
  font-weight: 700;
  padding: 12px 28px;
  border-radius: 30px;
  box-shadow: 0 6px 18px rgba(26, 26, 46, 0.2);
  transition: transform 0.15s, box-shadow 0.15s, background-color 0.15s, color 0.15s;
}

.ad-banner:hover .ad-banner-btn {
  background: var(--amber, #F5A623);
  color: var(--slate-900, #1A1A2E);
  box-shadow: 0 6px 18px rgba(245, 166, 35, 0.3);
}

.ad-banner-btn svg {
  width: 16px;
  height: 16px;
  stroke: currentColor;
  stroke-width: 2.5;
  transition: transform 0.15s;
}

.ad-banner-btn:hover svg {
  transform: translate(2px, -2px);
}

.ad-banner-bottom {
  border-top: 1.5px solid var(--border, #E8E8F0);
  padding-top: 20px;
  margin-top: auto;
}

.ad-features-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.ad-feature-item {
  display: flex;
  align-items: center;
  gap: 10px;
  text-align: left;
}

.ad-feature-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(196, 125, 12, 0.08);
  color: var(--amber-dark, #C47D0C);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: background-color 0.2s, color 0.2s;
}

.ad-banner:hover .ad-feature-icon {
  background: rgba(245, 166, 35, 0.12);
  color: var(--amber-dark, #C47D0C);
}

.ad-feature-icon svg {
  width: 18px;
  height: 18px;
  stroke: currentColor;
}

.ad-feature-text {
  font-size: 12px;
  font-weight: 600;
  color: var(--gray-text, #6B6B80);
  line-height: 1.35;
}

/* Responsive design */
@media (max-width: 991px) {
  .ad-banner {
    padding: 24px;
    min-height: auto;
  }
  .ad-banner-main {
    max-width: 100%;
  }
  .ad-banner-title {
    font-size: 30px;
  }
  .ad-features-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 16px 20px;
  }
}

@media (max-width: 576px) {
  .ad-banner {
    padding: 20px;
  }
  .ad-banner-title {
    font-size: 24px;
    margin-bottom: 20px;
  }
  .ad-banner-btn {
    padding: 10px 22px;
    font-size: 13px;
  }
  .ad-features-grid {
    grid-template-columns: 1fr;
    gap: 12px;
  }
  .ad-feature-item {
    gap: 8px;
  }
  .ad-feature-icon {
    width: 30px;
    height: 30px;
  }
  .ad-feature-icon svg {
    width: 15px;
    height: 15px;
  }
  .ad-feature-text {
    font-size: 11.5px;
  }
}
</style>
@endonce

<a href="https://t.me/byejey1" target="_blank" class="ad-banner" data-page="{{ $pageNumber ?? 1 }}" style="{{ isset($visibleOnLoad) && !$visibleOnLoad ? 'display: none !important;' : '' }}">
  <div class="ad-banner-top">
    <div class="ad-badge">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M11 5L6 9H2v6h4l5 4V5z"/>
        <path d="M15.54 8.46a5 5 0 0 1 0 7.07"/>
        <path d="M19.07 4.93a10 10 0 0 1 0 14.14"/>
      </svg>
      Реклама
    </div>
  </div>
  
  <div class="ad-banner-main">
    <div class="ad-banner-title">Здесь может быть <br><span>ваша реклама</span></div>
    <div class="ad-banner-btn">
      Разместить сейчас
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="7" y1="17" x2="17" y2="7"/>
        <polyline points="7 7 17 7 17 17"/>
      </svg>
    </div>
  </div>
  
  <div class="ad-banner-bottom">
    <div class="ad-features-grid">
      <div class="ad-feature-item">
        <div class="ad-feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <circle cx="12" cy="12" r="6"/>
            <circle cx="12" cy="12" r="2"/>
          </svg>
        </div>
        <div class="ad-feature-text">Привлекайте внимание</div>
      </div>
      
      <div class="ad-feature-item">
        <div class="ad-feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="20" x2="18" y2="10"/>
            <line x1="12" y1="20" x2="12" y2="4"/>
            <line x1="6" y1="20" x2="6" y2="14"/>
          </svg>
        </div>
        <div class="ad-feature-text">Увеличивайте продажи</div>
      </div>
      
      <div class="ad-feature-item">
        <div class="ad-feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            <polyline points="9 11 11 13 15 9"/>
          </svg>
        </div>
        <div class="ad-feature-text">Надёжный партнёр</div>
      </div>
      
      <div class="ad-feature-item">
        <div class="ad-feature-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <div class="ad-feature-text">Больше клиентов — больше возможностей</div>
      </div>
    </div>
  </div>
</a>
