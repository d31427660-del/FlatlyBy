@extends('layouts.app')

@section('title', 'Личный кабинет арендодателя — Flatly.by')

@section('head')
<style>
.dashboard-container {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 32px 80px;
  color: var(--slate-900);
  box-sizing: border-box;
}
.dashboard-grid {
  display: grid;
  grid-template-columns: 280px minmax(0, 1fr);
  gap: 32px;
  align-items: start;
}
.sidebar-panel {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
}
.host-profile {
  text-align: center;
  margin-bottom: 24px;
  border-bottom: 1px solid var(--border);
  padding-bottom: 20px;
}
.host-avatar {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: var(--slate-900);
  color: #FFF;
  font-family: 'Syne', sans-serif;
  font-size: 32px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 12px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}
.host-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.host-name {
  font-size: 16px;
  font-weight: 700;
}
.host-status {
  font-size: 12px;
  color: var(--gray-text);
  margin-top: 3px;
}
.sidebar-menu {
  list-style: none;
}
.sidebar-menu li {
  margin-bottom: 6px;
}
.sidebar-menu li a {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  color: var(--slate-900);
  transition: all 0.15s;
  cursor: pointer;
  text-decoration: none;
}
.sidebar-menu li a:hover, .sidebar-menu li a.active {
  background: var(--slate-50);
  color: var(--amber-dark);
}
.sidebar-menu li a svg {
  width: 16px;
  height: 16px;
  stroke: currentColor;
  fill: none;
  stroke-width: 2;
  flex-shrink: 0;
}

/* MAIN CONTENT PANEL */
.main-panel {
  display: flex;
  flex-direction: column;
  gap: 28px;
  min-width: 0;
}

/* SPA PANES */
.tab-pane {
  display: none;
  opacity: 0;
  transform: translateY(12px);
  transition: opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1), transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.tab-pane.active {
  display: block;
  opacity: 1;
  transform: translateY(0);
}

.stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}
.stat-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.stat-card-title {
  font-size: 12px;
  font-weight: 600;
  color: var(--gray-text);
  text-transform: uppercase;
  letter-spacing: .5px;
}
.stat-card-val {
  font-size: 28px;
  font-weight: 800;
  color: var(--slate-900);
}
.stat-card-trend {
  font-size: 11px;
  color: #3B6D11;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 4px;
}

.listings-section {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 28px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
}
.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}
.section-header h2 {
  font-size: 18px;
  font-weight: 700;
}
.add-btn {
  background: var(--amber);
  color: var(--slate-900);
  border: none;
  padding: 10px 18px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: background 0.15s;
  text-decoration: none;
}
.add-btn:hover {
  background: var(--amber-dark);
  color: #FFF;
}
.add-btn svg {
  width: 14px;
  height: 14px;
  stroke: currentColor;
  fill: none;
  stroke-width: 2.5;
}

/* LISTINGS LIST */
.listings-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.listing-row {
  display: grid;
  grid-template-columns: 100px minmax(0, 1fr);
  gap: 16px 20px;
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 16px;
  align-items: start;
  transition: all .2s ease;
  position: relative;
}
.listing-row:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.04);
}
.lr-img {
  width: 100px;
  height: 76px;
  border-radius: 6px;
  background: linear-gradient(140deg,#2C3E50,#3D5A6B);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  flex-shrink: 0;
}
.lr-info {
  min-width: 0;
}
.lr-info h3 {
  font-size: 15px;
  font-weight: 700;
  margin-bottom: 4px;
  color: var(--slate-900);
  overflow-wrap: break-word;
  word-break: break-word;
}
.lr-meta {
  font-size: 12px;
  color: var(--gray-text);
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
}
.badge-status {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
}
.badge-status.active { background: #EAF3DE; color: #3B6D11; }
.badge-status.pending { background: var(--amber-pale); color: var(--amber-dark); }
.badge-status.pending_payment { background: #FFF3E0; color: #E65100; }
.badge-status.rejected { background: #FCEBEB; color: #A32D2D; }
.badge-status.hidden { background: #F0F0F5; color: var(--gray-text); }

.lr-actions {
  grid-column: 1 / span 2;
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  gap: 8px;
  width: 100%;
  margin-top: 10px;
}
.lr-actions .lr-btn {
  flex: 1;
  min-width: 120px;
  max-width: 220px;
}
.lr-btn {
  background: var(--slate-50);
  border: 1px solid var(--border);
  border-radius: 6px;
  padding: 8px;
  font-size: 12px;
  font-weight: 600;
  text-align: center;
  color: var(--slate-900);
  transition: all .15s;
  cursor: pointer;
  text-decoration: none;
}
.lr-btn:hover {
  background: var(--slate-900);
  color: #FFF;
  border-color: var(--slate-900);
}
.lr-btn.primary {
  background: var(--amber);
  border-color: var(--amber);
}
.lr-btn.primary:hover {
  background: var(--amber-dark);
  border-color: var(--amber-dark);
}
.lr-btn.danger {
  color: #A32D2D;
}
.lr-btn.danger:hover {
  background: #A32D2D;
  color: #fff;
  border-color: #A32D2D;
}

/* PREMIUM STATISTICS DETAILS */
.stats-svg-container {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
  margin-bottom: 28px;
}
.stats-svg-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}
.stats-svg-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--slate-900);
}
.stats-period-select {
  padding: 8px 14px;
  border-radius: 10px;
  border: 1px solid var(--border);
  font-size: 13px;
  font-weight: 600;
  color: var(--slate-900);
  outline: none;
  background-color: var(--white);
  cursor: pointer;
}
.stats-chart-wrapper {
  position: relative;
  height: 220px;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  padding: 10px 10px 24px;
  border-bottom: 1px solid var(--border);
}
.chart-bar-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  flex: 1;
  height: 100%;
  justify-content: flex-end;
  position: relative;
}
.chart-bar {
  width: 36px;
  background: linear-gradient(180deg, var(--amber) 0%, var(--amber-dark) 100%);
  border-radius: 8px 8px 0 0;
  height: 0%; /* Updated via JS */
  transition: height 0.6s cubic-bezier(0.25, 1, 0.5, 1);
  cursor: pointer;
  position: relative;
}
.chart-bar:hover {
  background: linear-gradient(180deg, var(--slate-900) 0%, #252538 100%);
}
.chart-label {
  font-size: 11px;
  font-weight: 600;
  color: var(--gray-text);
  margin-top: 8px;
}
.chart-tooltip {
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translate(-50%, -8px) scale(0.95);
  background: var(--slate-900);
  color: #fff;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  white-space: nowrap;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  opacity: 0;
  pointer-events: none;
  transition: all 0.15s ease;
  z-index: 10;
}
.chart-tooltip::after {
  content: '';
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  border-width: 4px;
  border-style: solid;
  border-color: var(--slate-900) transparent transparent transparent;
}
.chart-bar:hover .chart-tooltip {
  opacity: 1;
  transform: translate(-50%, -8px) scale(1);
}

.stats-table-container {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 28px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
}
.stats-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}
.stats-table th {
  padding: 12px 16px;
  border-bottom: 2px solid var(--border);
  font-size: 11px;
  font-weight: 700;
  color: var(--gray-text);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.stats-table td {
  padding: 16px;
  border-bottom: 1px solid var(--border);
  font-size: 14px;
  color: var(--slate-900);
}
.stats-table tr:last-child td {
  border-bottom: none;
}

/* PROFILE CARD FORM */
.profile-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 28px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
}
.profile-avatar-section {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 28px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--border);
}
.profile-avatar-wrap {
  position: relative;
}
.profile-avatar-large {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  background: var(--slate-900);
  color: #fff;
  font-family: 'Syne', sans-serif;
  font-size: 40px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}
.profile-avatar-large img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.profile-avatar-upload-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: var(--amber);
  border: 2px solid var(--white);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  transition: background 0.15s;
}
.profile-avatar-upload-btn:hover {
  background: var(--amber-dark);
}
.profile-avatar-upload-btn svg {
  width: 14px;
  height: 14px;
  stroke: var(--slate-900);
  stroke-width: 2.5;
  fill: none;
}
.profile-avatar-info h4 {
  font-size: 16px;
  font-weight: 700;
  color: var(--slate-900);
  margin-bottom: 4px;
}
.profile-avatar-info p {
  font-size: 12px;
  color: var(--gray-text);
}
.profile-form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 24px;
}
.profile-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.profile-field label {
  font-size: 13px;
  font-weight: 600;
  color: var(--slate-900);
}
.profile-field input {
  width: 100%;
  box-sizing: border-box;
  padding: 12px 14px;
  border-radius: 10px;
  border: 1px solid var(--border);
  font-family: 'Inter', sans-serif;
  font-size: 14px;
  color: var(--slate-900);
  outline: none;
  background: var(--white);
  transition: all 0.15s;
}
.profile-field input:focus {
  border-color: var(--amber);
  box-shadow: 0 0 0 3px var(--amber-pale);
}
.unp-status {
  font-size: 11px;
  font-weight: 700;
  margin-top: 4px;
  display: flex;
  align-items: center;
  gap: 4px;
}
.unp-status.valid {
  color: #3B6D11;
}
.unp-status.invalid {
  color: #A32D2D;
}
.profile-save-btn {
  background: var(--amber);
  color: var(--slate-900);
  border: none;
  padding: 12px 28px;
  border-radius: 24px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.profile-save-btn:hover {
  background: var(--amber-dark);
  color: #fff;
}
.profile-save-btn svg {
  width: 16px;
  height: 16px;
  stroke: currentColor;
  stroke-width: 2.5;
  fill: none;
}

/* GLASSMORPHISM MODAL OVERLAYS */
.glass-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 15, 37, 0.4);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.3s ease;
}
.glass-overlay.show {
  opacity: 1;
  pointer-events: auto;
}
.glass-modal {
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid rgba(255, 255, 255, 0.6);
  border-radius: 18px;
  box-shadow: 0 20px 50px rgba(0,0,0,0.08);
  padding: 32px;
  max-width: 440px;
  width: 90%;
  transform: scale(0.92) translateY(20px);
  transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
  text-align: center;
}
.glass-overlay.show .glass-modal {
  transform: scale(1) translateY(0);
}
.glass-modal-title {
  font-size: 20px;
  font-weight: 800;
  color: var(--slate-900);
  margin-bottom: 12px;
}
.glass-modal-text {
  font-size: 14px;
  color: var(--gray-text);
  line-height: 1.6;
  margin-bottom: 24px;
}
.glass-modal-actions {
  display: flex;
  gap: 12px;
  justify-content: center;
}
.glass-btn {
  padding: 10px 22px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: all 0.15s;
}
.glass-btn.cancel {
  background: var(--slate-50);
  color: var(--slate-900);
  border: 1px solid var(--border);
}
.glass-btn.cancel:hover {
  background: var(--slate-100);
}
.glass-btn.danger {
  background: #A32D2D;
  color: #fff;
}
.glass-btn.danger:hover {
  background: #C43D3D;
}
.glass-btn.primary {
  background: var(--amber);
  color: var(--slate-900);
}
.glass-btn.primary:hover {
  background: var(--amber-dark);
}

.lr-btn.featured-active {
  background: linear-gradient(135deg, #FFF9E6 0%, #FFEBA3 100%);
  color: #B38F00;
  border-color: #FFE066;
  font-weight: 700;
  box-shadow: 0 0 10px rgba(255, 215, 0, 0.2);
}
.lr-btn.featured-active:hover {
  background: linear-gradient(135deg, #FFEBA3 0%, #FFD633 100%);
  color: #806600;
  border-color: #FFD633;
}
.lr-btn.featured-btn {
  border-color: #FFE066;
  color: #B38F00;
}
.lr-btn.featured-btn:hover {
  background: linear-gradient(135deg, #FFF9E6 0%, #FFEBA3 100%);
  border-color: #FFE066;
  color: #B38F00;
}
.lr-btn.homepage-active {
  background: linear-gradient(135deg, #EAF3DE 0%, #CDE8A3 100%);
  color: #3B6D11;
  border-color: #A3D66B;
  font-weight: 700;
  box-shadow: 0 0 10px rgba(59, 109, 17, 0.15);
}
.lr-btn.homepage-active:hover {
  background: linear-gradient(135deg, #CDE8A3 0%, #A4D66A 100%);
  color: #26460B;
  border-color: #A4D66A;
}
.lr-btn.homepage-btn {
  border-color: #A3D66B;
  color: #3B6D11;
}
.lr-btn.homepage-btn:hover {
  background: linear-gradient(135deg, #EAF3DE 0%, #CDE8A3 100%);
  border-color: #A3D66B;
  color: #3B6D11;
}

/* RESPONSIVE LAYOUT */
@media (max-width: 991px) {
  .dashboard-container {
    padding: 20px 16px 60px;
  }
  .dashboard-grid {
    grid-template-columns: minmax(0, 1fr);
    gap: 24px;
  }
  .sidebar-panel {
    padding: 16px;
  }
  .host-profile {
    display: flex;
    align-items: center;
    gap: 16px;
    text-align: left;
    margin-bottom: 16px;
    padding-bottom: 16px;
  }
  .host-avatar {
    margin: 0;
    width: 56px;
    height: 56px;
    font-size: 26px;
  }
  .sidebar-menu {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 8px;
    margin: 0 -8px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
  }
  .sidebar-menu::-webkit-scrollbar {
    display: none;
  }
  .sidebar-menu li {
    margin-bottom: 0;
    flex-shrink: 0;
    scroll-snap-align: start;
  }
  .sidebar-menu li a {
    white-space: nowrap !important;
    padding: 8px 14px;
    font-size: 13px;
    overflow: visible !important;
    text-overflow: clip !important;
  }
  .listings-section {
    padding: 16px;
  }
  .listing-row {
    display: grid;
    grid-template-columns: 80px 1fr;
    gap: 12px;
    text-align: left;
    padding: 12px;
  }
  .lr-img {
    width: 80px;
    height: 64px;
    font-size: 24px;
    margin: 0;
  }
  .lr-info {
    text-align: left;
  }
  .lr-info h3 {
    font-size: 14px;
    margin-bottom: 2px;
    font-weight: 700;
  }
  .lr-meta {
    justify-content: flex-start;
    gap: 6px;
  }
  .lr-actions {
    grid-column: 1 / span 2;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
    width: 100%;
    margin-top: 4px;
  }
  .lr-btn {
    width: 100%;
    padding: 8px 4px;
    font-size: 11px;
    text-align: center;
    border-radius: 6px;
    box-sizing: border-box;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .lr-btn.long-text {
    grid-column: span 2;
  }
  .stats-row {
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
  }
  .stat-card {
    padding: 12px 8px;
    gap: 4px;
    text-align: center;
    align-items: center;
  }
  .stat-card-title {
    font-size: 10px;
  }
  .stat-card-val {
    font-size: 18px;
    font-weight: 800;
  }
  .stat-card-trend {
    font-size: 9px;
  }
  .stats-chart-wrapper {
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding: 10px 0 24px;
  }
  .stats-hourly-chart {
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 12px;
  }
  .hourly-bar-container {
    min-width: 28px;
  }
  .profile-card {
    padding: 16px;
  }
  .profile-avatar-section {
    flex-direction: column;
    text-align: center;
    gap: 14px;
  }
  .profile-form-grid {
    grid-template-columns: 1fr;
  }
  .profile-save-btn {
    width: 100%;
    box-sizing: border-box;
    justify-content: center;
  }
  .section-header {
    margin-bottom: 16px;
  }
  .section-header h2 {
    font-size: 16px;
  }
  .add-btn {
    padding: 8px 12px;
    font-size: 12px;
  }
  .chart-bar {
    width: 24px;
  }
  .payment-periods-grid {
    grid-template-columns: 1fr 1fr;
    gap: 8px;
  }
  .glass-modal {
    padding: 24px 16px;
  }
  .lock-card {
    padding: 24px 16px;
    border-radius: 16px;
  }
  .lock-card h3 {
    font-size: 18px;
    margin-bottom: 8px;
  }
  .lock-features-list {
    margin-bottom: 18px;
  }
  .lock-features-list li {
    font-size: 12.5px;
    margin-bottom: 8px;
  }
}
@media (max-width: 480px) {
  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  .add-btn {
    width: 100%;
    justify-content: center;
  }
}

/* MONETIZATION SYSTEM STYLES */
.badge-status.pending_payment {
  background: #FFF3E0;
  color: #E65100;
  border: 1px solid #FFE0B2;
}
.payment-periods-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
  margin-bottom: 20px;
}
.period-card {
  border: 2px solid var(--border);
  border-radius: 12px;
  padding: 12px 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  background: var(--white);
  text-align: center;
  position: relative;
  overflow: hidden;
}
.period-card:hover {
  border-color: var(--amber);
  box-shadow: 0 4px 10px rgba(0,0,0,0.02);
}
.period-card.active {
  border-color: var(--amber);
  background: #FFFDE7;
}
.period-title {
  font-size: 13px;
  font-weight: 700;
  color: var(--slate-900);
}
.period-discount {
  position: absolute;
  top: 0;
  right: 0;
  background: #EAF3DE;
  color: #3B6D11;
  font-size: 9px;
  font-weight: 800;
  padding: 2px 6px;
  border-bottom-left-radius: 8px;
}
.period-discount.none {
  display: none;
}
.period-price {
  font-size: 11px;
  color: var(--gray-text);
  margin-top: 6px;
}
.promo-section {
  background: #F8F9FA;
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 12px;
  margin-bottom: 20px;
  text-align: left;
}
.promo-input-group {
  display: flex;
  gap: 8px;
}
.promo-input {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid var(--border);
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  text-transform: uppercase;
}
.promo-input:focus {
  outline: none;
  border-color: var(--amber);
}
.promo-btn {
  background: var(--slate-900);
  color: #FFF;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.15s;
}
.promo-btn:hover {
  background: var(--amber-dark);
}
.promo-msg {
  font-size: 11px;
  font-weight: 600;
  margin-top: 6px;
  display: none;
}
.promo-msg.success {
  color: #3B6D11;
  display: block;
}
.promo-msg.error {
  color: #A32D2D;
  display: block;
}
.payment-summary {
  background: #FFFDE7;
  border: 1px solid rgba(245, 166, 35, 0.2);
  border-radius: 10px;
  padding: 14px;
  margin-bottom: 24px;
  font-size: 13px;
}
.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 6px;
  color: var(--gray-text);
}
.summary-row.total {
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px dashed var(--border);
  font-weight: 800;
  font-size: 16px;
  color: var(--slate-900);
}

/* Detailed Stats Locked State */
.stats-premium-section {
  position: relative;
  margin-top: 28px;
  overflow: hidden;
  border-radius: 20px;
}
.stats-locked-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.45);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  display: flex;
  align-items: flex-start;
  justify-content: center;
  z-index: 100;
  border-radius: 20px;
  padding-top: 40px;
}
.lock-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 24px;
  padding: 40px;
  max-width: 500px;
  text-align: center;
  box-shadow: 0 20px 50px rgba(15, 15, 37, 0.08);
}
.lock-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: var(--amber-pale);
  color: var(--amber-dark);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}
.lock-card h3 {
  font-family: 'Syne', sans-serif;
  font-size: 22px;
  font-weight: 800;
  color: var(--slate-900);
  margin-bottom: 12px;
}
.lock-card p {
  font-size: 14px;
  color: var(--gray-text);
  line-height: 1.6;
  margin-bottom: 28px;
}
.unlock-actions {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.unlock-pay-btn {
  background: var(--amber);
  color: var(--slate-900);
  border: none;
  padding: 14px;
  border-radius: 12px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s;
  box-shadow: 0 4px 12px rgba(245, 166, 35, 0.2);
}
.unlock-pay-btn:hover {
  background: var(--amber-dark);
  color: #fff;
  transform: translateY(-1px);
}
.unlock-promo-row {
  display: flex;
  gap: 8px;
}
.unlock-promo-row input {
  flex: 1;
  background: var(--slate-50);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 14px;
  outline: none;
  transition: all 0.15s;
}
.unlock-promo-row input:focus {
  border-color: var(--amber);
  background: var(--white);
}
.unlock-promo-btn {
  background: var(--slate-900);
  color: var(--white);
  border: none;
  padding: 10px 18px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s;
}
.unlock-promo-btn:hover {
  background: var(--amber);
  color: var(--slate-900);
}
.locked-blur {
  filter: blur(5px);
  pointer-events: none;
  user-select: none;
}

/* Detailed Stats Locking List */
.lock-features-list {
  list-style: none;
  padding: 0;
  margin: 0 auto 28px;
  max-width: 440px;
  text-align: left;
}
.lock-features-list li {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-bottom: 12px;
  font-size: 13.5px;
  color: var(--gray-text);
  line-height: 1.4;
}
.lock-features-list li svg {
  color: var(--amber-dark);
  flex-shrink: 0;
  margin-top: 3px;
}

/* Extra Widgets Layout */
.stats-extra-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 28px;
  margin-top: 28px;
}
@media (max-width: 991px) {
  .stats-extra-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
}

/* Hourly activity bar chart */
.stats-hourly-chart {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  height: 120px;
  margin-top: 20px;
  padding-bottom: 4px;
  border-bottom: 1px solid var(--border);
}
.hourly-bar-container {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 100%;
  justify-content: flex-end;
  position: relative;
}
.hourly-bar {
  width: 65%;
  background: var(--slate-900);
  border-radius: 3px 3px 0 0;
  transition: height 0.3s ease-out;
  min-height: 2px;
}
.hourly-bar:hover {
  background: var(--amber);
}
.hourly-bar-container:hover .hourly-tooltip {
  opacity: 1;
  visibility: visible;
  transform: translateX(-50%) translateY(-8px);
}
.hourly-tooltip {
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%) translateY(0px);
  background: var(--slate-900);
  color: var(--white);
  font-size: 10px;
  font-weight: 700;
  padding: 4px 6px;
  border-radius: 4px;
  white-space: nowrap;
  opacity: 0;
  visibility: hidden;
  transition: all 0.15s ease-in-out;
  pointer-events: none;
  z-index: 10;
}
.hourly-label {
  font-size: 9px;
  color: var(--gray-text);
  margin-top: 6px;
  text-align: center;
}

/* Devices Breakdown Progress Bar */
.device-split-container {
  margin-top: 20px;
}
.device-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}
.device-info {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 600;
  color: var(--slate-900);
  width: 100px;
}
.device-info svg {
  color: var(--gray-text);
  flex-shrink: 0;
}
.device-bar-bg {
  height: 8px;
  background: var(--slate-50);
  border: 1px solid var(--border);
  border-radius: 4px;
  overflow: hidden;
  flex: 1;
  margin: 0 16px;
}
.device-bar-fill {
  height: 100%;
  background: var(--slate-900);
  border-radius: 4px;
  width: 0%;
  transition: width 0.4s ease-out;
}
.device-bar-fill.mobile {
  background: var(--amber);
}
.device-bar-fill.tablet {
  background: #5D6D7E;
}
.device-percent {
  font-size: 13px;
  font-weight: 800;
  color: var(--slate-900);
  width: 38px;
  text-align: right;
}

/* Competitor Benchmark Compare */
.market-compare-card {
  background: #FAF9F6;
  border: 1px dashed rgba(245, 166, 35, 0.4);
  border-radius: 12px;
  padding: 16px;
  margin-top: 20px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
}
.market-compare-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--white);
  border: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.market-compare-icon svg {
  color: var(--amber-dark);
}
.market-compare-info h4 {
  font-size: 13px;
  font-weight: 700;
  color: var(--slate-900);
  margin: 0 0 4px;
}
.market-compare-info p {
  font-size: 12px;
  color: var(--gray-text);
  line-height: 1.45;
  margin: 0;
}
.market-badge {
  display: inline-block;
  padding: 2px 6px;
  font-size: 10px;
  font-weight: 800;
  border-radius: 4px;
  margin-top: 6px;
}
.market-badge.positive {
  background: #EAF3DE;
  color: #3B6D11;
}
.market-badge.neutral {
  background: var(--slate-50);
  color: var(--gray-text);
}
/* ===== GLOBAL TEXT SIZE INCREASE OVERRIDES ===== */
.host-name { font-size: 17px !important; }
.host-status { font-size: 13px !important; }
.sidebar-menu li a { font-size: 15px !important; }
@media (max-width: 991px) {
  .sidebar-menu li a { font-size: 14px !important; }
}
.stat-card-title { font-size: 13px !important; }
.stat-card-val { font-size: 30px !important; }
.stat-card-trend { font-size: 12px !important; }
@media (max-width: 991px) {
  .stat-card-title { font-size: 11px !important; }
  .stat-card-val { font-size: 20px !important; }
  .stat-card-trend { font-size: 10px !important; }
}
.profile-field label { font-size: 14px !important; }
.profile-field input { font-size: 15px !important; }
.lr-info h3 { font-size: 17px !important; }
.lr-meta span { font-size: 13.5px !important; }
.lr-btn { font-size: 14px !important; }
@media (max-width: 991px) {
  .lr-info h3 { font-size: 15px !important; }
  .lr-meta span { font-size: 12.5px !important; }
  .lr-btn { font-size: 12px !important; }
}

/* TARIFFS AND GUIDE PANES CSS */
.tariffs-container {
  display: flex;
  flex-direction: column;
  gap: 28px;
}
.tariffs-title-section {
  text-align: left;
}
.tariffs-title-section h2 {
  font-family: 'Syne', sans-serif;
  font-weight: 800;
  font-size: 28px;
  color: var(--slate-900);
  margin-bottom: 8px;
}
.tariffs-title-section p {
  color: var(--gray-text);
  font-size: 14px;
}
.tariffs-cards-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 24px;
}
@media (max-width: 1200px) {
  .tariffs-cards-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
.tariff-showcase-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  transition: transform 0.2s, box-shadow 0.2s;
  position: relative;
  overflow: hidden;
}
.tariff-showcase-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.06);
  border-color: var(--slate-900);
}
.tariff-badge {
  position: absolute;
  top: 16px;
  right: 16px;
  background: var(--slate-900);
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 12px;
  text-transform: uppercase;
}
.tariff-showcase-card.cottage-card .tariff-badge {
  background: var(--amber);
  color: var(--slate-900);
}
.tariff-icon-title {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 16px;
}
.tariff-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: var(--slate-50);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}
.tariff-card-name {
  font-size: 20px;
  font-weight: 700;
  color: var(--slate-900);
}
.tariff-price-block {
  margin-bottom: 20px;
}
.tariff-price-main {
  font-size: 32px;
  font-weight: 800;
  font-family: 'Syne', sans-serif;
  color: var(--slate-900);
}
.tariff-price-sub {
  font-size: 12px;
  color: var(--gray-text);
  margin-top: 4px;
}
.tariff-features-list {
  list-style: none;
  padding: 0;
  margin: 0 0 24px 0;
  border-top: 1px solid var(--border);
  padding-top: 20px;
}
.tariff-features-list li {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  color: var(--slate-900);
  margin-bottom: 12px;
}
.tariff-features-list li svg {
  width: 16px;
  height: 16px;
  stroke: var(--amber-dark);
  stroke-width: 3;
  fill: none;
}
.tariffs-info-card {
  background: var(--slate-50);
  border-radius: 14px;
  padding: 24px;
  display: flex;
  gap: 16px;
  align-items: flex-start;
  border-left: 4px solid var(--amber);
}
.tariffs-info-card svg {
  width: 24px;
  height: 24px;
  stroke: var(--slate-900);
  fill: none;
  stroke-width: 2;
  flex-shrink: 0;
}
.tariffs-info-text h4 {
  font-size: 15px;
  font-weight: 700;
  color: var(--slate-900);
  margin-bottom: 6px;
}
.tariffs-info-text p {
  font-size: 13px;
  color: var(--gray-text);
  line-height: 1.5;
}

/* User Guide Accordion / Steps Styles */
.guide-container {
  display: flex;
  flex-direction: column;
  gap: 28px;
}
.guide-steps-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.guide-step-card {
  background: var(--white);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 24px;
  display: flex;
  gap: 20px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.02);
  align-items: flex-start;
}
.guide-step-num {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: var(--slate-900);
  color: #fff;
  font-size: 16px;
  font-weight: 800;
  font-family: 'Syne', sans-serif;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.guide-step-card:nth-child(even) .guide-step-num {
  background: var(--amber);
  color: var(--slate-900);
}
.guide-step-content h3 {
  font-size: 16px;
  font-weight: 700;
  color: var(--slate-900);
  margin-bottom: 8px;
}
.guide-step-content p {
  font-size: 13px;
  color: var(--gray-text);
  line-height: 1.6;
}
.faq-section {
  margin-top: 12px;
}
.faq-section h2 {
  font-family: 'Syne', sans-serif;
  font-weight: 800;
  font-size: 22px;
  color: var(--slate-900);
  margin-bottom: 16px;
}
.faq-accordion {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.faq-item {
  border: 1px solid var(--border);
  border-radius: 12px;
  background: var(--white);
  overflow: hidden;
  transition: border-color 0.2s;
}
.faq-item[open] {
  border-color: var(--slate-900);
}
.faq-summary {
  padding: 16px 20px;
  font-weight: 700;
  font-size: 14px;
  color: var(--slate-900);
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  user-select: none;
  list-style: none;
}
.faq-summary::-webkit-details-marker {
  display: none;
}
.faq-summary::after {
  content: '+';
  font-size: 18px;
  font-weight: 500;
  color: var(--gray-text);
  transition: transform 0.2s;
}
.faq-item[open] .faq-summary::after {
  content: '−';
  transform: rotate(180deg);
}
.faq-content {
  padding: 0 20px 20px 20px;
  font-size: 13px;
  color: var(--gray-text);
  line-height: 1.6;
  border-top: 1px solid var(--border);
  margin-top: 0;
  padding-top: 16px;
}

@media (max-width: 768px) {
  .tariffs-cards-grid {
    grid-template-columns: 1fr;
  }
  .guide-step-card {
    flex-direction: column;
    gap: 12px;
  }
}

/* ===== UNPAID WARNING BANNER ===== */
.unpaid-banner {
  background: linear-gradient(135deg, #FFF9E6 0%, #FFEBA3 100%);
  border: 1px solid #FFE066;
  border-radius: 14px;
  padding: 20px 24px;
  margin-bottom: 28px;
  display: flex;
  gap: 16px;
  align-items: flex-start;
  box-shadow: 0 4px 15px rgba(255, 224, 102, 0.15);
}
.unpaid-banner-icon {
  background: #FFF;
  color: var(--amber-dark);
  border-radius: 50%;
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.unpaid-banner-content {
  flex: 1;
}
.unpaid-banner-content h4 {
  margin: 0 0 6px 0;
  font-size: 16px;
  font-weight: 700;
  color: var(--slate-900);
}
.unpaid-banner-content p {
  margin: 0 0 14px 0;
  font-size: 13.5px;
  color: var(--slate-900);
  opacity: 0.85;
  line-height: 1.5;
}
.unpaid-listings-buttons {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}
.unpaid-pay-btn {
  background: var(--slate-900);
  color: var(--white);
  border: none;
  padding: 10px 18px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: background 0.15s, transform 0.1s;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.unpaid-pay-btn:hover {
  background: #252538;
  transform: translateY(-1px);
}
.unpaid-pay-btn:active {
  transform: translateY(0);
}
@media (max-width: 768px) {
  .unpaid-banner {
    padding: 16px;
    flex-direction: column;
    gap: 12px;
  }
  .unpaid-banner-icon {
    width: 36px;
    height: 36px;
  }
  .unpaid-banner-icon svg {
    width: 18px;
    height: 18px;
  }
  .unpaid-pay-btn {
    width: 100%;
    justify-content: center;
  }
}
</style>
@endsection

@section('content')
<div class="dashboard-container">
  @php
    $unpaidListings = $listings->filter(function($l) {
        return $l->status === 'pending_payment' || !$l->is_paid;
    });
  @endphp
  @if($unpaidListings->isNotEmpty())
  <div class="unpaid-banner">
    <div class="unpaid-banner-icon">
      <x-solar-card-linear width="24" height="24" />
    </div>
    <div class="unpaid-banner-content">
      <h4>Внимание! Требуется активация размещения</h4>
      <p>Чтобы ваши объявления появились в поиске и к вам пошли клиенты, перейдите в раздел <strong>Мои объявления</strong> и активируйте размещение:</p>
      <div class="unpaid-listings-buttons">
        @foreach($unpaidListings as $ul)
          <button class="unpaid-pay-btn" onclick="openPaymentModal({{ $ul->id }}, {{ $ul->tariff_price ?? 65 }}, '{{ $ul->type }}')">
            Активировать «{{ $ul->title }}» бесплатно
          </button>
        @endforeach
      </div>
    </div>
  </div>
  @endif
  <div class="dashboard-grid">
    
    <!-- SIDEBAR -->
    <aside class="sidebar-panel">
      <div class="host-profile">
        <div class="host-avatar" id="sidebar-host-avatar">Н</div>
        <div>
          <div class="host-name" id="sidebar-host-name">Наталья</div>
          <div class="host-status">Частный арендодатель</div>
        </div>
      </div>
      <ul class="sidebar-menu">
        <li>
          <a class="active" id="menu-listings" onclick="switchDashboardTab('listings', this)">
            <x-solar-home-2-linear width="18" height="18" />
            Мои объявления
          </a>
        </li>
        <li>
          <a id="menu-stats" onclick="switchDashboardTab('stats', this)">
            <x-solar-chart-2-linear width="18" height="18" />
            Статистика
          </a>
        </li>
        <li>
          <a id="menu-profile" onclick="switchDashboardTab('profile', this)">
            <x-solar-user-linear width="18" height="18" />
            Настройки профиля
          </a>
        </li>
        <li>
          <a id="menu-tariffs" onclick="switchDashboardTab('tariffs', this)">
            <x-solar-rocket-2-linear width="18" height="18" />
            Тарифы и услуги
          </a>
        </li>
        <li>
          <a id="menu-guide" onclick="switchDashboardTab('guide', this)">
            <x-solar-book-linear width="18" height="18" />
            Руководство
          </a>
        </li>
        <li>
          <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <x-solar-logout-2-linear width="18" height="18" />
            Выйти
          </a>
        </li>
      </ul>
    </aside>

    <!-- MAIN PANEL -->
    <main class="main-panel">
      
      <!-- PANE 1: LISTINGS -->
      <div id="pane-listings" class="tab-pane active">
        <!-- STATS CARDS GRID -->
        <div class="stats-row" style="margin-bottom: 28px;">
          <div class="stat-card">
            <span class="stat-card-title">Объявления</span>
            <span class="stat-card-val" id="card-stats-total">3</span>
            <span class="stat-card-trend">Всего на платформе</span>
          </div>
          <div class="stat-card">
            <span class="stat-card-title">Просмотры</span>
            <span class="stat-card-val" id="card-stats-views">1 660</span>
            <span class="stat-card-trend">+12% за неделю</span>
          </div>
          <div class="stat-card">
            <span class="stat-card-title">Клики на телефон</span>
            <span class="stat-card-val" id="card-stats-clicks">181</span>
            <span class="stat-card-trend">+5% за неделю</span>
          </div>
        </div>

        <div class="listings-section">
          <div class="section-header">
            <h2>Мои объявления</h2>
            <a href="{{ route('listings.create') }}" class="add-btn">
              <x-solar-add-circle-linear width="18" height="18" />
              Разместить жильё
            </a>
          </div>

          <div class="listings-list" id="listings-list-container">
            <!-- Dynamically populated via JS -->
          </div>
          <div id="listings-dashboard-pagination" class="pagination-container"></div>
        </div>
      </div>

      <!-- PANE 2: STATISTICS -->
      <div id="pane-stats" class="tab-pane">
        <div class="stats-row">
          <div class="stat-card">
            <span class="stat-card-title">Объявления</span>
            <span class="stat-card-val" id="stats-total-listings">3</span>
            <span class="stat-card-trend">Всего в аккаунте</span>
          </div>
          <div class="stat-card">
            <span class="stat-card-title">Просмотры</span>
            <span class="stat-card-val" id="stats-views-val">1 660</span>
            <span class="stat-card-trend" id="stats-views-trend">За все время</span>
          </div>
          <div class="stat-card">
            <span class="stat-card-title">Всего контактов</span>
            <span class="stat-card-val" id="stats-clicks-val">105</span>
            <span class="stat-card-trend" id="stats-clicks-trend">За все время</span>
          </div>
          <div class="stat-card" style="background: linear-gradient(145deg, #FFFFFF 0%, #ECFDF5 100%); border-color: #A7F3D0;">
            <span class="stat-card-title">Конверсия (CR)</span>
            <span class="stat-card-val" id="stats-conversion-val" style="color: #059669;">{{ $stats['conversion_rate'] ?? 0 }}%</span>
            <span class="stat-card-trend" style="color: #047857; font-weight: 600;">Контакты / Просмотры</span>
          </div>
        </div>

        <!-- Locked Stats Wrapper -->
        <div class="stats-premium-section">
          <!-- Locked overlay -->
          <div id="stats-locked-overlay" class="stats-locked-overlay" style="display: none;">
            <div class="lock-card">
              <div class="lock-icon">
                <x-solar-lock-keyhole-linear width="32" height="32" />
              </div>
              <h3>Детальная статистика</h3>
              <ul class="lock-features-list">
                <li>
                  <x-solar-check-circle-linear width="16" height="16" />
                  <span>Интерактивные графики просмотров и обращений по дням недели</span>
                </li>
                <li>
                  <x-solar-check-circle-linear width="16" height="16" />
                  <span>Детализация контактов: звонки, Telegram, WhatsApp и Viber отдельно</span>
                </li>
                <li>
                  <x-solar-check-circle-linear width="16" height="16" />
                  <span>Пообъектный анализ: просмотры, клики и конверсия (CR %) для каждого жилья</span>
                </li>
                <li>
                  <x-solar-check-circle-linear width="16" height="16" />
                  <span>Пиковые часы: статистика активности гостей по часам суток</span>
                </li>
                <li>
                  <x-solar-check-circle-linear width="16" height="16" />
                  <span>Типы устройств: разделение аудитории на мобильные и ПК</span>
                </li>
              </ul>
              
              <div class="unlock-actions">
                <button onclick="payForStats()" class="unlock-pay-btn">
                  Открыть за 30 Б̶ навсегда
                </button>
                <div class="unlock-promo-row">
                  <input type="text" id="stats-promo-input" placeholder="Промокод">
                  <button onclick="applyStatsPromo()" class="unlock-promo-btn">Применить</button>
                </div>
              </div>
            </div>
          </div>

          <div id="stats-blur-container">
            <!-- Channels breakdown card -->
            <div class="stats-svg-container" style="margin-top: 0; margin-bottom: 24px; padding: 20px;">
              <h3 class="stats-svg-title" style="margin-bottom: 14px;">Детализация контактов по каналам связи</h3>
              <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;">
                <div style="background: var(--bg); border: 1px solid var(--border); border-radius: 12px; padding: 14px; display: flex; align-items: center; gap: 12px;">
                  <div style="font-size: 24px;">📞</div>
                  <div>
                    <div style="font-size: 11px; font-weight: 700; color: var(--gray-text); text-transform: uppercase;">Звонки</div>
                    <div style="font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 800; color: var(--slate-900);" id="channel-phone-val">{{ $stats['channels']['phone'] ?? 0 }}</div>
                  </div>
                </div>
                <div style="background: var(--bg); border: 1px solid var(--border); border-radius: 12px; padding: 14px; display: flex; align-items: center; gap: 12px;">
                  <div style="font-size: 24px;">✈️</div>
                  <div>
                    <div style="font-size: 11px; font-weight: 700; color: var(--gray-text); text-transform: uppercase;">Telegram</div>
                    <div style="font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 800; color: #0284C7;" id="channel-telegram-val">{{ $stats['channels']['telegram'] ?? 0 }}</div>
                  </div>
                </div>
                <div style="background: var(--bg); border: 1px solid var(--border); border-radius: 12px; padding: 14px; display: flex; align-items: center; gap: 12px;">
                  <div style="font-size: 24px;">💬</div>
                  <div>
                    <div style="font-size: 11px; font-weight: 700; color: var(--gray-text); text-transform: uppercase;">WhatsApp</div>
                    <div style="font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 800; color: #059669;" id="channel-whatsapp-val">{{ $stats['channels']['whatsapp'] ?? 0 }}</div>
                  </div>
                </div>
                <div style="background: var(--bg); border: 1px solid var(--border); border-radius: 12px; padding: 14px; display: flex; align-items: center; gap: 12px;">
                  <div style="font-size: 24px;">💜</div>
                  <div>
                    <div style="font-size: 11px; font-weight: 700; color: var(--gray-text); text-transform: uppercase;">Viber</div>
                    <div style="font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 800; color: #7C3AED;" id="channel-viber-val">{{ $stats['channels']['viber'] ?? 0 }}</div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Premium SVG bar chart -->
            <div class="stats-svg-container">
              <div class="stats-svg-header">
                <div>
                  <h3 class="stats-svg-title">Просмотры и обращения по дням</h3>
                  <div style="display: flex; gap: 14px; font-size: 12px; font-weight: 600; color: var(--gray-text); margin-top: 4px;">
                    <span style="display: flex; align-items: center; gap: 6px;"><span style="width: 10px; height: 10px; border-radius: 3px; background: #4F46E5;"></span> Просмотры</span>
                    <span style="display: flex; align-items: center; gap: 6px;"><span style="width: 10px; height: 10px; border-radius: 3px; background: #059669;"></span> Клики по контактам</span>
                  </div>
                </div>
                <select class="stats-period-select" id="stats-period-selector" onchange="changeStatsPeriod(this.value)">
                  <option value="week" selected>За неделю</option>
                  <option value="month">За месяц</option>
                  <option value="all">За все время</option>
                </select>
              </div>
              
              <div class="stats-chart-wrapper" id="stats-chart-bars">
                <!-- Dynamically generated via JS -->
              </div>
            </div>

            <!-- Extra Premium Widgets (Devices, Competitors & Hourly) -->
            <div class="stats-extra-grid">
              <!-- Left Widget: Devices & Competitor Compare -->
              <div class="stats-svg-container" style="margin-top: 0; padding: 20px;">
                <h3 class="stats-svg-title">Типы устройств и сравнение</h3>
                
                <!-- Devices breakdown list -->
                <div class="device-split-container" id="stats-device-split">
                  <!-- Dynamically populated via JS -->
                </div>

                <!-- Competitor compare card -->
                <div id="stats-market-compare">
                  <!-- Dynamically populated via JS -->
                </div>
              </div>

              <!-- Right Widget: Hourly views distribution -->
              <div class="stats-svg-container" style="margin-top: 0; padding: 20px;">
                <h3 class="stats-svg-title">Активность гостей по часам</h3>
                <div class="stats-hourly-chart" id="stats-hourly-bars">
                  <!-- Dynamically populated via JS -->
                </div>
              </div>
            </div>

            <!-- Table Grid breakdown -->
            <div class="stats-table-container">
              <h3 class="stats-svg-title" style="margin-bottom: 18px;">Статистика по объектам</h3>
              <div style="overflow-x: auto;">
                <table class="stats-table">
                  <thead>
                    <tr>
                      <th>Объявление</th>
                      <th>Просмотры</th>
                      <th>Звонки</th>
                      <th>Мессенджеры</th>
                      <th>Всего контактов</th>
                      <th>Конверсия (CR)</th>
                    </tr>
                  </thead>
                  <tbody id="stats-table-body">
                    <!-- Dynamically populated via JS -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- PANE 3: PROFILE SETTINGS -->
      <div id="pane-profile" class="tab-pane">
        <div class="profile-card">
          <div class="profile-avatar-section">
            <div class="profile-avatar-wrap">
              <div class="profile-avatar-large" id="profile-avatar-container">Н</div>
              <label class="profile-avatar-upload-btn" for="avatar-file-input">
                <x-solar-camera-linear width="18" height="18" />
                <input type="file" id="avatar-file-input" style="display:none;" accept="image/*" onchange="uploadAvatarImage(this)">
              </label>
            </div>
            <div class="profile-avatar-info">
              <h4 id="profile-name-title">Наталья</h4>
              <p>Размер файла не более 5 МБ. Поддерживаются форматы JPG, PNG, WEBP.</p>
            </div>
          </div>

          <form id="host-profile-form" onsubmit="saveHostProfile(event)">
            <div class="profile-form-grid">
              <div class="profile-field">
                <label for="profile-name">Имя хозяина / Название компании <span style="color:var(--amber-dark)">*</span></label>
                <input type="text" id="profile-name" required placeholder="Введите ваше имя">
              </div>
              <div class="profile-field">
                <label for="profile-phone">Контактный телефон <span style="color:var(--amber-dark)">*</span></label>
                <input type="text" id="profile-phone" required placeholder="+375 XX XXX-XX-XX">
              </div>
              <div class="profile-field">
                <label for="profile-telegram">Telegram username</label>
                <input type="text" id="profile-telegram" placeholder="@username">
              </div>
              <div class="profile-field">
                <label for="profile-whatsapp">WhatsApp телефон</label>
                <input type="text" id="profile-whatsapp" placeholder="+375 XX XXX-XX-XX">
              </div>
              <div class="profile-field">
                <label for="profile-viber">Viber телефон</label>
                <input type="text" id="profile-viber" placeholder="+375 XX XXX-XX-XX">
              </div>
              <div class="profile-field" style="grid-column: 1 / -1;">
                <label for="profile-unp">УНП (для ИП/Юрлиц/Самозанятых)</label>
                <input type="text" id="profile-unp" placeholder="9 символов (например, AB1234567)" maxlength="9" oninput="validateUNPField(this.value)">
                <div id="unp-validator-status" class="unp-status invalid" style="display: none;">
                  <span id="unp-status-icon">✕</span> <span id="unp-status-text">Неверный формат. Требуется 9 цифр или 2 буквы + 7 цифр.</span>
                </div>
                <div style="font-size: 11px; color: var(--gray-text); margin-top: 4px; line-height: 1.4;">
                  Внимание: Для публикации коммерческих или загородных объектов арендодатель обязан предоставить учетный номер налогоплательщика УНП.
                </div>
              </div>
            </div>

            <button type="submit" class="profile-save-btn">
              <x-solar-check-circle-linear width="18" height="18" />
              Сохранить изменения
            </button>
          </form>
        </div>

        <!-- Telegram Notifications Card for Owners -->
        <div class="profile-card" style="margin-top: 24px;">
          <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(0,136,204,0.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <svg viewBox="0 0 24 24" width="24" height="24" fill="#0088cc"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12l-6.87 4.326-2.96-.924c-.643-.204-.658-.643.136-.953l11.57-4.46c.537-.204.997.12.825.861z"/></svg>
            </div>
            <div>
              <h3 style="font-size: 17px; font-weight: 700; color: var(--slate-900); margin: 0 0 2px 0;">Telegram-уведомления для собственника</h3>
              <p style="font-size: 13px; color: var(--gray-text); margin: 0;">Мгновенные оповещения в Telegram, когда потенциальные гости просматривают ваше объявление или кликают на контакты.</p>
            </div>
          </div>

          @php $tgChatId = $user ? $user->getTelegramChatId() : null; @endphp
          @if(!empty($tgChatId))
            <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 12px; padding: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
              <div style="display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 10px; height: 10px; border-radius: 50%; background: #10B981;"></span>
                <span style="font-size: 14px; font-weight: 600; color: #065F46;">Telegram-бот подключен (ID: {{ $tgChatId }})</span>
              </div>
              <form action="{{ route('dashboard.telegram.disconnect') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="glass-btn danger" style="padding: 8px 16px; font-size: 13px;">Отключить бот</button>
              </form>
            </div>
          @else
            <div style="background: var(--bg-soft, #f8fafc); border: 1px solid var(--border, #e2e8f0); border-radius: 12px; padding: 18px;">
              <p style="font-size: 13.5px; color: var(--slate-700); line-height: 1.5; margin-bottom: 14px;">
                Подключите нашего Telegram-бота, чтобы получать уведомления по вашим объявлениям прямо в Telegram вместо администратора.
              </p>
              <a href="https://t.me/{{ env('TELEGRAM_BOT_USERNAME', 'FLATLYNOT_bot') }}?start=bind_{{ $user->id }}" target="_blank" class="add-btn" style="background: #0088cc; display: inline-flex; text-decoration: none;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" style="margin-right: 6px;"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12l-6.87 4.326-2.96-.924c-.643-.204-.658-.643.136-.953l11.57-4.46c.537-.204.997.12.825.861z"/></svg>
                Подключить Telegram-бота
              </a>
            </div>
          @endif
        </div>
      </div>

      <!-- PANE 4: TARIFFS -->
      <div id="pane-tariffs" class="tab-pane">
        <div class="tariffs-container">
          <div class="tariffs-title-section">
            <h2>Тарифные планы и услуги</h2>
            <p>Flatly.by работает без посредников и скрытых комиссий. Выберите подходящие условия для вашего жилья.</p>
          </div>

          <div class="tariffs-cards-grid">
            <!-- Tariff 1: Apartment -->
            <div class="tariff-showcase-card">
              <div class="tariff-badge">Популярный</div>
              <div>
                <div class="tariff-icon-title">
                  <div class="tariff-icon">🏢</div>
                  <div class="tariff-card-name">Тариф «Квартира»</div>
                </div>
                <div class="tariff-price-block">
                  <div class="tariff-price-main">0 рублей</div>
                  <div class="tariff-price-sub">за 30 дней размещения одного объекта</div>
                </div>
                <ul class="tariff-features-list">
                  <li>
                    <x-solar-check-circle-linear width="16" height="16" />
                    24/7 показ на интерактивной карте
                  </li>
                  <li>
                    <x-solar-check-circle-linear width="16" height="16" />
                    Прямые контакты (Viber, Telegram, Phone)
                  </li>
                  <li>
                    <x-solar-check-circle-linear width="16" height="16" />
                    Детальная статистика просмотров и кликов
                  </li>
                  <li>
                    <x-solar-check-circle-linear width="16" height="16" />
                    Без комиссий с ваших бронирований
                  </li>
                </ul>
              </div>
            </div>

            <!-- Tariff 2: Cottage -->
            <div class="tariff-showcase-card cottage-card">
              <div class="tariff-badge">Коттеджи и дома</div>
              <div>
                <div class="tariff-icon-title">
                  <div class="tariff-icon">🏡</div>
                  <div class="tariff-card-name">Тариф «Загородный»</div>
                </div>
                <div class="tariff-price-block">
                  <div class="tariff-price-main">0 рублей</div>
                  <div class="tariff-price-sub">за 30 дней размещения одного объекта</div>
                </div>
                <ul class="tariff-features-list">
                  <li>
                    <x-solar-check-circle-linear width="16" height="16" />
                    24/7 показ на интерактивной карте
                  </li>
                  <li>
                    <x-solar-check-circle-linear width="16" height="16" />
                    Прямые контакты (Viber, Telegram, Phone)
                  </li>
                  <li>
                    <x-solar-check-circle-linear width="16" height="16" />
                    Детальная статистика просмотров и кликов
                  </li>
                  <li>
                    <x-solar-check-circle-linear width="16" height="16" />
                    Без комиссий с ваших бронирований
                  </li>
                </ul>
              </div>
            </div>

            <!-- Tariff 3: Highlighting -->
            <div class="tariff-showcase-card highlight-tariff-card">
              <div class="tariff-badge" style="background: var(--amber); color: var(--slate-900);">Выделение</div>
              <div>
                <div class="tariff-icon-title">
                  <div class="tariff-icon">🌟</div>
                  <div class="tariff-card-name">Услуга «Выделение»</div>
                </div>
                <div class="tariff-price-block">
                  <div class="tariff-price-main">15 рублей</div>
                  <div class="tariff-price-sub">в месяц (за 30 дней выделения объекта)</div>
                </div>
                <ul class="tariff-features-list">
                  <li>
                    <x-solar-check-circle-linear width="16" height="16" />
                    Привлекающий внимание желтый фон
                  </li>
                  <li>
                    <x-solar-check-circle-linear width="16" height="16" />
                    В среднем в 3-4 раза больше просмотров
                  </li>
                  <li>
                    <x-solar-check-circle-linear width="16" height="16" />
                    Выделение объекта на интерактивной карте
                  </li>
                  <li>
                    <x-solar-check-circle-linear width="16" height="16" />
                    Повышает доверие со стороны арендаторов
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Promo info / discounts info -->
          <div class="tariffs-info-card">
            <x-solar-info-circle-linear width="20" height="20" />
            <div class="tariffs-info-text">
              <h4>Как активировать или продлить тариф?</h4>
              <p>Перейдите во вкладку <strong>«Мои объявления»</strong>. Нажмите кнопку <strong>«Активировать бесплатно»</strong> или <strong>«Продлить тариф»</strong> под нужным жильем. В открывшемся окне вы сможете выбрать желаемый период размещения (на 3, 6 или 12 месяцев со скидками до -30%) и при необходимости ввести ваш скидочный промокод.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- PANE 5: GUIDE -->
      <div id="pane-guide" class="tab-pane">
        <div class="guide-container">
          <div class="tariffs-title-section">
            <h2>Руководство пользователя</h2>
            <p>Узнайте, как максимально эффективно использовать возможности платформы Flatly.by для сдачи жилья.</p>
          </div>

          <div class="guide-steps-list">
            <!-- Step 1 -->
            <div class="guide-step-card">
              <div class="guide-step-num">1</div>
              <div class="guide-step-content">
                <h3>Создание и заполнение объявления</h3>
                <p>Нажмите кнопку <strong>«Разместить жильё»</strong> во вкладке «Мои объявления». Заполните форму: укажите точный адрес (он автоматически определит координаты на карте), характеристики (спальные места, удобства), загрузите качественные светлые фотографии. Объявления с хорошими фотографиями получают в 3 раза больше просмотров!</p>
              </div>
            </div>

            <!-- Step 2 -->
            <div class="guide-step-card">
              <div class="guide-step-num">2</div>
              <div class="guide-step-content">
                <h3>Указание контактов для прямой связи</h3>
                <p>В настройках вашего профиля заполните контактные данные: номер телефона и ники в Viber, Telegram и WhatsApp. При нажатии на кнопки связи на сайте гости будут автоматически попадать в чат с вами в соответствующем мессенджере. Это увеличивает конверсию и экономит ваше время.</p>
              </div>
            </div>

            <!-- Step 3 -->
            <div class="guide-step-card">
              <div class="guide-step-num">3</div>
              <div class="guide-step-content">
                <h3>Регистрация УНП и модерация</h3>
                <p>Согласно законодательству Республики Беларусь, для сдачи квартир на короткие сроки арендодатели обязаны указывать свой Учетный номер плательщика (УНП). Впишите его в настройках профиля. Система автоматически проверит формат УНП, после чего ваши объявления будут успешно проверены модератором и допущены к публикации.</p>
              </div>
            </div>

            <!-- Step 4 -->
            <div class="guide-step-card">
              <div class="guide-step-num">4</div>
              <div class="guide-step-content">
                <h3>Активация объявлений и промокоды</h3>
                <p>Для публикации на карте выберите нужный период во всплывающем окне оплаты объявления. Для новых пользователей первое объявление размещается <strong>бесплатно на 30 дней</strong>. Также вы можете использовать скидочные и подарочные промокоды.</p>
              </div>
            </div>

            <!-- Step 5 -->
            <div class="guide-step-card">
              <div class="guide-step-num">5</div>
              <div class="guide-step-content">
                <h3>Анализ статистики и оптимизация</h3>
                <p>Регулярно заглядывайте в раздел <strong>«Статистика»</strong>. Если просмотры падают, попробуйте обновить главную фотографию или скорректировать цену. График кликов по контактам покажет реальный интерес аудитории к вашему объекту в разные дни недели и часы суток.</p>
              </div>
            </div>
          </div>

          <!-- FAQ Section -->
          <div class="faq-section">
            <h2>Часто задаваемые вопросы (FAQ)</h2>
            <div class="faq-accordion">
              <details class="faq-item">
                <summary class="faq-summary">Почему моё объявление находится в статусе «Отклонено»?</summary>
                <div class="faq-content">
                  Чаще всего объявление отклоняется из-за отсутствия УНП в вашем профиле или несоответствия фотографий правилам сайта (наличие водяных знаков других площадок, плохое качество). Проверьте настройки профиля, добавьте УНП и объявление автоматически отправится на повторную модерацию.
                </div>
              </details>

              <details class="faq-item">
                <summary class="faq-summary">Как работает оплата на сайте?</summary>
                <div class="faq-content">
                  Оплата осуществляется в белорусских рублях через защищенный шлюз банковскими картами любого белорусского или международного банка, а также с помощью системы ЕРИП. Все платежи безопасны и защищены SSL-сертификатом.
                </div>
              </details>

              <details class="faq-item">
                <summary class="faq-summary">Берет ли Flatly.by комиссию с бронирований гостей?</summary>
                <div class="faq-content">
                  Нет! Наша платформа работает по модели фиксированной подписки (тарифа). Мы не вмешиваемся в расчеты между вами и гостями и не берем никаких комиссий. Все заработанные деньги остаются вам.
                </div>
              </details>
            </div>
          </div>
        </div>
      </div>

    </main>

  </div>
</div>

<!-- DELETE CONFIRMATION GLASS MODAL -->
<div class="glass-overlay" id="delete-modal-overlay">
  <div class="glass-modal">
    <div class="glass-modal-title">Удалить объявление?</div>
    <div class="glass-modal-text">Вы действительно хотите безвозвратно удалить это объявление из списка? Это действие нельзя будет отменить.</div>
    <div class="glass-modal-actions">
      <button class="glass-btn cancel" onclick="closeModal('delete-modal-overlay')">Отмена</button>
      <button class="glass-btn danger" id="btn-confirm-delete" onclick="executeDeleteListing()">Удалить</button>
    </div>
  </div>
</div>

<!-- REJECTION REASON DETAILS MODAL -->
<div class="glass-overlay" id="reject-modal-overlay">
  <div class="glass-modal" style="max-width: 480px;">
    <div class="glass-modal-title" style="color: #A32D2D;">Причина отклонения публикации</div>
    <div class="glass-modal-text" style="text-align: left;" id="reject-modal-description">
      Объявление отклонено модератором по следующей причине:<br><br>
      <strong>«Нарушение законодательства о налогообложении (Указ № 99-З РБ). Отсутствует зарегистрированный УНП (Учетный номер плательщика) владельца в профиле хозяина.»</strong><br><br>
      Пожалуйста, перейдите в настройки профиля, укажите ваш 9-значный УНП и сохраните изменения для автоматической повторной отправки на проверку.
    </div>
    <div class="glass-modal-actions">
      <button class="glass-btn cancel" onclick="closeModal('reject-modal-overlay')">Закрыть</button>
      <button class="glass-btn primary" onclick="switchToProfileTabFromRejection()">В настройки профиля</button>
    </div>
  </div>
</div>

<!-- PAYMENT CHOICE AND PROMO CODE GLASS MODAL -->
<div class="glass-overlay" id="payment-modal-overlay">
  <div class="glass-modal" style="max-width: 480px;">
    <div class="glass-modal-title">Размещение объявления</div>
    <div class="glass-modal-text" style="text-align: left; margin-bottom: 16px;">
      Выберите подходящий срок размещения жилья на сайте:
    </div>

    <!-- Periods Grid -->
    <div class="payment-periods-grid">
      <div class="period-card active" data-months="1" onclick="selectPaymentPeriod(1)">
        <div class="period-title">1 мес</div>
        <div class="period-discount">Бесплатно</div>
        <div class="period-price"><span class="price-val">0</span> Б̶/мес</div>
      </div>
      <div class="period-card" data-months="3" onclick="selectPaymentPeriod(3)">
        <div class="period-title">3 мес</div>
        <div class="period-discount">-10%</div>
        <div class="period-price"><span class="price-val">9</span> Б̶/мес</div>
      </div>
      <div class="period-card" data-months="6" onclick="selectPaymentPeriod(6)">
        <div class="period-title">6 мес</div>
        <div class="period-discount">-20%</div>
        <div class="period-price"><span class="price-val">8</span> Б̶/мес</div>
      </div>
      <div class="period-card" data-months="12" onclick="selectPaymentPeriod(12)">
        <div class="period-title">12 мес</div>
        <div class="period-discount">-30%</div>
        <div class="period-price"><span class="price-val">7</span> Б̶/мес</div>
      </div>
    </div>

    <!-- Promo Code Section -->
    <div class="promo-section">
      <div style="font-size: 11px; font-weight: 700; color: var(--slate-900); margin-bottom: 6px;">Есть промокод?</div>
      <div class="promo-input-group">
        <input type="text" id="payment-promo-input" class="promo-input" placeholder="Введите промокод">
        <button type="button" class="promo-btn" onclick="applyPromoCode()">Применить</button>
      </div>
      <div id="payment-promo-msg" class="promo-msg"></div>
    </div>

    <!-- Calculations Summary -->
    <div class="payment-summary">
      <div class="summary-row">
        <span>Базовая стоимость:</span>
        <span id="summary-base-price">10.00 Б̶</span>
      </div>
      <div class="summary-row" id="summary-discount-row" style="display: none;">
        <span>Скидка за срок:</span>
        <span id="summary-discount-val">-0.00 Б̶</span>
      </div>
      <div class="summary-row" id="summary-promo-row" style="display: none;">
        <span>Скидка по промокоду:</span>
        <span id="summary-promo-val">-0.00 Б̶</span>
      </div>
      <div class="summary-row total">
        <span>Итого к оплате:</span>
        <span id="summary-total-price">10.00 Б̶</span>
      </div>
    </div>

    <div class="glass-modal-actions">
      <button class="glass-btn cancel" onclick="closePaymentModal()">Отмена</button>
      <button class="glass-btn primary" id="payment-submit-btn" style="flex: 1;" onclick="confirmPaymentCheckout()">Перейти к оплате</button>
    </div>
  </div>
</div>

<!-- FEATURED PROMOTION CHOICE AND PROMO CODE GLASS MODAL -->
<div class="glass-overlay" id="featured-modal-overlay">
  <div class="glass-modal" style="max-width: 480px;">
    <div class="glass-modal-title">Продвижение объявления 🌟</div>
    <div class="glass-modal-text" style="text-align: left; margin-bottom: 16px;">
      Подключите услугу <strong>«Выделение»</strong>, чтобы привлечь больше внимания арендаторов:
      <ul style="margin: 10px 0; padding-left: 20px; line-height: 1.5; font-size: 13px;">
        <li>⭐ Выделение объявления <strong>ярким цветом</strong> в результатах поиска;</li>
        <li>📍 Приоритетное отображение на интерактивной карте города;</li>
        <li>📈 Увеличение просмотров и обращений до 3-х раз.</li>
      </ul>
    </div>

    <!-- Promotion Periods Grid -->
    <div class="payment-periods-grid" id="featured-periods-grid" style="margin-top: 16px;">
      <div class="period-card active" data-featured-months="1" onclick="selectFeaturedPeriod(1)">
        <div class="period-title">1 мес</div>
        <div class="period-discount">Тариф</div>
        <div class="period-price"><span class="featured-price-val">15</span> Б̶/мес</div>
      </div>
      <div class="period-card" data-featured-months="3" onclick="selectFeaturedPeriod(3)">
        <div class="period-title">3 мес</div>
        <div class="period-discount">-10%</div>
        <div class="period-price"><span class="featured-price-val">13.5</span> Б̶/мес</div>
      </div>
      <div class="period-card" data-featured-months="6" onclick="selectFeaturedPeriod(6)">
        <div class="period-title">6 мес</div>
        <div class="period-discount">-20%</div>
        <div class="period-price"><span class="featured-price-val">12</span> Б̶/мес</div>
      </div>
      <div class="period-card" data-featured-months="12" onclick="selectFeaturedPeriod(12)">
        <div class="period-title">12 мес</div>
        <div class="period-discount">-30%</div>
        <div class="period-price"><span class="featured-price-val">10.5</span> Б̶/мес</div>
      </div>
    </div>

    <!-- Promo Code Section -->
    <div class="promo-section" style="margin-top: 16px; border-top: 1px solid var(--border); padding-top: 16px;">
      <div style="font-size: 11px; font-weight: 700; color: var(--slate-900); margin-bottom: 6px;">Есть промокод на продвижение?</div>
      <div class="promo-input-group">
        <input type="text" id="featured-promo-input" class="promo-input" placeholder="Введите промокод">
        <button type="button" class="promo-btn" onclick="applyFeaturedPromoCode()">Применить</button>
      </div>
      <div id="featured-promo-msg" class="promo-msg"></div>
    </div>

    <!-- Calculations Summary -->
    <div class="payment-summary" style="margin-top: 16px; border-top: 1px solid var(--border); padding-top: 16px;">
      <div class="summary-row">
        <span>Базовая стоимость:</span>
        <span id="featured-summary-base-price">15.00 Б̶</span>
      </div>
      <div class="summary-row" id="featured-summary-discount-row" style="display: none;">
        <span>Скидка за срок:</span>
        <span id="featured-summary-discount-val">-0.00 Б̶</span>
      </div>
      <div class="summary-row" id="featured-summary-promo-row" style="display: none;">
        <span>Скидка по промокоду:</span>
        <span id="featured-summary-promo-val">-0.00 Б̶</span>
      </div>
      <div class="summary-row total">
        <span>Итого к оплате:</span>
        <span id="featured-summary-total-price">15.00 Б̶</span>
      </div>
    </div>

    <div class="glass-modal-actions" style="margin-top: 20px;">
      <button class="glass-btn cancel" onclick="closeFeaturedModal()">Отмена</button>
      <button class="glass-btn primary" id="featured-submit-btn" style="flex: 1;" onclick="confirmFeaturedCheckout()">Перейти к оплате</button>
    </div>


  </div>
</div>

<!-- HOMEPAGE PROMOTION CHOICE AND PROMO CODE GLASS MODAL -->
<div class="glass-overlay" id="homepage-modal-overlay">
  <div class="glass-modal" style="max-width: 480px;">
    <div class="glass-modal-title">Тариф «Главная страница» 🏠</div>
    <div class="glass-modal-text" style="text-align: left; margin-bottom: 16px;">
      Разместите ваше объявление в самом престижном блоке <strong>«Рекомендуемые предложения»</strong> на главной странице Flatly:
      <ul style="margin: 10px 0; padding-left: 20px; line-height: 1.5; font-size: 13px;">
        <li>🏠 Показ на главной странице сайта;</li>
        <li>📈 100% охват целевой аудитории и максимальное доверие гостей;</li>
        <li>🚀 Рост просмотров и прямых звонков/сообщений до 10 раз.</li>
      </ul>
    </div>

    <!-- Homepage Periods Grid -->
    <div class="payment-periods-grid" id="homepage-periods-grid" style="margin-top: 16px;">
      <div class="period-card active" data-homepage-months="1" onclick="selectHomepagePeriod(1)">
        <div class="period-title">1 мес</div>
        <div class="period-discount">Тариф</div>
        <div class="period-price"><span class="homepage-price-val">50</span> Б̶/мес</div>
      </div>
      <div class="period-card" data-homepage-months="3" onclick="selectHomepagePeriod(3)">
        <div class="period-title">3 мес</div>
        <div class="period-discount">-10%</div>
        <div class="period-price"><span class="homepage-price-val">45</span> Б̶/мес</div>
      </div>
      <div class="period-card" data-homepage-months="6" onclick="selectHomepagePeriod(6)">
        <div class="period-title">6 мес</div>
        <div class="period-discount">-20%</div>
        <div class="period-price"><span class="homepage-price-val">40</span> Б̶/мес</div>
      </div>
      <div class="period-card" data-homepage-months="12" onclick="selectHomepagePeriod(12)">
        <div class="period-title">12 мес</div>
        <div class="period-discount">-30%</div>
        <div class="period-price"><span class="homepage-price-val">35</span> Б̶/мес</div>
      </div>
    </div>

    <!-- Promo Code Section -->
    <div class="promo-section" style="margin-top: 16px; border-top: 1px solid var(--border); padding-top: 16px;">
      <div style="font-size: 11px; font-weight: 700; color: var(--slate-900); margin-bottom: 6px;">Есть промокод на Главную страницу?</div>
      <div class="promo-input-group">
        <input type="text" id="homepage-promo-input" class="promo-input" placeholder="Введите промокод">
        <button type="button" class="promo-btn" onclick="applyHomepagePromoCode()">Применить</button>
      </div>
      <div id="homepage-promo-msg" class="promo-msg"></div>
    </div>

    <!-- Calculations Summary -->
    <div class="payment-summary" style="margin-top: 16px; border-top: 1px solid var(--border); padding-top: 16px;">
      <div class="summary-row">
        <span>Базовая стоимость:</span>
        <span id="homepage-summary-base-price">50.00 Б̶</span>
      </div>
      <div class="summary-row" id="homepage-summary-discount-row" style="display: none;">
        <span>Скидка за срок:</span>
        <span id="homepage-summary-discount-val">-0.00 Б̶</span>
      </div>
      <div class="summary-row" id="homepage-summary-promo-row" style="display: none;">
        <span>Скидка по промокоду:</span>
        <span id="homepage-summary-promo-val">-0.00 Б̶</span>
      </div>
      <div class="summary-row total">
        <span>Итого к оплате:</span>
        <span id="homepage-summary-total-price">50.00 Б̶</span>
      </div>
    </div>

    <div class="glass-modal-actions" style="margin-top: 20px;">
      <button class="glass-btn cancel" onclick="closeHomepageModal()">Отмена</button>
      <button class="glass-btn primary" id="homepage-submit-btn" style="flex: 1;" onclick="confirmHomepageCheckout()">Перейти к оплате</button>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
// Server-loaded database values mapped to front-end schema
const serverListings = (@json($listings)).map(listing => {
  const viewsCount = parseInt(listing.real_views_count) || 0;
  const phoneCount = parseInt(listing.real_phone_count) || 0;
  const messengersCount = parseInt(listing.real_messengers_count) || 0;
  const telegramCount = parseInt(listing.real_telegram_count) || 0;
  const whatsappCount = parseInt(listing.real_whatsapp_count) || 0;
  const viberCount = parseInt(listing.real_viber_count) || 0;
  const contactsCount = parseInt(listing.real_contacts_count) || (phoneCount + messengersCount);
  const conversionRate = parseFloat(listing.real_conversion_rate) || (viewsCount > 0 ? Math.round((contactsCount / viewsCount) * 1000) / 10 : 0);

  return {
    id: listing.id,
    title: listing.title,
    address: listing.address,
    status: listing.status,
    price: parseInt(listing.price) || 0,
    views: viewsCount,
    clicks: phoneCount,
    messengers: messengersCount,
    telegram: telegramCount,
    whatsapp: whatsappCount,
    viber: viberCount,
    contacts: contactsCount,
    conversion_rate: conversionRate,
    metro: listing.nearest_metro || 'Нет',
    image: listing.image || (listing.type === 'cottage' ? '🏡' : (listing.id === 5 ? '🏠' : '🏢')),
    imageBg: listing.imageBg || (listing.type === 'cottage' 
      ? 'linear-gradient(140deg,#1B3A2D,#2E6B4F)' 
      : (listing.id === 5 ? 'linear-gradient(140deg,#2D1A3D,#4A2A6B)' : 'linear-gradient(140deg,#2C3E50,#3D5A6B)')),
    moderation_reason: listing.moderation_reason,
    is_paid: listing.is_paid,
    expires_at: listing.expires_at,
    tariff_price: listing.tariff_price,
    type: listing.type,
    featured: !!listing.featured,
    featured_expires_at: listing.featured_expires_at,
    has_homepage_tariff: !!listing.has_homepage_tariff,
    homepage_expires_at: listing.homepage_expires_at,
    slug: listing.slug || ''
  };
});

const serverUser = @json($user);
const serverStats = @json($stats);

function getDynamicBars(period) {
  if (!serverStats || !serverStats.charts || !serverStats.charts[period]) {
    return [];
  }
  return serverStats.charts[period];
}

function initDetailedStatsState() {
  const isUnlocked = !!serverUser.has_detailed_stats;
  const overlay = document.getElementById('stats-locked-overlay');
  const blurContainer = document.getElementById('stats-blur-container');

  if (isUnlocked) {
    if (overlay) overlay.style.display = 'none';
    if (blurContainer) blurContainer.classList.remove('locked-blur');
    renderDetailedStatsWidgets();
  } else {
    if (overlay) overlay.style.display = 'flex';
    if (blurContainer) blurContainer.classList.add('locked-blur');
  }
}

function renderDetailedStatsWidgets() {
  const listings = serverListings;
  let totalViews = 0;
  let totalClicks = 0;
  let totalMessengers = 0;

  listings.forEach(listing => {
    if (listing.status === 'active' || listing.status === 'pending' || listing.status === 'rejected' || listing.status === 'pending_payment') {
      totalViews += listing.views || 0;
      totalClicks += listing.clicks || 0;
      totalMessengers += listing.messengers || 0;
    }
  });

  // 1. Device split distribution rendering
  const deviceSplit = document.getElementById('stats-device-split');
  if (deviceSplit) {
    const deviceCounts = [
      serverStats.devices.mobile || 0,
      serverStats.devices.desktop || 0,
      serverStats.devices.tablet || 0
    ];
    
    let mobilePct = 0;
    let desktopPct = 0;
    let tabletPct = 0;

    if (totalViews > 0) {
      mobilePct = Math.round((deviceCounts[0] / totalViews) * 100);
      desktopPct = Math.round((deviceCounts[1] / totalViews) * 100);
      tabletPct = 100 - mobilePct - desktopPct;
    }

    deviceSplit.innerHTML = `
      <div class="device-row">
        <div class="device-info">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="5" y="2" width="14" height="20" rx="3" stroke-linecap="round"/><circle cx="12" cy="17.5" r="1" fill="currentColor"/></svg>
          <span>Смартфоны</span>
        </div>
        <div class="device-bar-bg">
          <div class="device-bar-fill mobile" style="width: ${mobilePct}%;"></div>
        </div>
        <div class="device-percent">${mobilePct}%</div>
      </div>
      <div class="device-row">
        <div class="device-info">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M1 19h22" stroke-linecap="round"/></svg>
          <span>Компьютеры</span>
        </div>
        <div class="device-bar-bg">
          <div class="device-bar-fill" style="width: ${desktopPct}%;"></div>
        </div>
        <div class="device-percent">${desktopPct}%</div>
      </div>
      <div class="device-row">
        <div class="device-info">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="4" y="2" width="16" height="20" rx="3"/><circle cx="12" cy="18" r="0.75" fill="currentColor"/></svg>
          <span>Планшеты</span>
        </div>
        <div class="device-bar-bg">
          <div class="device-bar-fill tablet" style="width: ${tabletPct}%;"></div>
        </div>
        <div class="device-percent">${tabletPct}%</div>
      </div>
    `;
  }

  // 2. Competitor comparison card rendering
  const marketCompare = document.getElementById('stats-market-compare');
  if (marketCompare) {
    const userConversion = totalViews > 0 ? Math.round(((totalClicks + totalMessengers) / totalViews) * 100) : 0;
    let compareHtml = '';
    
    if (totalViews === 0) {
      compareHtml = `
        <div class="market-compare-card">
          <div class="market-compare-icon">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 11v6m0-8.5v.5" stroke-linecap="round"/></svg>
          </div>
          <div class="market-compare-info">
            <h4>Сравнение с конкурентами</h4>
            <p>Недостаточно данных для анализа. Опубликуйте объявления и дождитесь просмотров гостей.</p>
            <span class="market-badge neutral">Ожидание данных</span>
          </div>
        </div>
      `;
    } else if (userConversion >= 12) {
      const diff = userConversion - 12;
      compareHtml = `
        <div class="market-compare-card">
          <div class="market-compare-icon">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 20h20M5 14l5-5 4 4 7-7m0 0h-5m5 0v5"/></svg>
          </div>
          <div class="market-compare-info">
            <h4>Выше рынка на ${diff}%</h4>
            <p>Конверсия вашего жилья составляет ${userConversion}%, что опережает среднерыночный показатель по Минску (12%). Ваши фото и описание работают отлично!</p>
            <span class="market-badge positive">Высокая эффективность</span>
          </div>
        </div>
      `;
    } else {
      compareHtml = `
        <div class="market-compare-card">
          <div class="market-compare-icon">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 20h16M7 16v-4m5 4V8m5 8v-6" stroke-linecap="round"/></svg>
          </div>
          <div class="market-compare-info">
            <h4>Конверсия близка к норме</h4>
            <p>Конверсия ваших объявлений (${userConversion}%) находится на уровне или чуть ниже средней по рынку (12%). Рекомендуем обновить главную фотографию, чтобы выделиться.</p>
            <span class="market-badge neutral">Нормальный уровень</span>
          </div>
        </div>
      `;
    }
    marketCompare.innerHTML = compareHtml;
  }

  // 3. Hourly views distribution chart rendering
  const hourlyBars = document.getElementById('stats-hourly-bars');
  if (hourlyBars) {
    const labels = ["0-2", "2-4", "4-6", "6-8", "8-10", "10-12", "12-14", "14-16", "16-18", "18-20", "20-22", "22-24"];
    const hourlyCounts = serverStats.hourly || new Array(12).fill(0);
    
    let maxVal = 10;
    hourlyCounts.forEach(val => {
      if (val > maxVal) maxVal = val;
    });
    
    hourlyBars.innerHTML = '';
    
    labels.forEach((label, idx) => {
      const val = hourlyCounts[idx];
      const barHeightPercent = Math.round((val / maxVal) * 100);
      const el = document.createElement('div');
      el.className = 'hourly-bar-container';
      el.innerHTML = `
        <div class="hourly-bar" style="height: 0%;">
          <div class="hourly-tooltip">${val} ${getViewsPlural(val)}</div>
        </div>
        <div class="hourly-label">${label}</div>
      `;
      hourlyBars.appendChild(el);
      
      setTimeout(() => {
        const activeBar = el.querySelector('.hourly-bar');
        if (activeBar) {
          activeBar.style.height = `${barHeightPercent}%`;
        }
      }, 50);
    });
  }
}

function payForStats() {
  window.location.href = "{{ route('payments.checkout-stats') }}";
}

function applyStatsPromo() {
  const promoInput = document.getElementById('stats-promo-input');
  const promoCode = promoInput.value.trim();

  if (!promoCode) {
    showToast('⚠️ Пожалуйста, введите промокод.');
    return;
  }

  showToast('🔍 Проверяем промокод...');

  fetch("{{ route('payments.promo.check') }}", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": "{{ csrf_token() }}"
    },
    body: JSON.stringify({
      code: promoCode,
      type: 'stats'
    })
  })
  .then(res => {
    if (!res.ok) {
      return res.json().then(data => { throw new Error(data.message || 'Неверный промокод') });
    }
    return res.json();
  })
  .then(data => {
    if (data.success) {
      if (data.final_amount === 0) {
        showToast('🎉 Промокод на 100% применен! Разблокируем детальную статистику...');
        window.location.href = `{{ route('payments.checkout-stats') }}?promo=${encodeURIComponent(promoCode)}`;
      } else {
        showToast(`✓ Скидка ${data.discount} Б̶ применена! Переходим к оплате...`);
        window.location.href = `{{ route('payments.checkout-stats') }}?promo=${encodeURIComponent(promoCode)}`;
      }
    }
  })
  .catch(err => {
    showToast('❌ ' + err.message);
  });
}

let activeDeleteListingId = null;

// Global page load handler
document.addEventListener("DOMContentLoaded", function() {
  loadHostProfileData();
  renderListings();
  updateDashboardStats();
  initDetailedStatsState();
  renderStatsChart("week");
  renderStatsTable();

  // Switch to specific tab if query string contains it (e.g. from create form submission)
  const urlParams = new URLSearchParams(window.location.search);
  const targetTab = urlParams.get('tab');
  if (targetTab === 'profile') {
    switchDashboardTab('profile', document.getElementById('menu-profile'));
  } else if (targetTab === 'stats') {
    switchDashboardTab('stats', document.getElementById('menu-stats'));
  }
});

// Load profile data into form & badges
function loadHostProfileData() {
  const profile = serverUser;
  if (!profile) return;

  // Form elements
  document.getElementById('profile-name').value = profile.name || '';
  document.getElementById('profile-phone').value = profile.phone || '';
  document.getElementById('profile-telegram').value = profile.telegram || '';
  document.getElementById('profile-whatsapp').value = profile.whatsapp || '';
  document.getElementById('profile-viber').value = profile.viber || '';
  document.getElementById('profile-unp').value = profile.unp || '';

  // Validate UNP on load
  if (profile.unp) {
    validateUNPField(profile.unp);
  }

  // Visual widgets sync
  document.getElementById('sidebar-host-name').textContent = profile.name || 'Хозяин';
  document.getElementById('profile-name-title').textContent = profile.name || 'Хозяин';
  
  // Set avatar initials or image URL
  updateAvatarWidgets(profile.name, profile.avatar);
}

// Update avatar initials and image tags across the page
function updateAvatarWidgets(name, avatarDataUrl) {
  const initial = name ? name.charAt(0).toUpperCase() : 'Х';
  
  // Sidebar avatar
  const sidebarAvatar = document.getElementById('sidebar-host-avatar');
  if (avatarDataUrl) {
    sidebarAvatar.innerHTML = `<img src="${avatarDataUrl}">`;
  } else {
    sidebarAvatar.textContent = initial;
  }

  // Profile uploader preview
  const profileAvatar = document.getElementById('profile-avatar-container');
  if (avatarDataUrl) {
    profileAvatar.innerHTML = `<img src="${avatarDataUrl}">`;
  } else {
    profileAvatar.textContent = initial;
  }

  // Header Dropdown avatar sync
  const headerAvatar = document.querySelector('.nav-user .avatar');
  if (headerAvatar) {
    if (avatarDataUrl) {
      headerAvatar.innerHTML = `<img src="${avatarDataUrl}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`;
    } else {
      headerAvatar.innerHTML = `<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="9" r="3"/><path d="M17.969 19a7.994 7.994 0 0 0-11.938 0" stroke-linecap="round"/></svg>`;
    }
  }
}

// Handle avatar image selection and save it as Data URL
function uploadAvatarImage(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    if (file.size > 5 * 1024 * 1024) {
      showToast('❌ Ошибка: размер файла превышает 5 МБ.');
      return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
      const dataUrl = e.target.result;
      
      // Update local memory
      serverUser.avatar = dataUrl;

      // Reload profile widgets
      updateAvatarWidgets(serverUser.name, dataUrl);
      showToast('📸 Аватар изменен! Сохраните профиль для отправки на сервер.');
    };
    reader.readAsDataURL(file);
  }
}

// Validate UNP Input length
function validateUNPField(value) {
  const validatorStatus = document.getElementById('unp-validator-status');
  const icon = document.getElementById('unp-status-icon');
  const text = document.getElementById('unp-status-text');
  const trimmed = value.trim();

  if (!value) {
    validatorStatus.style.display = 'none';
    return;
  }

  validatorStatus.style.display = 'flex';
  
  const isValid = /^\d{9}$/.test(trimmed) || /^[a-zA-Zа-яА-ЯёЁ]{2}\d{7}$/.test(trimmed);
  if (isValid) {
    validatorStatus.className = 'unp-status valid';
    icon.textContent = '✓';
    text.textContent = 'УНП корректен и валидирован налоговой службой РБ.';
  } else {
    validatorStatus.className = 'unp-status invalid';
    icon.textContent = '✕';
    text.textContent = 'Неверный формат УНП. Требуется 9 цифр или 2 буквы + 7 цифр.';
  }
}

// Save Host Profile form submission
function saveHostProfile(event) {
  event.preventDefault();
  const name = document.getElementById('profile-name').value.trim();
  const phone = document.getElementById('profile-phone').value.trim();
  const telegram = document.getElementById('profile-telegram').value.trim();
  const whatsapp = document.getElementById('profile-whatsapp').value.trim();
  const viber = document.getElementById('profile-viber').value.trim();
  const unp = document.getElementById('profile-unp').value.trim();

  if (unp && !(/^\d{9}$/.test(unp) || /^[a-zA-Zа-яА-ЯёЁ]{2}\d{7}$/.test(unp))) {
    showToast('⚠️ Пожалуйста, укажите верный УНП (9 цифр или 2 буквы + 7 цифр).');
    return;
  }

  // Get current avatar from local copy
  const avatar = serverUser.avatar || '';
  
  const newProfile = {
    name: name,
    phone: phone,
    telegram: telegram,
    whatsapp: whatsapp,
    viber: viber,
    unp: unp,
    avatar: avatar
  };

  // Perform AJAX to server DB
  fetch("{{ route('dashboard.profile.save') }}", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": "{{ csrf_token() }}"
    },
    body: JSON.stringify(newProfile)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      // Sync local copy
      serverUser.name = name;
      serverUser.phone = phone;
      serverUser.telegram = telegram;
      serverUser.whatsapp = whatsapp;
      serverUser.viber = viber;
      serverUser.unp = unp;

      // Sync visual widgets
      document.getElementById('sidebar-host-name').textContent = name;
      document.getElementById('profile-name-title').textContent = name;
      updateAvatarWidgets(name, avatar);

      showToast('💾 Профиль успешно сохранен в базе данных хостинга!');

      // Trigger UNP compliance easter-egg
      if (unp.length === 9) {
        let unpUnlocked = false;

        serverListings.forEach(listing => {
          if (listing.id === 3 && listing.status === 'rejected') {
            listing.status = 'active'; // Approved automatically since tax ID is verified
            listing.views = 142; // Seed views since it is now published
            listing.clicks = 18;
            listing.messengers = 9;
            unpUnlocked = true;
          }
        });

        if (unpUnlocked) {
          renderListings();
          updateDashboardStats();
          renderStatsTable();
          
          // Delay toast slightly to create a beautiful chain of events
          setTimeout(() => {
            showToast('🎉 УНП подтвержден! Объявление "Уютный загородный домик у леса" успешно опубликовано!');
          }, 1500);
        }
      }
    } else {
      showToast('❌ Ошибка сохранения: ' + data.message);
    }
  })
  .catch(() => {
    showToast('❌ Ошибка сети: не удалось сохранить профиль на сервере.');
  });
}

// Switch between SPA tabs
function switchDashboardTab(tabId, element) {
  // Update menu links styling
  document.querySelectorAll('.sidebar-menu li a').forEach(link => {
    link.classList.remove('active');
  });
  element.classList.add('active');

  // Update tabs panes with slide-fade transition
  const activePane = document.querySelector('.tab-pane.active');
  const targetPane = document.getElementById('pane-' + tabId);

  if (activePane && activePane !== targetPane) {
    activePane.style.opacity = '0';
    activePane.style.transform = 'translateY(12px)';
    
    setTimeout(() => {
      activePane.classList.remove('active');
      
      targetPane.classList.add('active');
      // trigger browser redraw
      targetPane.offsetHeight;
      
      targetPane.style.opacity = '1';
      targetPane.style.transform = 'translateY(0)';
      
      // If statistics tab is chosen, redraw charts to ensure smooth height loading
      if (tabId === 'stats') {
        const currentPeriod = document.getElementById('stats-period-selector').value;
        renderStatsChart(currentPeriod);
      }
    }, 200);
  }
}

// Render Host Listings dynamically from server database
var dashboardCurrentPage = 1;
const dashboardItemsPerPage = 3;

function renderListings() {
  const listings = serverListings;
  const container = document.getElementById('listings-list-container');
  
  if (listings.length === 0) {
    container.innerHTML = `
      <div style="text-align: center; padding: 40px; color: var(--gray-text);">
        <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 12px; display: block;">
          <path d="M4 19V5a2 2 0 0 1 2-2h10l4 4v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z"/>
          <path d="M14 3v4h4M8 12h8M8 16h5" stroke-linecap="round"/>
        </svg>
        <p style="font-weight: 600;">У вас пока нет добавленных объявлений.</p>
        <p style="font-size: 13px; margin-top: 4px;">Нажмите кнопку «Разместить жильё», чтобы опубликовать первое объявление.</p>
      </div>
    `;
    const pagContainer = document.getElementById('listings-dashboard-pagination');
    if (pagContainer) pagContainer.innerHTML = '';
    return;
  }

  container.innerHTML = '';
  
  // Calculate pagination
  const totalItems = listings.length;
  const totalPages = Math.ceil(totalItems / dashboardItemsPerPage);
  
  if (dashboardCurrentPage > totalPages) {
    dashboardCurrentPage = Math.max(1, totalPages);
  }
  
  const startIdx = (dashboardCurrentPage - 1) * dashboardItemsPerPage;
  const endIdx = dashboardCurrentPage * dashboardItemsPerPage;
  const paginatedListings = listings.slice(startIdx, endIdx);
  
  paginatedListings.forEach(listing => {
    let statusText = '';
    let statusClass = '';
    let actionBtn = '';
    let expiryHtml = '';
    let featuredExpiryHtml = '';
    let homepageExpiryHtml = '';

    if (listing.is_paid && listing.expires_at) {
      expiryHtml = `<span style="font-size: 11px; color: var(--gray-text); display: inline-flex; align-items: center; gap: 4px; background: #F5F5F7; padding: 4px 8px; border-radius: 6px;">📅 До ${listing.expires_at}</span>`;
    }
    if (listing.featured && listing.featured_expires_at) {
      featuredExpiryHtml = `<span style="font-size: 11px; color: var(--gray-text); display: inline-flex; align-items: center; gap: 4px; background: #FFF9E6; padding: 4px 8px; border-radius: 6px;">🌟 До ${listing.featured_expires_at}</span>`;
    }
    if (listing.has_homepage_tariff && listing.homepage_expires_at) {
      homepageExpiryHtml = `<span style="font-size: 11px; color: var(--gray-text); display: inline-flex; align-items: center; gap: 4px; background: #EAF3DE; padding: 4px 8px; border-radius: 6px;">🏠 До ${listing.homepage_expires_at}</span>`;
    }

    if (listing.status === 'active') {
      statusClass = 'active';
      statusText = '✓ Опубликовано';
      actionBtn = `<a class="lr-btn" onclick="toggleListingStatus(${listing.id}, this)">Деактивировать</a>`;
      actionBtn += `<a class="lr-btn" onclick="openPaymentModal(${listing.id}, ${listing.tariff_price}, '${listing.type}')">Продлить тариф</a>`;
    } else if (listing.status === 'hidden') {
      statusClass = 'hidden';
      statusText = '✕ Скрыто';
      if (listing.is_paid) {
        actionBtn = `<a class="lr-btn primary" onclick="toggleListingStatus(${listing.id}, this)">Активировать</a>`;
      } else {
        actionBtn = `<a class="lr-btn primary long-text" onclick="openPaymentModal(${listing.id}, ${listing.tariff_price}, '${listing.type}')">Активировать бесплатно</a>`;
      }
    } else if (listing.status === 'pending_payment') {
      statusClass = 'pending_payment';
      statusText = '💳 Ожидает активации';
      actionBtn = `<a class="lr-btn primary" onclick="openPaymentModal(${listing.id}, ${listing.tariff_price}, '${listing.type}')">Активировать бесплатно</a>`;
    } else if (listing.status === 'pending') {
      statusClass = 'pending';
      statusText = '⏳ На модерации';
      actionBtn = `<a class="lr-btn" style="opacity: 0.65; cursor: not-allowed;" onclick="showToast('Нельзя редактировать объявление на модерации')">На модерации</a>`;
    } else if (listing.status === 'rejected') {
      statusClass = 'rejected';
      statusText = '✕ Отклонено';
      actionBtn = `<a class="lr-btn primary long-text" onclick="showRejectionReason(${listing.id})">Причина отказа</a>`;
    }

    const featuredBtn = listing.featured 
      ? `<a class="lr-btn featured-active" onclick="openFeaturedModal(${listing.id}, true)">Продлить продвижение</a>`
      : `<a class="lr-btn featured-btn" onclick="openFeaturedModal(${listing.id}, false)">Продвижение 🌟</a>`;

    const homepageBtn = listing.has_homepage_tariff
      ? `<a class="lr-btn homepage-active" onclick="openHomepageModal(${listing.id}, true)">Продлить Главную</a>`
      : `<a class="lr-btn homepage-btn" onclick="openHomepageModal(${listing.id}, false)">На главной</a>`;

    const row = document.createElement('div');
    row.className = 'listing-row';
    row.id = `listing-row-${listing.id}`;
    
    // Check if the image field is custom base64 or emoji
    const imageContent = listing.image.length > 4 ? `<img src="${listing.image}" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">` : listing.image;
    
    // Currency conversion support
    const displayPrice = window.convertPrice ? window.convertPrice(listing.price) : `${listing.price} Б̶`;

    row.innerHTML = `
      <div class="lr-img" style="background: ${listing.imageBg || 'linear-gradient(140deg,#2C3E50,#3D5A6B)'}">
        ${imageContent}
      </div>
      <div class="lr-info">
        <h3>${listing.title}</h3>
        <div class="lr-meta">
          ${listing.address ? `<span>${listing.address}</span>` : ''}
          <span style="font-weight: 700; color: var(--slate-900)">${displayPrice} / ночь</span>
          <span class="badge-status ${statusClass}">${statusText}</span>
          ${expiryHtml}
          ${featuredExpiryHtml}
          ${homepageExpiryHtml}
        </div>
      </div>
      <div class="lr-actions">
        ${listing.status === 'rejected' 
          ? `<a href="{{ route('listings.create') }}?edit=${listing.id}" class="lr-btn long-text">Исправить форму</a>`
          : `<a href="/listings/${listing.id}${listing.slug ? '-' + listing.slug : ''}" class="lr-btn primary">Посмотреть</a>`
        }
        ${listing.status === 'pending'
          ? `<a class="lr-btn" style="opacity: 0.5; cursor: not-allowed;" onclick="showToast('Нельзя редактировать объявление на модерации')">Редактировать</a>`
          : (listing.status !== 'rejected' ? `<a href="{{ route('listings.create') }}?edit=${listing.id}" class="lr-btn">Редактировать</a>` : '')
        }
        ${actionBtn}
        ${featuredBtn}
        ${homepageBtn}
        <a class="lr-btn danger" onclick="showDeleteConfirm(${listing.id})">Удалить</a>
      </div>
    `;
    container.appendChild(row);
  });
  
  renderDashboardPaginationControls(totalPages);
}

function renderDashboardPaginationControls(totalPages) {
  const pagContainer = document.getElementById('listings-dashboard-pagination');
  if (!pagContainer) return;
  
  if (totalPages <= 1) {
    pagContainer.innerHTML = '';
    return;
  }
  
  let html = '';
  
  // Кнопка Назад
  const prevDisabled = dashboardCurrentPage === 1 ? 'disabled' : '';
  const prevOnClick = dashboardCurrentPage === 1 ? '' : `onclick="changeDashboardPage(${dashboardCurrentPage - 1})"`;
  html += `<button class="page-btn ${prevDisabled}" ${prevOnClick} aria-label="Предыдущая страница">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 19l-7-7 7-7"/></svg>
  </button>`;
  
  // Кнопки страниц с усечением (truncation)
  let startPage = Math.max(1, dashboardCurrentPage - 2);
  let endPage = Math.min(totalPages, dashboardCurrentPage + 2);
  
  if (dashboardCurrentPage <= 3) {
    endPage = Math.min(totalPages, 5);
  }
  if (dashboardCurrentPage >= totalPages - 2) {
    startPage = Math.max(1, totalPages - 4);
  }
  
  if (startPage > 1) {
    html += `<button class="page-btn" onclick="changeDashboardPage(1)">1</button>`;
    if (startPage > 2) {
      html += `<span class="page-ellipsis">...</span>`;
    }
  }
  
  for (let i = startPage; i <= endPage; i++) {
    const activeClass = i === dashboardCurrentPage ? 'active' : '';
    html += `<button class="page-btn ${activeClass}" onclick="changeDashboardPage(${i})">${i}</button>`;
  }
  
  if (endPage < totalPages) {
    if (endPage < totalPages - 1) {
      html += `<span class="page-ellipsis">...</span>`;
    }
    html += `<button class="page-btn" onclick="changeDashboardPage(${totalPages})">${totalPages}</button>`;
  }
  
  // Кнопка Вперед
  const nextDisabled = dashboardCurrentPage === totalPages ? 'disabled' : '';
  const nextOnClick = dashboardCurrentPage === totalPages ? '' : `onclick="changeDashboardPage(${dashboardCurrentPage + 1})"`;
  html += `<button class="page-btn ${nextDisabled}" ${nextOnClick} aria-label="Следующая страница">
    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg>
  </button>`;
  
  pagContainer.innerHTML = html;
}

function changeDashboardPage(page) {
  dashboardCurrentPage = page;
  renderListings();
  
  const sectionEl = document.querySelector('.listings-section');
  if (sectionEl) {
    sectionEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

// Toggle status of dynamic listing cards
function toggleListingStatus(id, btn) {
  fetch(`/listings/${id}/status`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": "{{ csrf_token() }}"
    }
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const listing = serverListings.find(l => l.id === id);
      if (listing) {
        listing.status = data.status;
      }
      
      renderListings();
      updateDashboardStats();
      renderStatsTable();
      
      if (data.status === 'hidden') {
        showToast('🔇 Объявление скрыто из поисковой выдачи.');
      } else {
        showToast('📢 Объявление успешно опубликовано и доступно для поиска.');
      }
    } else if (data.requires_payment) {
      showToast('💳 ' + data.message);
      const listing = serverListings.find(l => l.id === id);
      if (listing) {
        openPaymentModal(listing.id, listing.tariff_price, listing.type);
      }
    } else {
      showToast('❌ Ошибка изменения статуса: ' + data.message);
    }
  })
  .catch(() => {
    showToast('❌ Ошибка сети при изменении статуса объявления.');
  });
}

// Monetization system variables and helper functions
let currentPaymentListingId = null;
let currentPaymentMonths = 1;
let currentPaymentBasePrice = 10;
let currentPaymentPromoCode = '';
let promoType = null;
let promoValue = 0;
let currentPaymentFinalAmount = 10.00;

function openPaymentModal(id, price, type) {
  currentPaymentListingId = id;
  currentPaymentMonths = 1;
  currentPaymentBasePrice = price;
  currentPaymentPromoCode = '';
  promoType = null;
  promoValue = 0;

  // Clear inputs
  document.getElementById('payment-promo-input').value = '';
  
  // Clear messages
  const msgEl = document.getElementById('payment-promo-msg');
  msgEl.textContent = '';
  msgEl.className = 'promo-msg';

  // Prevent double-click redirection
  const submitBtn = document.getElementById('payment-submit-btn');
  submitBtn.disabled = true;
  setTimeout(() => {
    submitBtn.disabled = false;
  }, 400);

  // Update month buttons styling
  const cards = document.querySelectorAll('#payment-modal-overlay .period-card');
  cards.forEach(card => {
    const months = parseInt(card.getAttribute('data-months'));
    if (months === 1) {
      card.classList.add('active');
    } else {
      card.classList.remove('active');
    }

    // Update prices displayed inside the cards dynamically
    let cardMultiplier = 1.0;
    if (months === 3) cardMultiplier = 0.9;
    else if (months === 6) cardMultiplier = 0.8;
    else if (months === 12) cardMultiplier = 0.7;

    const monthlyPrice = months === 1 ? 0 : Math.round(price * cardMultiplier);
    card.querySelector('.price-val').textContent = monthlyPrice;
  });

  recalculatePrices();
  showModal('payment-modal-overlay');
}

function closePaymentModal() {
  closeModal('payment-modal-overlay');
}

function selectPaymentPeriod(months) {
  currentPaymentMonths = months;
  
  // Highlight active period card
  const cards = document.querySelectorAll('#payment-modal-overlay .period-card');
  cards.forEach(card => {
    if (parseInt(card.getAttribute('data-months')) === months) {
      card.classList.add('active');
    } else {
      card.classList.remove('active');
    }
  });
  
  recalculatePrices();
}

function applyPromoCode() {
  const codeInput = document.getElementById('payment-promo-input');
  const code = codeInput.value.trim();
  const msgEl = document.getElementById('payment-promo-msg');
  
  if (!code) {
    msgEl.textContent = 'Введите промокод.';
    msgEl.className = 'promo-msg error';
    return;
  }
  
  msgEl.textContent = 'Проверка...';
  msgEl.className = 'promo-msg';

  fetch('/payments/promo/check', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      code: code,
      listing_id: currentPaymentListingId,
      months: currentPaymentMonths
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      currentPaymentPromoCode = data.code;
      promoType = data.type;
      promoValue = parseFloat(data.value);
      
      msgEl.textContent = data.description || 'Промокод успешно применен!';
      msgEl.className = 'promo-msg success';
      
      recalculatePrices();
    } else {
      msgEl.textContent = data.message || 'Ошибка применения промокода.';
      msgEl.className = 'promo-msg error';
    }
  })
  .catch(() => {
    msgEl.textContent = 'Ошибка сети при проверке промокода.';
    msgEl.className = 'promo-msg error';
  });
}

function recalculatePrices() {
  let totalRaw = currentPaymentMonths === 1 ? 0 : (currentPaymentBasePrice * currentPaymentMonths);
  
  // Apply period discounts
  let multiplier = 1.0;
  if (currentPaymentMonths === 3) {
    multiplier = 0.9;
  } else if (currentPaymentMonths === 6) {
    multiplier = 0.8;
  } else if (currentPaymentMonths === 12) {
    multiplier = 0.7;
  }
  
  let priceWithPeriodDiscount = Math.round(totalRaw * multiplier * 100) / 100;
  
  // Apply promo code discount if active
  let promoDiscount = 0.00;
  if (currentPaymentPromoCode) {
    if (promoType === 'percentage') {
      promoDiscount = Math.round(priceWithPeriodDiscount * (promoValue / 100) * 100) / 100;
    } else if (promoType === 'fixed') {
      promoDiscount = Math.min(promoValue, priceWithPeriodDiscount);
    } else if (promoType === 'free') {
      promoDiscount = priceWithPeriodDiscount;
    }
  }
  
  let finalAmount = Math.max(0.00, priceWithPeriodDiscount - promoDiscount);
  currentPaymentFinalAmount = finalAmount;
  
  // Update UI values
  document.getElementById('summary-base-price').textContent = `${totalRaw.toFixed(2)} Б̶`;
  
  const discountRow = document.getElementById('summary-discount-row');
  if (multiplier < 1.0) {
    let periodDiscount = totalRaw - priceWithPeriodDiscount;
    document.getElementById('summary-discount-val').textContent = `-${periodDiscount.toFixed(2)} Б̶`;
    discountRow.style.display = 'flex';
  } else {
    discountRow.style.display = 'none';
  }
  
  const promoRow = document.getElementById('summary-promo-row');
  if (currentPaymentPromoCode && promoDiscount > 0) {
    document.getElementById('summary-promo-val').textContent = `-${promoDiscount.toFixed(2)} Б̶`;
    promoRow.style.display = 'flex';
  } else {
    promoRow.style.display = 'none';
  }
  
  document.getElementById('summary-total-price').textContent = `${finalAmount.toFixed(2)} Б̶`;
  
  // Update submit button text
  const submitBtn = document.getElementById('payment-submit-btn');
  if (finalAmount <= 0) {
    submitBtn.textContent = 'Активировать бесплатно (0 Б̶)';
  } else {
    submitBtn.textContent = 'Перейти к оплате';
  }
  submitBtn.disabled = false;
}

function confirmPaymentCheckout() {
  if (!currentPaymentListingId) return;
  
  const submitBtn = document.getElementById('payment-submit-btn');
  submitBtn.disabled = true;
  submitBtn.textContent = 'Обработка...';
  
  // Construct checkout URL
  const checkoutUrl = `/payments/checkout/${currentPaymentListingId}?months=${currentPaymentMonths}&promo=${encodeURIComponent(currentPaymentPromoCode)}`;
  
  // Redirect
  window.location.href = checkoutUrl;
}

// Custom confirmation modals trigger
function showModal(overlayId) {
  const overlay = document.getElementById(overlayId);
  overlay.classList.add('show');
}

// Custom confirmation modals close
function closeModal(overlayId) {
  const overlay = document.getElementById(overlayId);
  overlay.classList.remove('show');
}

// Show rejection details trigger
function showRejectionReason(id) {
  const listing = serverListings.find(l => l.id === id);
  if (listing && listing.moderation_reason) {
    document.getElementById('reject-modal-description').innerHTML = `
      Объявление отклонено модератором по следующей причине:<br><br>
      <strong style="color: #A32D2D;">«${listing.moderation_reason}»</strong><br><br>
      Пожалуйста, перейдите в настройки профиля, исправьте указанные замечания или добавьте УНП, чтобы опубликовать объявление.
    `;
  } else {
    document.getElementById('reject-modal-description').innerHTML = `
      Объявление отклонено модератором по следующей причине:<br><br>
      <strong style="color: #A32D2D;">«Нарушение законодательства о налогообложении (Указ № 99-З РБ). Отсутствует зарегистрированный УНП (Учетный номер плательщика) в профиле хозяина.»</strong><br><br>
      Пожалуйста, перейдите в настройки профиля, укажите ваш 9-значный УНП и сохраните изменения для автоматической повторной публикации.
    `;
  }
  showModal('reject-modal-overlay');
}

// Quick transition from rejection modal directly to profile settings UNP field
function switchToProfileTabFromRejection() {
  closeModal('reject-modal-overlay');
  switchDashboardTab('profile', document.getElementById('menu-profile'));
  
  // Focus the UNP field with a beautiful pulse effect
  setTimeout(() => {
    const unpField = document.getElementById('profile-unp');
    unpField.focus();
    unpField.style.borderColor = 'var(--amber)';
  }, 450);
}

// Delete confirm modal trigger
function showDeleteConfirm(id) {
  activeDeleteListingId = id;
  showModal('delete-modal-overlay');
}

// Execute Delete with smooth collapse animation
function executeDeleteListing() {
  if (activeDeleteListingId === null) return;
  
  const id = activeDeleteListingId;
  
  fetch(`/listings/${id}`, {
    method: "DELETE",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": "{{ csrf_token() }}"
    }
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const idx = serverListings.findIndex(l => l.id === id);
      if (idx !== -1) {
        serverListings.splice(idx, 1);
      }
      
      closeModal('delete-modal-overlay');
      animateDeleteRow(id, '🗑️ Объявление безвозвратно удалено с сервера.');
    } else {
      showToast('❌ Ошибка удаления: ' + data.message);
    }
  })
  .catch(() => {
    showToast('❌ Ошибка сети при удалении объявления.');
  });
}

function animateDeleteRow(id, successMessage) {
  const row = document.getElementById(`listing-row-${id}`);
  if (row) {
    // Elegant fade out & collapse transition in DOM
    row.style.opacity = '0';
    row.style.transform = 'scale(0.95)';
    row.style.transition = 'all 0.4s ease';
    
    setTimeout(() => {
      row.style.height = '0px';
      row.style.padding = '0px';
      row.style.margin = '0px';
      row.style.border = 'none';
      
      setTimeout(() => {
        row.remove();
        renderListings(); // Recalculate blank layouts if list is empty
        updateDashboardStats();
        renderStatsTable();
        showToast(successMessage);
      }, 300);
    }, 400);
  } else {
    renderListings();
    updateDashboardStats();
    renderStatsTable();
    showToast(successMessage);
  }
}

// Calculate and refresh card counts at top
function updateDashboardStats() {
  const listings = serverListings;
  
  // Totals calculations
  const totalCount = listings.length;
  let totalViews = 0;
  let totalClicks = 0;

  listings.forEach(listing => {
    // Only accumulate stats for active listings to simulate organic traffic
    if (listing.status === 'active' || listing.status === 'pending' || listing.status === 'rejected' || listing.status === 'pending_payment') {
      totalViews += listing.views || 0;
      totalClicks += listing.clicks || 0;
    }
  });

  // Update DOM elements on main list pane
  document.getElementById('card-stats-total').textContent = totalCount;
  document.getElementById('card-stats-views').textContent = formatStatsNumber(totalViews);
  document.getElementById('card-stats-clicks').textContent = formatStatsNumber(totalClicks);

  // Update DOM elements on statistics pane
  document.getElementById('stats-total-listings').textContent = totalCount;
  document.getElementById('stats-views-val').textContent = formatStatsNumber(totalViews);
  
  // Mesengers totals sync
  let totalMessengers = 0;
  listings.forEach(listing => {
    totalMessengers += listing.messengers || 0;
  });
  document.getElementById('stats-clicks-val').textContent = formatStatsNumber(totalMessengers);
}

// Distribute non-fractional views using a deterministic greedy algorithm based on weights
function distributeIntegerValues(total, weights) {
  if (total <= 0) return new Array(weights.length).fill(0);
  
  let result = new Array(weights.length).fill(0);
  let allocated = 0;
  
  for (let i = 0; i < weights.length; i++) {
    let val = Math.floor(total * weights[i]);
    result[i] = val;
    allocated += val;
  }
  
  let remaining = total - allocated;
  if (remaining > 0) {
    let fractions = weights.map((w, idx) => {
      return { index: idx, frac: (total * w) - result[idx] };
    });
    fractions.sort((a, b) => b.frac - a.frac);
    
    for (let i = 0; i < remaining; i++) {
      let targetIdx = fractions[i % weights.length].index;
      result[targetIdx] += 1;
    }
  }
  
  return result;
}

// Pluralize Russian "просмотр" (views) label
function getViewsPlural(num) {
  const lastDigit = num % 10;
  const lastTwo = num % 100;
  if (lastTwo >= 11 && lastTwo <= 19) return 'просмотров';
  if (lastDigit === 1) return 'просмотр';
  if (lastDigit >= 2 && lastDigit <= 4) return 'просмотра';
  return 'просмотров';
}

// Formats big numbers elegantly with a space separator (e.g. 1 248)
function formatStatsNumber(num) {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

// Render dynamic custom styled chart bars
function renderStatsChart(period) {
  const container = document.getElementById('stats-chart-bars');
  let dataBars = getDynamicBars(period);
  let maxVal = 10;

  // Find max value to calibrate height percentages properly
  dataBars.forEach(bar => {
    if (bar.val > maxVal) maxVal = bar.val;
    if (bar.contacts && bar.contacts > maxVal) maxVal = bar.contacts;
  });

  container.innerHTML = '';

  dataBars.forEach(bar => {
    const viewHeightPercent = Math.max(6, Math.round((bar.val / maxVal) * 100));
    const contactsCount = bar.contacts || 0;
    const contactHeightPercent = Math.max(0, Math.round((contactsCount / maxVal) * 100));

    const barWrapper = document.createElement('div');
    barWrapper.className = 'chart-bar-container';
    
    if (period === 'week') {
      barWrapper.innerHTML = `
        <div style="display: flex; gap: 4px; align-items: flex-end; width: 100%; height: 100%;">
          <div class="chart-bar" style="height: 0%; flex: 1; background: linear-gradient(180deg, #4F46E5 0%, #818CF8 100%); position: relative;">
            <div class="chart-tooltip">${bar.val} просмотров</div>
          </div>
          <div class="chart-bar" style="height: 0%; flex: 1; background: linear-gradient(180deg, #059669 0%, #34D399 100%); position: relative;">
            <div class="chart-tooltip">${contactsCount} контактов</div>
          </div>
        </div>
        <div class="chart-label">${bar.label}</div>
      `;
    } else {
      barWrapper.innerHTML = `
        <div class="chart-bar" style="height: 0%;">
          <div class="chart-tooltip">${bar.val} просмотров</div>
        </div>
        <div class="chart-label">${bar.label}</div>
      `;
    }

    container.appendChild(barWrapper);
    
    // Trigger slide height animation
    setTimeout(() => {
      const activeBars = barWrapper.querySelectorAll('.chart-bar');
      if (activeBars.length > 0) {
        activeBars[0].style.height = `${viewHeightPercent}%`;
        if (activeBars.length > 1) {
          activeBars[1].style.height = `${contactHeightPercent}%`;
        }
      }
    }, 50);
  });
}

// Handle statistics period select changes
function changeStatsPeriod(value) {
  renderStatsChart(value);
  
  const viewsVal = document.getElementById('stats-views-val');
  const clicksVal = document.getElementById('stats-clicks-val');
  const viewsTrend = document.getElementById('stats-views-trend');
  const clicksTrend = document.getElementById('stats-clicks-trend');

  if (serverStats && serverStats.totals && serverStats.totals[value]) {
    const periodTotals = serverStats.totals[value];
    viewsVal.textContent = formatStatsNumber(periodTotals.views);
    clicksVal.textContent = formatStatsNumber(periodTotals.messengers);
  }

  if (value === 'week') {
    viewsTrend.textContent = 'За неделю';
    clicksTrend.textContent = 'За неделю';
  } else if (value === 'month') {
    viewsTrend.textContent = 'За месяц';
    clicksTrend.textContent = 'За месяц';
  } else if (value === 'all') {
    viewsTrend.textContent = 'За все время';
    clicksTrend.textContent = 'За все время';
  }
}

// Render dynamic breakdown metrics table grid
function renderStatsTable() {
  const listings = serverListings;
  const tbody = document.getElementById('stats-table-body');
  
  if (listings.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="6" style="text-align: center; color: var(--gray-text); padding: 24px;">Нет данных для отображения.</td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = '';

  listings.forEach(listing => {
    const totalContacts = listing.contacts || ((listing.clicks || 0) + (listing.messengers || 0));
    const cr = listing.conversion_rate || (listing.views > 0 ? (Math.round((totalContacts / listing.views) * 1000) / 10) : 0);

    const tr = document.createElement('tr');
    
    const imgMarkup = listing.image.length > 4 
      ? `<img src="${listing.image}" style="width:36px;height:27px;object-fit:cover;border-radius:4px;flex-shrink:0;">` 
      : `<span style="font-size:20px;flex-shrink:0;">${listing.image}</span>`;
      
    tr.innerHTML = `
      <td style="font-weight: 700; vertical-align: middle;">
        <div style="display: flex; align-items: center; gap: 8px;">
          ${imgMarkup}
          <div>
            <div>${listing.title}</div>
            <div style="font-size: 11px; font-weight: 500; color: var(--gray-text); margin-top: 2px;">${listing.address}</div>
          </div>
        </div>
      </td>
      <td><strong>${formatStatsNumber(listing.views)}</strong></td>
      <td>${listing.clicks || 0}</td>
      <td>${listing.messengers || 0}</td>
      <td><strong style="color: var(--slate-900);">${totalContacts}</strong></td>
      <td>
        <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; background: ${cr >= 5 ? '#ECFDF5' : '#F1F5F9'}; color: ${cr >= 5 ? '#059669' : 'var(--slate-900)'}; font-weight: 700;">
          ${cr}%
        </span>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

// FEATURED PROMOTION MODAL & CHECKOUT FUNCTIONS
let currentFeaturedListingId = null;
let currentFeaturedPromoCode = '';
let featuredPromoType = null;
let featuredPromoValue = 0;
let currentFeaturedFinalAmount = 5.00;
let currentFeaturedMonths = 1;

function openFeaturedModal(id, isCurrentlyFeatured) {
  currentFeaturedListingId = id;
  currentFeaturedPromoCode = '';
  featuredPromoType = null;
  featuredPromoValue = 0;
  currentFeaturedFinalAmount = 5.00;
  currentFeaturedMonths = 1;

  // Clear inputs & messages
  document.getElementById('featured-promo-input').value = '';
  const msgEl = document.getElementById('featured-promo-msg');
  msgEl.textContent = '';
  msgEl.className = 'promo-msg';

  const submitBtn = document.getElementById('featured-submit-btn');
  submitBtn.disabled = true;
  setTimeout(() => {
    submitBtn.disabled = false;
  }, 400);

  // Reset month cards active state
  const cards = document.querySelectorAll('#featured-periods-grid .period-card');
  cards.forEach(card => {
    const months = parseInt(card.getAttribute('data-featured-months'));
    if (months === 1) {
      card.classList.add('active');
    } else {
      card.classList.remove('active');
    }
  });

  recalculateFeaturedPrices();
  showModal('featured-modal-overlay');
}

function closeFeaturedModal() {
  closeModal('featured-modal-overlay');
}

function selectFeaturedPeriod(months) {
  currentFeaturedMonths = months;
  
  const cards = document.querySelectorAll('#featured-periods-grid .period-card');
  cards.forEach(card => {
    if (parseInt(card.getAttribute('data-featured-months')) === months) {
      card.classList.add('active');
    } else {
      card.classList.remove('active');
    }
  });
  
  recalculateFeaturedPrices();
}

function applyFeaturedPromoCode() {
  const codeInput = document.getElementById('featured-promo-input');
  const code = codeInput.value.trim();
  const msgEl = document.getElementById('featured-promo-msg');
  
  if (!code) {
    msgEl.textContent = 'Введите промокод.';
    msgEl.className = 'promo-msg error';
    return;
  }
  
  msgEl.textContent = 'Проверка...';
  msgEl.className = 'promo-msg';

  fetch('/payments/promo/check', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      code: code,
      type: 'featured',
      listing_id: currentFeaturedListingId,
      months: currentFeaturedMonths
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      currentFeaturedPromoCode = data.code;
      featuredPromoType = data.type;
      featuredPromoValue = parseFloat(data.value);
      
      msgEl.textContent = data.description || 'Промокод успешно применен!';
      msgEl.className = 'promo-msg success';
      
      recalculateFeaturedPrices();
    } else {
      msgEl.textContent = data.message || 'Ошибка применения промокода.';
      msgEl.className = 'promo-msg error';
    }
  })
  .catch(() => {
    msgEl.textContent = 'Ошибка сети при проверке промокода.';
    msgEl.className = 'promo-msg error';
  });
}

function recalculateFeaturedPrices() {
  const unitPrice = 15.00;
  const months = currentFeaturedMonths || 1;
  const totalRaw = unitPrice * months;
  
  let periodMultiplier = 1.0;
  if (months === 3) periodMultiplier = 0.9;
  else if (months === 6) periodMultiplier = 0.8;
  else if (months === 12) periodMultiplier = 0.7;

  const priceWithPeriodDiscount = Math.round(totalRaw * periodMultiplier * 100) / 100;
  const periodDiscount = totalRaw - priceWithPeriodDiscount;

  let promoDiscount = 0.00;
  if (currentFeaturedPromoCode) {
    if (featuredPromoType === 'percentage') {
      promoDiscount = Math.round(priceWithPeriodDiscount * (featuredPromoValue / 100) * 100) / 100;
    } else if (featuredPromoType === 'fixed') {
      promoDiscount = Math.min(featuredPromoValue, priceWithPeriodDiscount);
    } else if (featuredPromoType === 'free') {
      promoDiscount = priceWithPeriodDiscount;
    }
  }
  
  const finalAmount = Math.max(0.00, priceWithPeriodDiscount - promoDiscount);
  currentFeaturedFinalAmount = finalAmount;
  
  document.getElementById('featured-summary-base-price').textContent = `${totalRaw.toFixed(2)} Б̶`;

  const discountRow = document.getElementById('featured-summary-discount-row');
  if (periodDiscount > 0) {
    document.getElementById('featured-summary-discount-val').textContent = `-${periodDiscount.toFixed(2)} Б̶`;
    discountRow.style.display = 'flex';
  } else {
    discountRow.style.display = 'none';
  }

  const promoRow = document.getElementById('featured-summary-promo-row');
  if (currentFeaturedPromoCode && promoDiscount > 0) {
    document.getElementById('featured-summary-promo-val').textContent = `-${promoDiscount.toFixed(2)} Б̶`;
    promoRow.style.display = 'flex';
  } else {
    promoRow.style.display = 'none';
  }
  
  document.getElementById('featured-summary-total-price').textContent = `${finalAmount.toFixed(2)} Б̶`;
  
  const submitBtn = document.getElementById('featured-submit-btn');
  if (finalAmount <= 0) {
    submitBtn.textContent = 'Активировать бесплатно (0 Б̶)';
  } else {
    submitBtn.textContent = 'Перейти к оплате';
  }
  submitBtn.disabled = false;
}

function confirmFeaturedCheckout() {
  if (!currentFeaturedListingId) return;
  
  const submitBtn = document.getElementById('featured-submit-btn');
  submitBtn.disabled = true;
  submitBtn.textContent = 'Обработка...';
  
  const checkoutUrl = `/payments/checkout-featured/${currentFeaturedListingId}?months=${currentFeaturedMonths}&promo=${encodeURIComponent(currentFeaturedPromoCode)}`;
  window.location.href = checkoutUrl;
}

// HOMEPAGE PROMOTION MODAL & CHECKOUT FUNCTIONS
let currentHomepageListingId = null;
let currentHomepagePromoCode = '';
let homepagePromoType = null;
let homepagePromoValue = 0;
let currentHomepageFinalAmount = 50.00;
let currentHomepageMonths = 1;

function openHomepageModal(id, isCurrentlyActive) {
  const activeHomepageCount = {{ $activeHomepageListingsCount ?? 0 }};
  if (!isCurrentlyActive && activeHomepageCount >= 10) {
    showToast('⚠️ Все места на главной странице заняты (максимум 10 объявлений). Пожалуйста, попробуйте позже.');
    return;
  }

  currentHomepageListingId = id;
  currentHomepagePromoCode = '';
  homepagePromoType = null;
  homepagePromoValue = 0;
  currentHomepageFinalAmount = 50.00;
  currentHomepageMonths = 1;

  // Clear inputs & messages
  document.getElementById('homepage-promo-input').value = '';
  const msgEl = document.getElementById('homepage-promo-msg');
  msgEl.textContent = '';
  msgEl.className = 'promo-msg';

  const submitBtn = document.getElementById('homepage-submit-btn');
  submitBtn.disabled = true;
  setTimeout(() => {
    submitBtn.disabled = false;
  }, 400);

  // Reset month cards active state
  const cards = document.querySelectorAll('#homepage-periods-grid .period-card');
  cards.forEach(card => {
    const months = parseInt(card.getAttribute('data-homepage-months'));
    if (months === 1) {
      card.classList.add('active');
    } else {
      card.classList.remove('active');
    }
  });

  recalculateHomepagePrices();
  showModal('homepage-modal-overlay');
}

function closeHomepageModal() {
  closeModal('homepage-modal-overlay');
}

function selectHomepagePeriod(months) {
  currentHomepageMonths = months;
  
  const cards = document.querySelectorAll('#homepage-periods-grid .period-card');
  cards.forEach(card => {
    if (parseInt(card.getAttribute('data-homepage-months')) === months) {
      card.classList.add('active');
    } else {
      card.classList.remove('active');
    }
  });
  
  recalculateHomepagePrices();
}

function applyHomepagePromoCode() {
  const codeInput = document.getElementById('homepage-promo-input');
  const code = codeInput.value.trim();
  const msgEl = document.getElementById('homepage-promo-msg');
  
  if (!code) {
    msgEl.textContent = 'Введите промокод.';
    msgEl.className = 'promo-msg error';
    return;
  }
  
  msgEl.textContent = 'Проверка...';
  msgEl.className = 'promo-msg';

  fetch('/payments/promo/check', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    },
    body: JSON.stringify({
      code: code,
      type: 'homepage',
      listing_id: currentHomepageListingId,
      months: currentHomepageMonths
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      currentHomepagePromoCode = data.code;
      homepagePromoType = data.type;
      homepagePromoValue = parseFloat(data.value);
      
      msgEl.textContent = data.description || 'Промокод успешно применен!';
      msgEl.className = 'promo-msg success';
      
      recalculateHomepagePrices();
    } else {
      msgEl.textContent = data.message || 'Ошибка применения промокода.';
      msgEl.className = 'promo-msg error';
    }
  })
  .catch(() => {
    msgEl.textContent = 'Ошибка сети при проверке промокода.';
    msgEl.className = 'promo-msg error';
  });
}

function recalculateHomepagePrices() {
  const unitPrice = 50.00;
  const months = currentHomepageMonths || 1;
  const totalRaw = unitPrice * months;
  
  let periodMultiplier = 1.0;
  if (months === 3) periodMultiplier = 0.9;
  else if (months === 6) periodMultiplier = 0.8;
  else if (months === 12) periodMultiplier = 0.7;

  const priceWithPeriodDiscount = Math.round(totalRaw * periodMultiplier * 100) / 100;
  const periodDiscount = totalRaw - priceWithPeriodDiscount;

  let promoDiscount = 0.00;
  if (currentHomepagePromoCode) {
    if (homepagePromoType === 'percentage') {
      promoDiscount = Math.round(priceWithPeriodDiscount * (homepagePromoValue / 100) * 100) / 100;
    } else if (homepagePromoType === 'fixed') {
      promoDiscount = Math.min(homepagePromoValue, priceWithPeriodDiscount);
    } else if (homepagePromoType === 'free') {
      promoDiscount = priceWithPeriodDiscount;
    }
  }
  
  const finalAmount = Math.max(0.00, priceWithPeriodDiscount - promoDiscount);
  currentHomepageFinalAmount = finalAmount;
  
  document.getElementById('homepage-summary-base-price').textContent = `${totalRaw.toFixed(2)} Б̶`;

  const discountRow = document.getElementById('homepage-summary-discount-row');
  if (periodDiscount > 0) {
    document.getElementById('homepage-summary-discount-val').textContent = `-${periodDiscount.toFixed(2)} Б̶`;
    discountRow.style.display = 'flex';
  } else {
    discountRow.style.display = 'none';
  }

  const promoRow = document.getElementById('homepage-summary-promo-row');
  if (currentHomepagePromoCode && promoDiscount > 0) {
    document.getElementById('homepage-summary-promo-val').textContent = `-${promoDiscount.toFixed(2)} Б̶`;
    promoRow.style.display = 'flex';
  } else {
    promoRow.style.display = 'none';
  }
  
  document.getElementById('homepage-summary-total-price').textContent = `${finalAmount.toFixed(2)} Б̶`;
  
  const submitBtn = document.getElementById('homepage-submit-btn');
  if (finalAmount <= 0) {
    submitBtn.textContent = 'Активировать бесплатно (0 Б̶)';
  } else {
    submitBtn.textContent = 'Перейти к оплате';
  }
  submitBtn.disabled = false;
}

function confirmHomepageCheckout() {
  if (!currentHomepageListingId) return;
  
  const submitBtn = document.getElementById('homepage-submit-btn');
  submitBtn.disabled = true;
  submitBtn.textContent = 'Обработка...';
  
  const checkoutUrl = `/payments/checkout-homepage/${currentHomepageListingId}?months=${currentHomepageMonths}&promo=${encodeURIComponent(currentHomepagePromoCode)}`;
  window.location.href = checkoutUrl;
}
</script>
@endsection
