@extends('layouts.app')

@section('title', 'Подать объявление — Flatly.by')

@section('head')
<style>
/* PROGRESS */
.progress-wrap { background: var(--white); border-bottom: 1px solid var(--border); padding: 0 40px; }
.progress-steps { display: flex; gap: 0; max-width: 720px; margin: 0 auto; }
.pstep { flex: 1; display: flex; flex-direction: column; align-items: center; padding: 14px 8px; position: relative; cursor: pointer; }
.pstep:not(:last-child)::after { content: ''; position: absolute; top: 22px; right: -1px; width: calc(100% - 32px); left: calc(50% + 16px); height: 2px; background: var(--border); }
.pstep.done:not(:last-child)::after { background: var(--amber); }
.pstep-circle { width: 28px; height: 28px; border-radius: 50%; border: 2px solid var(--border); background: var(--white); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: var(--gray-text); position: relative; z-index: 1; transition: all .2s; }
.pstep.active .pstep-circle { border-color: var(--amber); background: var(--amber); color: var(--slate-900); }
.pstep.done .pstep-circle { border-color: var(--amber); background: var(--amber); color: var(--slate-900); }
.pstep-label { font-size: 11px; font-weight: 500; color: var(--gray-text); margin-top: 5px; white-space: nowrap; }
.pstep.active .pstep-label { color: var(--slate-900); font-weight: 600; }
.pstep.done .pstep-label { color: var(--slate-900); }

/* LAYOUT */
.layout { display: grid; grid-template-columns: 1fr 300px; gap: 28px; max-width: 1100px; margin: 0 auto; padding: 36px 40px 60px; align-items: start; }
#form-wrap { min-width: 0; }
.sidebar { display: flex; flex-direction: column; gap: 16px; position: sticky; top: 96px; }

/* FORM CARD */
.form-card { background: var(--white); border-radius: 14px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
.fc-header { padding: 24px 28px 20px; border-bottom: 1px solid var(--border); }
.fc-title { font-size: 20px; font-weight: 700; color: var(--slate-900); }
.fc-sub { font-size: 13px; color: var(--gray-text); margin-top: 4px; }
.fc-body { padding: 28px; }
.fc-section { margin-bottom: 32px; }
.fc-section:last-child { margin-bottom: 0; }
.section-label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .8px; color: var(--gray-text); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.section-label svg { width: 15px; height: 15px; stroke: var(--gray-text); fill: none; stroke-width: 2; }

/* FIELD */
.field { margin-bottom: 18px; }
.field:last-child { margin-bottom: 0; }
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-grid-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.checkbox-grid-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.label { font-size: 13px; font-weight: 600; color: var(--slate-900); margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
.label span { color: var(--gray-text); font-weight: 400; }
.badge-req { font-size: 10px; background: var(--amber-pale); color: var(--amber-dark); padding: 1px 6px; border-radius: 10px; font-weight: 600; }

input[type=text], input[type=tel], input[type=number], input[type=email], input[type=time], select, textarea {
  width: 100%; background: var(--white); border: 1px solid var(--border); border-radius: 10px;
  padding: 11px 14px; font-family: 'Inter', sans-serif; font-size: 14px; color: var(--slate-900);
  outline: none; transition: border-color .15s;
  -webkit-appearance: none; appearance: none;
}
input:focus, select:focus, textarea:focus { border-color: var(--amber); }
input::placeholder, textarea::placeholder { color: #B0B0C0; }

select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236B6B80' stroke-width='2.5' stroke-linecap='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; padding-right: 36px; }
textarea { resize: vertical; min-height: 100px; line-height: 1.6; }
.hint { font-size: 12px; color: var(--gray-text); margin-top: 5px; }
/* TYPE TILES */
.type-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  margin: 24px 0;
  max-width: 560px;
}
@media (max-width: 768px) {
  .type-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
  }
}
.type-tile {
  border: 1.5px solid var(--border);
  border-radius: 16px;
  padding: 32px 20px;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  gap: 16px;
  background: var(--white);
  transition: all .2s ease;
  min-height: 180px;
}
.type-tile:hover {
  border-color: var(--amber);
  background: var(--amber-pale);
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(245, 166, 35, 0.08);
}
.type-tile.sel {
  border-color: var(--amber);
  background: var(--amber-pale);
  box-shadow: 0 6px 16px rgba(245, 166, 35, 0.12);
}
.type-tile.sel svg {
  color: var(--amber-dark);
}
.type-tile svg {
  color: var(--gray-text);
  transition: color .2s;
}
.tt-name {
  font-size: 16px;
  font-weight: 700;
  color: var(--slate-900);
}
.tt-sub {
  font-size: 12px;
  color: var(--gray-text);
  margin-top: 4px;
  line-height: 1.4;
}

/* PRICE GRID */
.price-input-wrap { display: flex; align-items: center; border: 1px solid var(--border); border-radius: 10px; overflow: hidden; background: var(--white); }
.price-input-wrap:focus-within { border-color: var(--amber); }
.price-cur { padding: 0 10px; font-size: 13px; font-weight: 600; color: var(--gray-text); background: var(--slate-50); border-right: 1px solid var(--border); height: 40px; display: flex; align-items: center; }
.price-input-wrap input { border: none; border-radius: 0; padding: 0 10px; height: 40px; flex: 1; }
.price-input-wrap input:focus { border: none; }

/* PHOTO UPLOAD */
.upload-zone { border: 2px dashed var(--border); border-radius: 10px; padding: 32px 20px; text-align: center; cursor: pointer; display: block; transition: border-color .15s, background .15s; }
.upload-zone:hover { border-color: var(--amber); background: var(--amber-pale); }
.upload-zone svg { width: 32px; height: 32px; stroke: var(--gray-text); fill: none; stroke-width: 1.5; margin: 0 auto 10px; }
.upload-zone p { font-size: 14px; font-weight: 600; color: var(--slate-900); margin-bottom: 4px; }
.upload-zone span { font-size: 12px; color: var(--gray-text); }
.upload-zone input { display: none; }
.photo-previews { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 12px; }
.photo-thumb {
  aspect-ratio: 1; border-radius: 10px; overflow: hidden; position: relative; background: var(--slate-50);
  cursor: grab; transition: transform .15s, box-shadow .15s, border-color .15s; border: 2px solid transparent; user-select: none;
}
.photo-thumb:active { cursor: grabbing; }
.photo-thumb.dragging { opacity: 0.35; transform: scale(0.95); }
.photo-thumb.drag-over { border-color: var(--amber); transform: scale(1.04); box-shadow: 0 6px 16px rgba(245, 166, 35, 0.25); }
.photo-thumb.is-cover { border-color: var(--amber); box-shadow: 0 4px 12px rgba(245, 166, 35, 0.18); }
.photo-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; pointer-events: none; }
.photo-cover-badge {
  position: absolute; top: 6px; left: 6px;
  background: var(--amber); color: var(--slate-900);
  font-size: 10px; font-weight: 700;
  padding: 3px 8px; border-radius: 6px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.25);
  pointer-events: none;
  z-index: 2;
  display: flex; align-items: center; gap: 3px;
}
.photo-make-cover-btn {
  position: absolute; bottom: 6px; left: 6px; right: 6px;
  background: rgba(15, 23, 42, 0.8); color: #fff;
  font-size: 10.5px; font-weight: 600; text-align: center;
  padding: 5px 6px; border-radius: 6px;
  cursor: pointer; opacity: 0; transition: opacity .15s, background .15s, color .15s;
  backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px);
  z-index: 2;
}
.photo-thumb:hover .photo-make-cover-btn { opacity: 1; }
.photo-make-cover-btn:hover { background: rgba(15, 23, 42, 0.95); color: var(--amber); }
.photo-thumb-add { border: 2px dashed var(--border); border-radius: 10px; aspect-ratio: 1; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: border-color .15s, background .15s; }
.photo-thumb-add:hover { border-color: var(--amber); background: var(--amber-pale); }
.photo-thumb-add svg { width: 22px; height: 22px; stroke: var(--gray-text); fill: none; stroke-width: 2; }
.photo-remove { position: absolute; top: 6px; right: 6px; width: 22px; height: 22px; background: rgba(0,0,0,.65); border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 3; transition: background .15s, transform .15s; }
.photo-remove:hover { background: #e53e3e; transform: scale(1.1); }
.photo-remove svg { width: 11px; height: 11px; stroke: #fff; fill: none; stroke-width: 2.5; }

.photo-quality-tip {
  margin-top: 12px;
  padding: 10px 14px;
  background: #FFF9E6;
  border: 1px solid #F5D061;
  border-radius: 8px;
  font-size: 12px;
  color: #7A4B00;
  display: flex;
  align-items: center;
  gap: 8px;
  line-height: 1.45;
}
.photo-quality-tip svg {
  width: 16px; height: 16px; flex-shrink: 0; stroke: #D48806; fill: none; stroke-width: 2;
}

/* ERROR HIGHLIGHT & INLINE VALIDATION */
.field-error-input {
  border-color: #E53E3E !important;
  background-color: #FFF5F5 !important;
  box-shadow: 0 0 0 1px #E53E3E !important;
}
.field-error-text {
  font-size: 11.5px;
  color: #E53E3E;
  margin-top: 5px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 4px;
}

/* SIDEBAR CARDS */
.sidebar-card { background: var(--white); border-radius: 14px; border: 1px solid var(--border); padding: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
.sc-title { font-size: 14px; font-weight: 600; color: var(--slate-900); margin-bottom: 12px; display: flex; align-items: center; gap: 7px; }
.sc-title svg { width: 16px; height: 16px; stroke: var(--amber-dark); fill: none; stroke-width: 2; }
.sc-tip { font-size: 13px; color: var(--gray-text); line-height: 1.65; margin-bottom: 8px; }
.sc-tip:last-child { margin-bottom: 0; }
.preview-mini { border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
.pm-photo { height: 120px; background: linear-gradient(140deg,#2C3E50,#3D5A6B); display: flex; align-items: center; justify-content: center; font-size: 40px; }
.pm-body { padding: 12px; }
.pm-title { font-size: 14px; font-weight: 600; color: var(--slate-900); margin-bottom: 2px; }
.pm-sub { font-size: 12px; color: var(--gray-text); }
.pm-price { font-size: 15px; font-weight: 700; color: var(--slate-900); margin-top: 8px; }
.pm-price span { font-size: 12px; font-weight: 400; color: var(--gray-text); }
.pm-phone { display: inline-flex; align-items: center; gap: 5px; background: var(--amber); padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; color: var(--slate-900); margin-top: 8px; }
.pm-phone svg { width: 12px; height: 12px; stroke: var(--slate-900); fill: none; stroke-width: 2.5; }

/* SUBMIT */
.submit-area { background: var(--white); border-top: 1px solid var(--border); padding: 16px 28px; display: flex; align-items: center; justify-content: space-between; gap: 16px; border-radius: 0 0 14px 14px; }
.submit-note { font-size: 12px; color: var(--gray-text); line-height: 1.6; flex: 1; }
.btn-draft { border: 1.5px solid var(--border); background: var(--white); padding: 11px 20px; border-radius: 22px; font-size: 14px; font-weight: 600; color: var(--slate-900); transition: background .15s; white-space: nowrap; }
.btn-draft:hover { background: var(--slate-50); }
.btn-submit { background: var(--amber); border: none; padding: 11px 24px; border-radius: 22px; font-size: 14px; font-weight: 700; color: var(--slate-900); display: flex; align-items: center; gap: 8px; transition: background .15s; white-space: nowrap; }
.btn-submit:hover { background: var(--amber-dark); color: var(--white); }
.btn-submit svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2.5; }
@keyframes spin {
  to { transform: rotate(360deg); }
}
.spinner {
  display: inline-block;
  width: 14px;
  height: 14px;
  border: 2px solid currentColor;
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

/* SUCCESS */
.success-overlay { display: none; position: fixed; inset: 0; background: rgba(10,10,20,.55); z-index: 5000; align-items: center; justify-content: center; }
.success-overlay.show { display: flex; }
.success-card { background: var(--white); border-radius: 18px; padding: 40px 36px; max-width: 420px; width: 90%; text-align: center; }
.success-icon { width: 64px; height: 64px; border-radius: 50%; background: #EAF3DE; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
.success-icon svg { width: 32px; height: 32px; stroke: #3B6D11; fill: none; stroke-width: 2.5; }
.success-card h2 { font-size: 22px; font-weight: 700; color: var(--slate-900); margin-bottom: 10px; }
.success-card p { font-size: 14px; color: var(--gray-text); line-height: 1.7; margin-bottom: 24px; }
.success-btn { background: var(--amber); border: none; padding: 13px 28px; border-radius: 10px; font-size: 15px; font-weight: 700; color: var(--slate-900); width: 100%; transition: background .15s; }
.success-btn:hover { background: var(--amber-dark); color: var(--white); }

.char-count { font-size: 11px; color: var(--gray-text); text-align: right; margin-top: 4px; }
.char-count.warn { color: var(--amber-dark); }
.req { color: var(--amber-dark); }

/* ===== RESPONSIVE MEDIA QUERIES ===== */
@media (max-width: 991px) {
  .layout {
    grid-template-columns: 1fr;
    gap: 24px;
    padding: 20px 16px 60px;
  }
  .sidebar {
    position: static;
    order: 2; /* Сайдбар уходит вниз */
  }
}

@media (max-width: 768px) {
  .progress-wrap {
    padding: 0 16px;
  }
  .pstep-label {
    display: none; /* скрываем подписи шагов на мобильном */
  }
  .pstep:not(:last-child)::after {
    width: calc(100% - 20px);
    left: calc(50% + 10px);
  }
  .field-row {
    grid-template-columns: 1fr;
    gap: 12px;
  }
  .form-grid-2col {
    grid-template-columns: 1fr;
    gap: 12px;
  }
  .photo-previews {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 576px) {
  .fc-header {
    padding: 20px 16px;
  }
  .fc-body {
    padding: 20px 16px;
  }
  .submit-area {
    padding: 16px;
    flex-direction: column-reverse;
    align-items: stretch;
    gap: 12px;
  }
  .btn-draft, .btn-submit {
    justify-content: center;
    width: 100%;
  }
  .photo-previews {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (max-width: 480px) {
  .checkbox-grid-2col {
    grid-template-columns: 1fr;
  }
}

#listing-map ymaps img {
  filter: grayscale(85%) brightness(104%) contrast(95%) sepia(3%);
}
</style>
@endsection

@section('content')
<!-- PROGRESS -->
<div class="progress-wrap">
  <div class="progress-steps" id="steps">
    <!-- Rendered dynamically by JS -->
  </div>
</div>

<!-- LAYOUT -->
<div class="layout">

  <!-- FORM -->
  <div id="form-wrap">

    <!-- STEP 1: Выбор объекта -->
    <div class="form-card" id="step_type">
      <div class="fc-header">
        <div class="fc-title">Выбор типа объекта</div>
        <div class="fc-sub">Выберите категорию жилья для размещения</div>
      </div>
      <div class="fc-body">
        <div class="fc-section">
          <div class="section-label">
            <x-solar-home-2-linear width="18" height="18" />
            Категория недвижимости
          </div>
          <div class="type-grid">
            <div class="type-tile sel" data-type="apartment" onclick="selectObjectType('apartment')">
              <x-solar-buildings-2-linear width="48" height="48" />
              <div>
                <div class="tt-name">Квартира</div>
                <div class="tt-sub">Посуточная аренда квартир</div>
              </div>
            </div>

            
            <div class="type-tile" data-type="cottage" onclick="selectObjectType('cottage')">
              <x-solar-home-2-linear width="48" height="48" />
              <div>
                <div class="tt-name">Коттедж</div>
                <div class="tt-sub">Дома, коттеджи, таунхаусы</div>
              </div>
            </div>

          </div>

        </div>
      </div>
      <div class="submit-area">
        <p class="submit-note">Шаг 1 из 7 — Выбор типа объекта</p>
        <button class="btn-submit" onclick="nextStep()">
          Далее — основное
          <x-solar-alt-arrow-right-linear width="16" height="16" />
        </button>
      </div>
    </div>

    <!-- STEP 2: Основное -->
    <div class="form-card" id="step_main" style="display:none;">
      <div class="fc-header">
        <div class="fc-title">Основное</div>
        <div class="fc-sub">* — поля обязательные для заполнения</div>
      </div>
      <div class="fc-body">

        <!-- Заголовок -->
        <div class="fc-section">
          <div class="section-label">
            <x-solar-document-text-linear width="18" height="18" />
            Заголовок объявления <span class="req">*</span>
          </div>
          <div class="field">
            <input type="text" id="title-input" maxlength="60" placeholder="Например: Уютная квартира в центре" oninput="countChars('title-input','title-count',60)">
            <div class="char-count" id="title-count">0 / 60</div>
          </div>
        </div>
        <!-- Блок адреса для квартир -->
        <div id="apartment-address-section">
          <!-- Город -->
          <div class="fc-section">
            <div class="section-label">
              <x-solar-map-point-linear width="18" height="18" />
              Укажите город <span class="req">*</span>
            </div>
            <div class="field">
              <select id="apartment-city-select">
                <option selected>Минск</option><option>Брест</option><option>Гродно</option>
                <option>Гомель</option><option>Витебск</option><option>Могилёв</option>
                <option>Бобруйск</option><option>Барановичи</option><option>Другой</option>
              </select>
            </div>
            <div class="field" id="apartment-city-custom-wrap" style="display: none; margin-top: 12px;">
              <label class="label">Название вашего города <span class="req">*</span></label>
              <input type="text" id="apartment-city-custom-input" placeholder="Введите название города">
            </div>
            <div class="field" id="apartment-district-wrap" style="margin-top: 12px;">
              <label class="label">Район города</label>
              <select id="apartment-district-select">
                <option value="" selected>Выберите район (необязательно)</option>
              </select>
            </div>
          </div>

          <!-- Адрес -->
          <div class="fc-section">
            <div class="section-label">
              <x-solar-home-2-linear width="18" height="18" />
              Адрес <span class="req">*</span>
            </div>
            <div class="field">
              <label class="label">Тип улицы (пр., ул., пер. и др.)</label>
              <input type="text" placeholder="Тип" id="street-type-input">
            </div>
            <div class="field">
              <label class="label">Название улицы <span class="req">*</span></label>
              <input type="text" placeholder="Улица" id="street-name-input">
            </div>
            <div class="field-row">
              <div class="field">
                <label class="label">Номер дома <span class="req">*</span></label>
                <input type="text" placeholder="Дом" id="house-number-input">
              </div>
              <div class="field">
                <label class="label">Корпус</label>
                <input type="text" placeholder="Корпус" id="house-corpus-input">
              </div>
            </div>
            <div class="field" id="metro-field">
              <label class="label">Станция метро</label>
              <select id="metro-select">
                <option value="" selected>Выберите станцию</option>
                <optgroup label="Московская линия (1-я)">
                  <option>Уручье</option>
                  <option>Борисовский тракт</option>
                  <option>Восток</option>
                  <option>Московская</option>
                  <option>Парк Челюскинцев</option>
                  <option>Академия наук</option>
                  <option>Площадь Якуба Коласа</option>
                  <option>Площадь Победы</option>
                  <option>Октябрьская</option>
                  <option>Площадь Ленина</option>
                  <option>Институт культуры</option>
                  <option>Грушевка</option>
                  <option>Михалово</option>
                  <option>Петровщина</option>
                  <option>Малиновка</option>
                </optgroup>
                <optgroup label="Автозаводская линия (2-я)">
                  <option>Каменная Горка</option>
                  <option>Кунцевщина</option>
                  <option>Спортивная</option>
                  <option>Пушкинская</option>
                  <option>Молодёжная</option>
                  <option>Фрунзенская</option>
                  <option>Немига</option>
                  <option>Купаловская</option>
                  <option>Первомайская</option>
                  <option>Пролетарская</option>
                  <option>Тракторный завод</option>
                  <option>Партизанская</option>
                  <option>Автозаводская</option>
                  <option>Могилевская</option>
                </optgroup>
                <optgroup label="Зеленолужская линия (3-я)">
                  <option>Юбилейная площадь</option>
                  <option>Площадь Франтишка Богушевича</option>
                  <option>Вокзальная</option>
                  <option>Ковальская Слобода</option>
                  <option>Аэродромная</option>
                  <option>Неморшанский Сад</option>
                  <option>Слуцкий Гостинец</option>
                </optgroup>
              </select>
            </div>
            <div class="field" style="margin-top: 16px;">
              <label class="label">Особенности расположения (быстрый поиск и категории)</label>
              <div class="checkbox-grid-2col" id="location-features-container">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;">
                  <input type="checkbox" id="loc-center" name="location_feature" value="В центре"> В центре города
                </label>
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;">
                  <input type="checkbox" id="loc-station" name="location_feature" value="Рядом с вокзалом"> Рядом с ж/д вокзалом
                </label>
              </div>
            </div>
            <div class="hint">Точный адрес показывается гостю после того, как он с вами свяжется</div>
          </div>
        </div>

        <!-- Блок адреса для коттеджей и усадеб -->
        <div id="cottage-address-section" style="display:none;">
          <div class="fc-section">
            <div class="section-label">
              <x-solar-home-2-linear width="18" height="18" />
              Адрес <span class="req">*</span>
            </div>
            
            <div class="field">
              <label class="label">Область <span class="req">*</span></label>
              <select id="region-select" onchange="updateDistricts()">
                <option value="" selected>Выберите область</option>
                <option value="Минская область">Минская область</option>
                <option value="Брестская область">Брестская область</option>
                <option value="Витебская область">Витебская область</option>
                <option value="Гомельская область">Гомельская область</option>
                <option value="Гродненская область">Гродненская область</option>
                <option value="Могилевская область">Могилевская область</option>
              </select>
            </div>

            <div class="field">
              <label class="label">Район</label>
              <select id="district-select">
                <option value="" selected>Выберите район</option>
              </select>
            </div>

            <div class="field">
              <label class="label">Детальный адрес (нас. пункт, улица, дом) <span class="req">*</span></label>
              <input type="text" id="detailed-address-input" placeholder="Населённый пункт, улица, дом">
            </div>
            
            <div class="hint">Точный адрес показывается гостю после того, как он с вами свяжется</div>
          </div>
        </div>

        <!-- Карта -->
        <div class="fc-section">
          <div class="section-label">
            <x-solar-map-linear width="18" height="18" />
            Карта <span class="req">*</span>
          </div>
          <div class="hint" style="margin-bottom:10px;">Это расположение гости увидят на сайте. Пожалуйста перетащите маркер в точное место</div>
          <div style="border-radius:10px;overflow:hidden;border:1px solid var(--border);height:220px;position:relative;">
            <div id="listing-map" style="width:100%;height:100%;"></div>
            <div id="map-placeholder" style="position:absolute;top:0;left:0;width:100%;height:100%;background:#e8e0d8;display:flex;align-items:center;justify-content:center;text-align:center;color:var(--gray-text);z-index:5;">
              <div>
                <x-solar-map-linear width="32" height="32" style="display:block;margin:0 auto 8px;" />
                <span style="font-size:13px;">Карта загрузится после ввода адреса</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Параметры объекта / квартиры -->
        <div class="fc-section">
          <div class="section-label" id="params-section-title">
            <x-solar-tuning-2-linear width="18" height="18" />
            Параметры квартиры <span class="req">*</span>
          </div>
          
          <div class="field">
            <label class="label" id="area-label">Общая площадь <span class="req">*</span></label>
            <div class="price-input-wrap">
              <input type="number" placeholder="Площадь" min="1" id="area-input">
              <div class="price-cur" style="border-left:1px solid var(--border);border-right:none;color:var(--amber-dark);font-weight:700;">м.кв.</div>
            </div>
          </div>

          <!-- Площадь участка (только для коттеджей/усадеб) -->
          <div class="field" id="plot-area-field" style="display:none;">
            <label class="label">Площадь участка <span class="req">*</span></label>
            <div class="price-input-wrap">
              <input type="number" placeholder="Площадь участка" min="1" id="plot-area-input">
              <div class="price-cur" style="border-left:1px solid var(--border);border-right:none;color:var(--amber-dark);font-weight:700;">соток</div>
            </div>
          </div>

          <div class="field">
            <label class="label">Количество комнат <span class="req">*</span></label>
            <input type="number" placeholder="Комнат" min="0" id="rooms-input">
            <div id="studio-check-wrap" style="margin-top:8px;display:flex;align-items:center;gap:8px;">
              <input type="checkbox" id="studio-check" style="width:16px;height:16px;">
              <label for="studio-check" style="font-size:13px;color:var(--slate-900);cursor:pointer;">Студия</label>
            </div>
          </div>

          <!-- Этаж и этажность (квартиры) -->
          <div class="field-row" id="floor-row">
            <div class="field" id="floor-field">
              <label class="label">Этаж <span class="req">*</span></label>
              <input type="number" placeholder="Этаж" min="1" id="floor-input">
            </div>
            <div class="field" id="total-floors-field">
              <label class="label">Этажность (этажей в доме) <span class="req">*</span></label>
              <input type="number" placeholder="Этажей" min="1" id="total-floors-input">
            </div>
          </div>

          <!-- Этажность дома (коттедж) -->
          <div class="field" id="floors-in-house-field" style="display:none;">
            <label class="label">Количество этажей в доме <span class="req">*</span></label>
            <input type="number" placeholder="Количество этажей" min="1" id="floors-in-house-input">
          </div>

          <!-- Тип дома / стен (квартиры) -->
          <div class="field" id="building-material-field">
            <label class="label">Тип дома</label>
            <select id="building-material-select">
              <option value="" selected>Выберите тип дома</option>
              <option>Панельный</option><option>Кирпичный</option>
              <option>Монолитный</option><option>Блочный</option>
              <option>Деревянный</option><option>Каркасный</option>
            </select>
          </div>

          <!-- Тип стен (коттеджи) -->
          <div class="field" id="cottage-material-field" style="display:none;">
            <label class="label">Материал стен</label>
            <select id="cottage-material-select">
              <option value="" selected>Выберите материал</option>
              <option>Брус</option><option>Бревно</option>
              <option>Кирпич</option><option>Блоки</option>
              <option>Каркасный</option><option>Иное</option>
            </select>
          </div>

          <!-- Вместимость банкетного зала (коттедж) -->
          <div class="field" id="banquet-hall-field" style="display:none;">
            <label class="label">Вместимость банкетного зала (человек)</label>
            <input type="number" placeholder="Например: 25" min="0" id="banquet-hall-input">
          </div>

          <div class="field">
            <label class="label">Количество спальных мест (вмещаемость гостей) <span class="req">*</span></label>
            <select id="guests">
              <option value="" selected>Выберите количество</option>
              <option>1</option><option>2</option><option>3</option><option>4</option>
              <option>5</option><option>6</option><option>7</option><option>8</option>
              <option>9</option><option>10+</option>
            </select>
          </div>
        </div>

        <!-- Спальные места -->
        <div class="fc-section">
          <div class="section-label">
            <x-solar-bed-linear width="18" height="18" />
            Спальные места
          </div>
          <div class="field-row">
            <div class="field">
              <label class="label">Односпальная кровать</label>
              <input type="number" id="bed-single" value="0" min="0">
            </div>
            <div class="field">
              <label class="label">Полутороспальная кровать</label>
              <input type="number" id="bed-double-small" value="0" min="0">
            </div>
          </div>
          <div class="field-row">
            <div class="field">
              <label class="label">Двуспальная кровать</label>
              <input type="number" id="bed-double" value="0" min="0">
            </div>
            <div class="field">
              <label class="label">Двуспальная широкая (king-size)</label>
              <input type="number" id="bed-kingsize" value="0" min="0">
            </div>
          </div>
          <div class="field-row">
            <div class="field">
              <label class="label">Диван-кровать</label>
              <input type="number" id="bed-sofa" value="0" min="0">
            </div>
            <div class="field">
              <label class="label">Кресло-кровать</label>
              <input type="number" id="bed-armchair" value="0" min="0">
            </div>
          </div>
          <div class="field" style="margin-top:8px;">
            <label class="label">Всего спальных мест <span class="req">*</span></label>
            <input type="number" id="total-beds" placeholder="Количество" min="0">
          </div>
        </div>

        <!-- Описание -->
        <div class="fc-section">
          <div class="section-label" id="desc-section-title">
            <x-solar-document-text-linear width="18" height="18" />
            Описание квартиры
          </div>
          <div class="field">
            <textarea id="desc-input" maxlength="1200" rows="5" placeholder="Описание" oninput="countChars('desc-input','desc-count',1200)"></textarea>
            <div class="char-count" id="desc-count">0 / 1200</div>
          </div>
        </div>

        <!-- Фото -->
        <div class="fc-section">
          <div class="section-label">
            <x-solar-gallery-linear width="18" height="18" />
            Загрузка фотографий <span class="req">*</span>
          </div>
          <label class="upload-zone" for="photo-upload" id="upload-zone">
            <input type="file" id="photo-upload" multiple accept="image/*" onchange="handlePhotos(this)">
            <x-solar-cloud-upload-linear width="40" height="40" />
            <p>Перетащите фото или нажмите для выбора</p>
            <span>JPG, PNG, WEBP (не более 15 файлов, до 5 МБ каждый)</span>
          </label>
          <div class="photo-quality-tip">
            <x-solar-info-circle-linear width="16" height="16" />
            <span><b>Совет по фото:</b> Первое фото станет главной обложкой. Вы можете перетаскивать фото или нажать «Сделать обложкой». Объявления с 5+ качественными фото получают в 3 раза больше откликов.</span>
          </div>
          <div class="photo-previews" id="photo-previews">
            <div class="photo-thumb-add" onclick="document.getElementById('photo-upload').click()">
              <x-solar-add-circle-linear width="20" height="20" />
            </div>
          </div>
        </div>

      </div>
      <div class="submit-area">
        <button class="btn-draft" onclick="prevStep()">← Назад</button>
        <button class="btn-submit" onclick="nextStep()">
          Далее — характеристики
          <x-solar-alt-arrow-right-linear width="16" height="16" />
        </button>
      </div>
    </div>

    <!-- STEP 3: Характеристики -->
    <div class="form-card" id="step_chars" style="display:none;">
      <div class="fc-header">
        <div class="fc-title">Характеристики</div>
        <div class="fc-sub">* — поля обязательные для заполнения</div>
      </div>
      <div class="fc-body">

        <div class="form-grid-2col">

          <!-- Тип санузла -->
          <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;">
            <div class="section-label" style="margin-bottom:12px;">Тип санузла <span class="req">*</span></div>
            <div class="checkbox-grid-2col" id="bathroom-container">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;">
                <input type="radio" name="bathroom" value="combined" checked> Совмещённый
              </label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;">
                <input type="radio" name="bathroom" value="separate"> Раздельный
              </label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;">
                <input type="radio" name="bathroom" value="two"> 2 санузла
              </label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;">
                <input type="radio" name="bathroom" value="three_plus"> 3+ санузла
              </label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;display:none;" id="outside-bathroom-option">
                <input type="radio" name="bathroom" value="outside"> На улице
              </label>
            </div>
          </div>

          <!-- Балкон -->
          <div class="fc-section" id="apartment-balcony-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;">
            <div class="section-label" style="margin-bottom:12px;">Балкон</div>
            <div class="checkbox-grid-2col" id="balcony-container">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="balcony" value="none" checked> Нет</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="balcony" value="balcony"> Есть балкон</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="balcony" value="loggia"> Есть лоджия</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="balcony" value="terrace"> Есть терраса</label>
            </div>
          </div>

          <!-- На территории -->
          <div class="fc-section" id="apartment-territory-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;">
            <div class="section-label" style="margin-bottom:12px;">На территории</div>
            <div class="checkbox-grid-2col" id="territory-container">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="territory" value="Парковка"> Парковка</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="territory" value="Детская площадка"> Детская площадка</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="territory" value="Спортивная площадка"> Спортивная площадка</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="territory" value="Зарядка для электромобилей"> Зарядка для электромобилей</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="territory" value="Беседка"> Беседка</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="territory" value="Видеонаблюдение"> Видеонаблюдение</label>
            </div>
          </div>

          <!-- Удобства дома -->
          <div class="fc-section" id="apartment-house-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;">
            <div class="section-label" style="margin-bottom:12px;">Удобства дома</div>
            <div class="checkbox-grid-2col" id="house-container">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="house" value="Лифт"> Лифт</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="house" value="Пандус"> Пандус</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="house" value="Консьерж"> Консьерж</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="house" value="Колясочная"> Колясочная</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="house" value="Подъёмник для инвалидов"> Подъёмник для инвалидов</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="house" value="Домофон"> Домофон</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="house" value="Велопарковка"> Велопарковка</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="house" value="Парковка для инвалидов"> Парковка для инвалидов</label>
            </div>
          </div>

        </div>

        <!-- Коттеджные характеристики -->
        <div id="cottage-chars-section" style="display:none; margin-top:16px;">
          <!-- Парковка -->
          <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
            <div class="section-label" style="margin-bottom:12px;">Тип парковки</div>
            <div class="field">
              <select id="parking-type-select">
                <option value="" selected>Выберите тип парковки</option>
                <option value="private">Охраняемая на территории</option>
                <option value="free">Бесплатная на территории</option>
                <option value="street">Рядом с домом</option>
              </select>
            </div>
          </div>

          <!-- Окрестности -->
          <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
            <div class="section-label" style="margin-bottom:12px;">Окрестности</div>
            <div class="checkbox-grid-2col" id="neighborhood-container">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="neighborhood" value="Лес"> Лес</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="neighborhood" value="Озеро"> Озеро</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="neighborhood" value="Река"> Река</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="neighborhood" value="Водоём"> Водоём</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="neighborhood" value="Сад / Зеленая зона"> Сад / Зеленая зона</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="neighborhood" value="Горы / Холмы"> Горы / Холмы</label>
            </div>
          </div>

          <!-- Назначение -->
          <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
            <div class="section-label" style="margin-bottom:12px;">Назначение</div>
            <div class="checkbox-grid-2col" id="purpose-container">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="object_purpose" value="Семейный отдых"> Семейный отдых</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="object_purpose" value="Вечеринки / Праздники"> Вечеринки / Праздники</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="object_purpose" value="Корпоративы / Тимбилдинги"> Корпоративы / Тимбилдинги</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="object_purpose" value="Свадьбы / Банкеты"> Свадьбы / Банкеты</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="object_purpose" value="Фотосессии / Видеосъемка"> Фотосессии / Видеосъемка</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="object_purpose" value="Спокойный отдых"> Спокойный отдых</label>
            </div>
          </div>

          <!-- Видеонаблюдение -->
          <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
            <div class="section-label" style="margin-bottom:12px;">Видеонаблюдение</div>
            <div class="checkbox-grid-2col" id="video-surveillance-container">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="video_surveillance" value="На территории"> На территории</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="video_surveillance" value="В доме"> В доме</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="video_surveillance" value="В общих зонах"> В общих зонах</label>
            </div>
          </div>
        </div>

      </div>
      <div class="submit-area">
        <button class="btn-draft" onclick="prevStep()">← Назад</button>
        <button class="btn-submit" onclick="nextStep()">
          Далее — удобства
          <x-solar-alt-arrow-right-linear width="16" height="16" />
        </button>
      </div>
    </div>

    <!-- STEP 4: Удобства -->
    <div class="form-card" id="step_amenities" style="display:none;">
      <div class="fc-header">
        <div class="fc-title">Удобства</div>
        <div class="fc-sub">* — поля обязательные для заполнения</div>
      </div>
      <div class="fc-body">

        <!-- Apartment Amenities Wrapper -->
        <div id="apartment-amenities">
          <!-- Основные удобства -->
          <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
            <div class="section-label" style="margin-bottom:14px;">Основные удобства</div>
            <div class="checkbox-grid-2col">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_basic" value="Интернет Wi-Fi"> Интернет Wi-Fi</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_basic" value="Кондиционер"> Кондиционер</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_basic" value="Телевизор / Smart TV"> Телевизор / Smart TV</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_basic" value="Стиральная машина"> Стиральная машина</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_basic" value="Гардеробная"> Гардеробная</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_basic" value="Гладильная доска"> Гладильная доска</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_basic" value="Детский стул"> Детский стул</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_basic" value="Детская кроватка"> Детская кроватка</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_basic" value="Джакузи / Гидромассаж"> Джакузи / Гидромассаж</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_basic" value="Сушилка для белья"> Сушилка для белья</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_basic" value="Утюг"> Утюг</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_basic" value="Шкаф для одежды"> Шкаф для одежды</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_basic" value="Шторы блэкаут"> Шторы блэкаут</label>
            </div>
          </div>

          <!-- Кухонная зона -->
          <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
            <div class="section-label" style="margin-bottom:14px;">Кухонная зона</div>
            <div class="checkbox-grid-2col">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_kitchen" value="Индукционная плита"> Индукционная плита</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_kitchen" value="Газовая плита"> Газовая плита</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_kitchen" value="Микроволновка"> Микроволновка</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_kitchen" value="Холодильник"> Холодильник</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_kitchen" value="Электрочайник"> Электрочайник</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_kitchen" value="Посудомоечная машина"> Посудомоечная машина</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="amenity_kitchen" value="Посуда и столовые приборы"> Посуда и столовые приборы</label>
            </div>
          </div>
        </div>

        <!-- Cottage Amenities Wrapper -->
        <div id="cottage-amenities" style="display:none;">
          <!-- Услуги на территории -->
          <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
            <div class="section-label" style="margin-bottom:14px;">Услуги на территории</div>
            <div class="checkbox-grid-2col">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_territory" value="Беседка"> Беседка</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_territory" value="Мангал / Зона барбекю"> Мангал / Зона барбекю</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_territory" value="Детская площадка"> Детская площадка</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_territory" value="Русская баня"> Русская баня</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_territory" value="Финская сауна"> Финская сауна</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_territory" value="Бассейн"> Бассейн</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_territory" value="Купель"> Купель</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_territory" value="Терраса"> Терраса</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_territory" value="Качели"> Качели</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_territory" value="Гамак"> Гамак</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_territory" value="Зона для костра"> Зона для костра</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_territory" value="Шезлонги"> Шезлонги</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_territory" value="Джакузи / Гидромассаж"> Джакузи / Гидромассаж</label>
            </div>
          </div>

          <!-- Климат и коммуникации -->
          <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
            <div class="section-label" style="margin-bottom:14px;">Климат и коммуникации</div>
            <div class="checkbox-grid-2col">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_climate" value="Интернет Wi-Fi"> Интернет Wi-Fi</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_climate" value="Кондиционер"> Кондиционер</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_climate" value="Камин"> Камин</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_climate" value="Отопление"> Отопление</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_climate" value="Подогрев полов"> Подогрев полов</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_climate" value="Телевизор / Smart TV"> Телевизор / Smart TV</label>
            </div>
          </div>

          <!-- Досуг и развлечения внутри -->
          <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
            <div class="section-label" style="margin-bottom:14px;">Досуг и развлечения внутри</div>
            <div class="checkbox-grid-2col">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_leisure" value="Настольный теннис"> Настольный теннис</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_leisure" value="Бильярд"> Бильярд</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_leisure" value="Настольные игры"> Настольные игры</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_leisure" value="Караоке"> Караоке</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_leisure" value="Детские игрушки / Книги"> Детские игрушки / Книги</label>
            </div>
          </div>

          <!-- Сервис и услуги -->
          <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
            <div class="section-label" style="margin-bottom:14px;">Сервис и услуги</div>
            <div class="checkbox-grid-2col">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_service" value="Постельное белье"> Постельное белье</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_service" value="Полотенца"> Полотенца</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_service" value="Средства личной гигиены"> Средства личной гигиены</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_service" value="Кухонная посуда"> Кухонная посуда</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_service" value="Мангальный набор (шампуры, решетка)"> Мангальный набор (шампуры, решетка)</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_service" value="Детская кроватка"> Детская кроватка</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_service" value="Услуги повара / Банкетное меню"> Услуги повара / Банкетное меню</label>
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="cottage_amenity_service" value="Трансфер"> Трансфер</label>
            </div>
          </div>
        </div>

      </div>
      <div class="submit-area">
        <button class="btn-draft" onclick="prevStep()">← Назад</button>
        <button class="btn-submit" onclick="nextStep()">
          Далее
          <x-solar-alt-arrow-right-linear width="16" height="16" />
        </button>
      </div>
    </div>

    <!-- STEP 5: Развлечения (только для Коттеджей и Усадеб) -->
    <div class="form-card" id="step_entertainment" style="display:none;">
      <div class="fc-header">
        <div class="fc-title">Развлечения</div>
        <div class="fc-sub">Выберите доступные развлечения для гостей загородной недвижимости</div>
      </div>
      <div class="fc-body">
        <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;">
          <div class="section-label" style="margin-bottom:14px;">Развлечения на природе и активный отдых</div>
          <div class="checkbox-grid-2col">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="entertainment" value="Катание на телеге / Санях"> Катание на телеге / Санях</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="entertainment" value="Прокат квадроциклов"> Прокат квадроциклов</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="entertainment" value="Прокат велосипедов"> Прокат велосипедов</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="entertainment" value="Рыбалка"> Рыбалка</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="entertainment" value="Катание на лодке / Катамаране"> Катание на лодке / Катамаране</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="entertainment" value="Экскурсии / Прогулки с гидом"> Экскурсии / Прогулки с гидом</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="entertainment" value="Конные прогулки"> Конные прогулки</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="entertainment" value="Стрельба из лука / Пневматики"> Стрельба из лука / Пневматики</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="entertainment" value="Лазертаг / Пейнтбол"> Лазертаг / Пейнтбол</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="entertainment" value="Спортивный инвентарь (мячи, бадминтон)"> Спортивный инвентарь (мячи, бадминтон)</label>
          </div>
        </div>
      </div>
      <div class="submit-area">
        <button class="btn-draft" onclick="prevStep()">← Назад</button>
        <button class="btn-submit" onclick="nextStep()">
          Далее — условия
          <x-solar-alt-arrow-right-linear width="16" height="16" />
        </button>
      </div>
    </div>

    <!-- STEP 6: Условия -->
    <div class="form-card" id="step_rules" style="display:none;">
      <div class="fc-header">
        <div class="fc-title">Условия</div>
        <div class="fc-sub">* — поля обязательные для заполнения</div>
      </div>
      <div class="fc-body">

        <!-- Время заселения -->
        <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
          <div class="section-label" style="margin-bottom:14px;">Время заселения <span class="req">*</span></div>
          <div class="field-row">
            <div class="field">
              <label class="label">Расчётное время заселения <span class="req">*</span></label>
              <input type="time" placeholder="--:--" value="14:00" id="checkin-time">
            </div>
            <div class="field">
              <label class="label">Расчётное время выселения <span class="req">*</span></label>
              <input type="time" placeholder="--:--" value="12:00" id="checkout-time">
            </div>
          </div>
        </div>

        <!-- Правила размещения -->
        <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
          <div class="section-label" style="margin-bottom:14px;">Правила размещения</div>
          <div class="checkbox-grid-2col">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" id="children-allowed" checked> Можно с детьми</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" id="animals-allowed"> С животными разрешено</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" id="events-allowed"> Мероприятия разрешены</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" id="smoking-allowed"> Разрешено курить</label>
          </div>
        </div>

        <!-- Условия заселения -->
        <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
          <div class="section-label" style="margin-bottom:14px;">Условия заселения <span class="req">*</span></div>
          <div class="checkbox-grid-2col" id="checking-rules-container">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="checking_rule" value="Бесконтактное заселение"> Бесконтактное заселение</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="checking_rule" value="Круглосуточное заселение"> Круглосуточное заселение</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="checking_rule" value="Отчётные документы" checked> Отчётные документы</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="checking_rule" value="Предоплата обязательна"> Предоплата обязательна</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="checking_rule" value="Залог обязателен"> Залог обязателен</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="checking_rule" value="Нужен документ личности"> Нужен документ личности</label>
          </div>
        </div>

      </div>
      <div class="submit-area">
        <button class="btn-draft" onclick="prevStep()">← Назад</button>
        <button class="btn-submit" onclick="nextStep()">
          Далее — оплата
          <x-solar-alt-arrow-right-linear width="16" height="16" />
        </button>
      </div>
    </div>

    <!-- STEP 7: Оплата -->
    <div class="form-card" id="step_payment" style="display:none;">
      <div class="fc-header">
        <div class="fc-title">Оплата</div>
        <div class="fc-sub">* — поля обязательные для заполнения</div>
      </div>
      <div class="fc-body">

        <div class="fc-section">
          <div class="section-label">
            <x-solar-tag-price-linear width="18" height="18" />
            Цена за сутки <span class="req">*</span>
          </div>
          <div class="price-input-wrap">
            <input type="number" placeholder="Укажите цену" min="1" id="price-weekday">
            <div class="price-cur" style="border-left:1px solid var(--border);border-right:none;color:var(--amber-dark);font-weight:700;">Б̶</div>
          </div>
        </div>

        <div class="fc-section" style="margin-top:16px;">
          <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
            <input type="checkbox" id="rent-by-hours" style="width:16px;height:16px;" onchange="toggleHoursInput()">
            <label for="rent-by-hours" style="font-size:13px;font-weight:600;color:var(--slate-900);cursor:pointer;">Сдается на часы</label>
          </div>
          <div class="price-input-wrap" id="hours-price-container" style="display:none;">
            <input type="number" placeholder="Укажите цену за час" min="1" id="price-hourly-input">
            <div class="price-cur" style="border-left:1px solid var(--border);border-right:none;color:var(--amber-dark);font-weight:700;">Б̶</div>
          </div>
        </div>

        <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;">
          <div class="section-label" style="margin-bottom:14px;">Способы оплаты <span class="req">*</span></div>
          <div style="display:flex;flex-direction:column;gap:10px;">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="payment_method" value="Наличными" checked> Наличными</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="payment_method" value="Банковской картой"> Банковской картой</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="payment_method" value="ЕРИП"> ЕРИП</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="payment_method" value="Безналичный расчет (юр. лица)"> Безналичный расчет (юр. лица)</label>
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="payment_method" value="Иной"> Иной</label>
          </div>
        </div>

      </div>
      <div class="submit-area">
        <button class="btn-draft" onclick="prevStep()">← Назад</button>
        <button class="btn-submit" onclick="nextStep()">
          Далее — контакты
          <x-solar-alt-arrow-right-linear width="16" height="16" />
        </button>
      </div>
    </div>

    <!-- STEP 8: Контакты -->
    <div class="form-card" id="step_contacts" style="display:none;">
      <div class="fc-header">
        <div class="fc-title">Контакты</div>
        <div class="fc-sub">* — поля обязательные для заполнения</div>
      </div>
      <div class="fc-body">

        <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
          <div class="section-label" style="margin-bottom:14px;">Контакты <span class="req">*</span></div>
          <div class="field">
            <label class="label">Имя арендодателя <span class="req">*</span></label>
            <input type="text" placeholder="Имя" id="host-name-input">
          </div>
          <div class="field">
            <label class="label">Номер телефона <span class="req">*</span></label>
            <div style="display:flex;gap:8px;">
              <div style="background:var(--slate-50);border:1px solid var(--border);border-radius:10px;padding:11px 14px;font-size:14px;font-weight:600;white-space:nowrap;">+375</div>
              <input type="tel" placeholder="(XX) XXX-XX-XX" id="phone-input" style="flex:1;">
            </div>
          </div>
          <div class="field">
            <label class="label">УНП (для ИП/Юрлиц/Самозанятых)</label>
            <input type="text" placeholder="9 символов (например, AB1234567)" id="unp-input" value="{{ $user->unp ?? '' }}" maxlength="9">
          </div>
        </div>

        <div class="fc-section" style="background:var(--white);border:1px solid var(--border);border-radius:10px;padding:18px;margin-bottom:16px;">
          <div class="section-label" style="margin-bottom:6px;">Мессенджеры</div>
          <div class="hint" style="margin-bottom:14px;">Укажите контактную информацию</div>
          <div class="field">
            <label class="label">Telegram</label>
            <input type="text" placeholder="@username" id="telegram-input">
          </div>
          <div class="field">
            <label class="label">WhatsApp</label>
            <input type="text" placeholder="Ссылка или номер" id="whatsapp-input">
          </div>
          <div class="field">
            <label class="label">Viber</label>
            <input type="text" placeholder="Ссылка или номер" id="viber-input">
          </div>
        </div>

        <div class="fc-section">
          <div style="display:flex;align-items:flex-start;gap:10px;">
            <input type="checkbox" id="agree" style="width:16px;height:16px;margin-top:2px;flex-shrink:0;">
            <label for="agree" style="font-size:13px;color:var(--slate-900);line-height:1.6;cursor:pointer;">
              Я согласен с правилами размещения и подтверждаю, что информация в объявлении достоверна
            </label>
          </div>
        </div>

      </div>
      <div class="submit-area">
        <div>
          <button class="btn-draft" onclick="prevStep()">← Назад</button>
        </div>
        <button class="btn-submit" onclick="submitListing()" style="font-size:16px;padding:14px 32px;">
          <x-solar-check-circle-linear width="18" height="18" />
          Опубликовать
        </button>
      </div>
    </div>

  </div><!-- /form-wrap -->

  <!-- SIDEBAR -->
  <aside class="sidebar">

    <div class="sidebar-card">
      <div class="sc-title">
        <x-solar-question-circle-linear width="18" height="18" />
        Советы
      </div>
      <p class="sc-tip" id="sidebar-tip">Выберите правильную категорию — гости ищут «квартиру» и «коттедж» по-разному.</p>
    </div>

    <div class="sidebar-card">
      <div class="sc-title">
        <x-solar-gallery-linear width="18" height="18" />
        Предпросмотр
      </div>
      <div class="preview-mini">
        <div class="pm-photo">🏢</div>
        <div class="pm-body">
          <div class="pm-title" id="preview-title">Заголовок объявления</div>
          <div class="pm-sub">Минск · Частный арендодатель</div>
          <div class="pm-price"><span id="preview-price">65</span> Б̶ <span>/ ночь</span></div>
          <div class="pm-phone">
            <x-solar-phone-calling-linear width="16" height="16" />
            <span id="preview-phone">+375 XX XXX-XX-XX</span>
          </div>
        </div>
      </div>
    </div>

    <div class="sidebar-card">
      <div class="sc-title">
        <x-solar-check-circle-linear width="18" height="18" />
        Бесплатно
      </div>
      <p class="sc-tip">Размещение объявления — <strong>бесплатно</strong>. Гости звонят вам напрямую.</p>
    </div>

  </aside>

</div><!-- /layout -->

<!-- SUCCESS MODAL -->
<div class="success-overlay" id="success-overlay" onclick="closeSuccess(event)">
  <div class="success-card">
    <div class="success-icon">
      <x-solar-check-circle-linear width="48" height="48" />
    </div>
    <h2 id="success-title">Объявление создано!</h2>
    <p id="success-text">Ваше объявление успешно создано! Чтобы оно появилось в поиске, перейдите в личный кабинет и оплатите тариф.</p>
    <button class="success-btn" onclick="window.location.href='/dashboard'">Перейти в личный кабинет →</button>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://api-maps.yandex.ru/2.1/?apikey={{ env('YANDEX_MAPS_API_KEY') }}&lang=ru_RU" type="text/javascript"></script>
<script>
// Server-loaded database values
const serverListing = @json($listing ?? null);
const serverUser = @json($user ?? null);

let currentStepIndex = 0; // index in the stepSequence
let isEditMode = false;
let editListingId = null;
let uploadedPhotos = []; // Keep track of uploaded photo Data URLs
let selectedObjectType = 'apartment'; // default selection

// Yandex Maps variables
let myMap;
let myPlacemark;
let currentCoords = [53.902, 27.555]; // default Minsk center

// Dynamic steps sequence logic
function getStepSequence() {
  if (selectedObjectType === 'cottage' || selectedObjectType === 'agrotourism') {
    return ['step_type', 'step_main', 'step_chars', 'step_amenities', 'step_entertainment', 'step_rules', 'step_payment', 'step_contacts'];
  } else {
    return ['step_type', 'step_main', 'step_chars', 'step_amenities', 'step_rules', 'step_payment', 'step_contacts'];
  }
}

const stepLabels = {
  'step_type': 'Объект',
  'step_main': 'Основное',
  'step_chars': 'Характеристики',
  'step_amenities': 'Удобства',
  'step_entertainment': 'Развлечения',
  'step_rules': 'Условия',
  'step_payment': 'Оплата',
  'step_contacts': 'Контакты'
};

const tips = {
  'step_type': 'Выберите правильную категорию объекта — это определит шаги заполнения и фильтры на сайте.',
  'step_main': 'Заполните адрес максимально точно — это поможет гостям найти вас быстро.',
  'step_chars': 'Укажите все характеристики — это фильтры, по которым гости ищут жильё.',
  'step_amenities': 'Чем больше удобств указано, тем выше позиция в поиске.',
  'step_entertainment': 'Развлечения — ключевой фактор выбора усадьбы или коттеджа для отдыха.',
  'step_rules': 'Чёткие условия заселения снижают количество лишних вопросов от гостей.',
  'step_payment': 'Укажите способы оплаты — это удобнее для гостей.',
  'step_contacts': 'Укажите Viber или Telegram — большинство гостей в Беларуси пишут туда, а не звонят.'
};

document.addEventListener("DOMContentLoaded", function() {
  // Initialize phone mask
  setupPhoneMask();

  // Initialize apartment districts selector
  updateApartmentDistricts();
  const aptCitySelect = document.getElementById('apartment-city-select');
  if (aptCitySelect) {
    aptCitySelect.addEventListener('change', updateApartmentDistricts);
  }

  // Check if we are in Edit Mode
  const urlParams = new URLSearchParams(window.location.search);
  const editId = urlParams.get('edit');
  
  if (editId) {
    isEditMode = true;
    editListingId = parseInt(editId);
    prepareEditMode(editListingId);
  } else {
    // If not editing, we can still pre-fill profile defaults into host info fields!
    if (serverUser) {
      document.getElementById('host-name-input').value = serverUser.name || '';
      const formattedPhone = formatBelarusPhone(serverUser.phone || '');
      document.getElementById('phone-input').value = formattedPhone;
      document.getElementById('preview-phone').textContent = formattedPhone ? `+375 ${formattedPhone}` : '+375 XX XXX-XX-XX';
      document.getElementById('telegram-input').value = serverUser.telegram || '';
      document.getElementById('whatsapp-input').value = serverUser.whatsapp || '';
      document.getElementById('viber-input').value = serverUser.viber || '';
    }
    // Initialize default progress bar
    selectObjectType('apartment');
  }

  // Set up listeners for live preview
  setupPreviewSync();
  
  // Set up map geocoding listeners
  setupMapListeners();
});

// Select category handler
function selectObjectType(type) {
  selectedObjectType = type;
  
  // Update tile active styles
  document.querySelectorAll('#step_type .type-tile').forEach(tile => {
    if (tile.getAttribute('data-type') === type) {
      tile.classList.add('sel');
    } else {
      tile.classList.remove('sel');
    }
  });

  adjustFormFieldsForType(type);
  
  // Redraw progress bar
  renderProgressBar(currentStepIndex, getStepSequence());

  // Sync the preview card icon/emoji
  const previewMini = document.querySelector('.pm-photo');
  if (previewMini && uploadedPhotos.length === 0) {
    if (type === 'apartment') previewMini.textContent = '🏢';
    else if (type === 'apartments') previewMini.textContent = '🔑';
    else if (type === 'cottage') previewMini.textContent = '🏡';
    else if (type === 'agrotourism') previewMini.textContent = '🌳';
  }
}

function adjustFormFieldsForType(type) {
  const isCottage = (type === 'cottage' || type === 'agrotourism');
  
  // Update section title label
  const paramsTitle = document.getElementById('params-section-title');
  if (paramsTitle) {
    paramsTitle.innerHTML = isCottage ? 'Параметры объекта <span class="req">*</span>' : 'Параметры квартиры <span class="req">*</span>';
  }

  // Update area label
  const areaLabel = document.getElementById('area-label');
  if (areaLabel) {
    areaLabel.innerHTML = isCottage ? 'Общая площадь дома <span class="req">*</span>' : 'Общая площадь <span class="req">*</span>';
  }
  // Show/Hide address sections
  document.getElementById('apartment-address-section').style.display = isCottage ? 'none' : 'block';
  document.getElementById('cottage-address-section').style.display = isCottage ? 'block' : 'none';

  // Show/Hide fields
  document.getElementById('plot-area-field').style.display = isCottage ? 'block' : 'none';
  document.getElementById('studio-check-wrap').style.display = isCottage ? 'none' : 'flex';
  document.getElementById('floor-row').style.display = isCottage ? 'none' : 'grid';
  document.getElementById('floors-in-house-field').style.display = isCottage ? 'block' : 'none';
  document.getElementById('building-material-field').style.display = isCottage ? 'none' : 'block';
  document.getElementById('cottage-material-field').style.display = isCottage ? 'block' : 'none';
  document.getElementById('banquet-hall-field').style.display = isCottage ? 'block' : 'none';

  // Step 3 Characteristics
  document.getElementById('cottage-chars-section').style.display = isCottage ? 'block' : 'none';
  const outsideBathroom = document.getElementById('outside-bathroom-option');
  if (outsideBathroom) {
    outsideBathroom.style.display = isCottage ? 'flex' : 'none';
  }
  
  // Hide apartment characteristics for cottages
  document.getElementById('apartment-balcony-section').style.display = isCottage ? 'none' : 'block';
  document.getElementById('apartment-territory-section').style.display = isCottage ? 'none' : 'block';
  document.getElementById('apartment-house-section').style.display = isCottage ? 'none' : 'block';

  // Step 4 Amenities
  document.getElementById('apartment-amenities').style.display = isCottage ? 'none' : 'block';
  document.getElementById('cottage-amenities').style.display = isCottage ? 'block' : 'none';

  // Dynamic description title and placeholder
  const descTitle = document.getElementById('desc-section-title');
  if (descTitle) {
    descTitle.innerHTML = isCottage 
      ? '<svg width="18" height="18" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5" d="M3 10c0-3.771 0-5.657 1.172-6.828C5.343 2 7.229 2 11 2h2c3.771 0 5.657 0 6.828 1.172C21 4.343 21 6.229 21 10v4c0 3.771 0 5.657-1.172 6.828C18.657 22 16.771 22 13 22h-2c-3.771 0-5.657 0-6.828-1.172C3 19.657 3 17.771 3 14zm5-2h8m-8 4h8m-8 4h5"/></svg> Описание объекта'
      : '<svg width="18" height="18" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5" d="M3 10c0-3.771 0-5.657 1.172-6.828C5.343 2 7.229 2 11 2h2c3.771 0 5.657 0 6.828 1.172C21 4.343 21 6.229 21 10v4c0 3.771 0 5.657-1.172 6.828C18.657 22 16.771 22 13 22h-2c-3.771 0-5.657 0-6.828-1.172C3 19.657 3 17.771 3 14zm5-2h8m-8 4h8m-8 4h5"/></svg> Описание квартиры';
  }

  const descInput = document.getElementById('desc-input');
  if (descInput) {
    descInput.placeholder = isCottage 
      ? 'Опишите ваш дом, усадьбу, территорию, условия отдыха...'
      : 'Опишите вашу квартиру, ремонт, инфраструктуру рядом...';
  }
}

const districtsMap = {
  'Минская область': ['Минский', 'Борисовский', 'Солигорский', 'Молодечненский', 'Слуцкий', 'Дзержинский', 'Вилейский', 'Логойский', 'Мядельский', 'Пуховичский', 'Смолевичский', 'Столбцовский'],
  'Брестская область': ['Брестский', 'Барановичский', 'Пинский', 'Кобринский', 'Березовский', 'Лунинецкий', 'Ивацевичский', 'Пружанский'],
  'Витебская область': ['Витебский', 'Оршанский', 'Полоцкий', 'Глубокский', 'Лепельский', 'Браславский', 'Докшицкий', 'Поставский'],
  'Гомельская область': ['Гомельский', 'Мозырский', 'Жлобинский', 'Речицкий', 'Светлогорский', 'Рогачевский'],
  'Гродненская область': ['Гродненский', 'Лидский', 'Слонимский', 'Волковысский', 'Сморгонский', 'Островецкий', 'Новогрудский'],
  'Могилевская область': ['Могилевский', 'Бобруйский', 'Горецкий', 'Осиповичский', 'Кричевский', 'Климовичский']
};

function updateDistricts() {
  const region = document.getElementById('region-select').value;
  const districtSelect = document.getElementById('district-select');
  districtSelect.innerHTML = '<option value="" selected>Выберите район</option>';
  
  if (region && districtsMap[region]) {
    districtsMap[region].forEach(d => {
      const opt = document.createElement('option');
      opt.value = d;
      opt.textContent = d + ' район';
      districtSelect.appendChild(opt);
    });
  }
}

// City districts for apartments
const cityDistrictsMap = {
  'Минск': [
    'Центральный', 'Советский', 'Первомайский', 'Партизанский',
    'Заводской', 'Ленинский', 'Октябрьский', 'Московский', 'Фрунзенский'
  ],
  'Брест': ['Ленинский', 'Московский'],
  'Гродно': ['Ленинский', 'Октябрьский'],
  'Гомель': ['Центральный', 'Советский', 'Железнодорожный', 'Новобелицкий'],
  'Витебск': ['Железнодорожный', 'Октябрьский', 'Первомайский'],
  'Могилёв': ['Ленинский', 'Октябрьский'],
  'Бобруйск': ['Ленинский', 'Первомайский']
};

function updateApartmentDistricts() {
  const citySelect = document.getElementById('apartment-city-select');
  const districtSelect = document.getElementById('apartment-district-select');
  const districtWrap = document.getElementById('apartment-district-wrap');
  const metroField = document.getElementById('metro-field');

  if (!citySelect || !districtSelect) return;

  const city = citySelect.value;
  const districts = cityDistrictsMap[city] || [];

  // Metro station selector is only relevant for Minsk
  if (metroField) {
    if (city === 'Минск') {
      metroField.style.display = 'block';
    } else {
      metroField.style.display = 'none';
      const metroSelect = document.getElementById('metro-select');
      if (metroSelect) metroSelect.value = '';
    }
  }

  // Populate city districts
  const currentVal = districtSelect.value;
  districtSelect.innerHTML = '<option value="" selected>Выберите район (необязательно)</option>';

  if (districts.length > 0) {
    if (districtWrap) districtWrap.style.display = 'block';
    districts.forEach(d => {
      const opt = document.createElement('option');
      opt.value = d;
      opt.textContent = d + ' район';
      if (d === currentVal) opt.selected = true;
      districtSelect.appendChild(opt);
    });
  } else {
    // If other or small city without administrative division
    if (districtWrap) districtWrap.style.display = (city === 'Другой' ? 'none' : 'block');
  }
}

// Phone Mask Formatter for Belarus (+375)
function formatBelarusPhone(val) {
  if (!val) return '';
  let digits = String(val).replace(/\D/g, '');
  if (digits.startsWith('375')) {
    digits = digits.substring(3);
  }
  digits = digits.substring(0, 9);
  if (!digits) return '';
  if (digits.length <= 2) return `(${digits}`;
  if (digits.length <= 5) return `(${digits.slice(0, 2)}) ${digits.slice(2)}`;
  if (digits.length <= 7) return `(${digits.slice(0, 2)}) ${digits.slice(2, 5)}-${digits.slice(5)}`;
  return `(${digits.slice(0, 2)}) ${digits.slice(2, 5)}-${digits.slice(5, 7)}-${digits.slice(7, 9)}`;
}

function setupPhoneMask() {
  const phoneInput = document.getElementById('phone-input');
  if (!phoneInput) return;

  function handleMask() {
    const formatted = formatBelarusPhone(phoneInput.value);
    phoneInput.value = formatted;
    const previewPhone = document.getElementById('preview-phone');
    if (previewPhone) {
      previewPhone.textContent = formatted ? `+375 ${formatted}` : '+375 XX XXX-XX-XX';
    }
  }

  phoneInput.addEventListener('input', handleMask);
  phoneInput.addEventListener('paste', function() {
    setTimeout(handleMask, 0);
  });
}

// Inline Validation & Smooth Scroll Helpers
function markFieldError(el, msg) {
  if (!el) return;
  el.classList.add('field-error-input');

  const parent = el.closest('.field') || el.closest('.fc-section') || el.parentElement;
  if (parent) {
    let errEl = parent.querySelector('.field-error-text');
    if (!errEl) {
      errEl = document.createElement('div');
      errEl.className = 'field-error-text';
      const charCount = parent.querySelector('.char-count');
      if (charCount) {
        charCount.insertAdjacentElement('afterend', errEl);
      } else {
        el.insertAdjacentElement('afterend', errEl);
      }
    }
    errEl.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 7v6m0 4.5v-.5"/></g></svg> ${msg}`;
    errEl.style.display = 'flex';
  }

  const clearHandler = function() {
    clearFieldError(el);
    el.removeEventListener('input', clearHandler);
    el.removeEventListener('change', clearHandler);
  };
  el.addEventListener('input', clearHandler);
  el.addEventListener('change', clearHandler);
}

function clearFieldError(el) {
  if (!el) return;
  el.classList.remove('field-error-input');
  const parent = el.closest('.field') || el.closest('.fc-section') || el.parentElement;
  if (parent) {
    const errEl = parent.querySelector('.field-error-text');
    if (errEl) errEl.remove();
  }
}

function clearAllFieldErrors() {
  document.querySelectorAll('.field-error-input').forEach(el => el.classList.remove('field-error-input'));
  document.querySelectorAll('.field-error-text').forEach(el => el.remove());
}

function renderProgressBar(activeIdx, seq) {
  const stepsContainer = document.getElementById('steps');
  if (!stepsContainer) return;

  stepsContainer.innerHTML = '';
  seq.forEach((stepId, i) => {
    const isDone = i < activeIdx;
    const isActive = i === activeIdx;
    
    const pstep = document.createElement('div');
    pstep.className = 'pstep';
    if (isActive) pstep.classList.add('active');
    if (isDone) pstep.classList.add('done');
    
    if (isDone || isActive || isEditMode) {
      pstep.onclick = () => showStepByIndex(i);
      pstep.style.cursor = 'pointer';
    } else {
      pstep.style.cursor = 'default';
    }

    const circle = document.createElement('div');
    circle.className = 'pstep-circle';
    circle.textContent = isDone ? '✓' : (i + 1);

    const label = document.createElement('div');
    label.className = 'pstep-label';
    label.textContent = stepLabels[stepId] || '';

    pstep.appendChild(circle);
    pstep.appendChild(label);
    stepsContainer.appendChild(pstep);
  });

  // Update submit area step counts
  const totalSteps = seq.length;
  document.querySelectorAll('.submit-area').forEach((el, index) => {
    const note = el.querySelector('.submit-note');
    if (note) {
      note.innerHTML = `Шаг ${activeIdx + 1} из ${totalSteps} — ${stepLabels[seq[activeIdx]]}`;
    }
  });
}

function showStepByIndex(idx) {
  const seq = getStepSequence();
  if (idx < 0 || idx >= seq.length) return;

  // Hide current step
  const currentId = seq[currentStepIndex];
  const currentEl = document.getElementById(currentId);
  if (currentEl) currentEl.style.display = 'none';

  // Update current step index
  currentStepIndex = idx;

  // Show new step
  const newId = seq[idx];
  const newEl = document.getElementById(newId);
  if (newEl) {
    newEl.style.display = 'block';
    newEl.scrollIntoView({behavior:'smooth', block:'start'});
  }

  // Update progress bar
  renderProgressBar(idx, seq);

  // Update sidebar tip
  document.getElementById('sidebar-tip').textContent = tips[newId] || tips['step_type'];
}

// Sync input changes with Right Sidebar Preview card on the fly
function setupPreviewSync() {
  const titleInput = document.getElementById('title-input');
  if (titleInput) {
    titleInput.addEventListener('input', function() {
      document.getElementById('preview-title').textContent = this.value || 'Заголовок объявления';
    });
  }

  const priceInput = document.getElementById('price-weekday');
  if (priceInput) {
    priceInput.addEventListener('input', function() {
      document.getElementById('preview-price').textContent = this.value || '65';
    });
  }

  const phoneInput = document.getElementById('phone-input');
  if (phoneInput) {
    phoneInput.addEventListener('input', function() {
      document.getElementById('preview-phone').textContent = this.value ? `+375 ${this.value}` : '+375 XX XXX-XX-XX';
    });
  }

  const studioCheck = document.getElementById('studio-check');
  const roomsInput = document.getElementById('rooms-input');
  if (studioCheck && roomsInput) {
    studioCheck.addEventListener('change', function() {
      if (this.checked) {
        roomsInput.value = '';
        roomsInput.disabled = true;
        roomsInput.style.opacity = '0.5';
      } else {
        roomsInput.disabled = false;
        roomsInput.style.opacity = '1';
      }
    });
  }
}

function toggleHoursInput() {
  const container = document.getElementById('hours-price-container');
  const checked = document.getElementById('rent-by-hours').checked;
  container.style.display = checked ? 'flex' : 'none';
  if (!checked) {
    document.getElementById('price-hourly-input').value = '';
  }
}

// Prepare Form fields for Edit Mode
function prepareEditMode(id) {
  const listing = serverListing;
  if (!listing) return;

  // Set the selected type tile
  selectObjectType(listing.type || 'apartment');

  // Change Titles and actions in DOM
  document.querySelectorAll('.fc-title').forEach(el => el.textContent = 'Редактирование объявления');
  document.getElementById('success-title').textContent = 'Объявление обновлено!';
  document.getElementById('success-text').textContent = 'Ваши изменения успешно сохранены!';

  // Fill in known data
  document.getElementById('title-input').value = listing.title;
  document.getElementById('preview-title').textContent = listing.title;
  countChars('title-input', 'title-count', 60);

  document.getElementById('area-input').value = listing.area || '';
  document.getElementById('guests').value = listing.sleeping_places || '';
  document.getElementById('total-beds').value = listing.sleeping_places || '';

  const isCottage = (selectedObjectType === 'cottage' || selectedObjectType === 'agrotourism');
  if (isCottage) {
    document.getElementById('plot-area-input').value = listing.plot_area || '';
    document.getElementById('floors-in-house-input').value = listing.floors_in_house || '';
    document.getElementById('banquet-hall-input').value = listing.banquet_hall_capacity || '';
    document.getElementById('parking-type-select').value = listing.parking_type || '';
    document.getElementById('cottage-material-select').value = listing.cottage_material || '';

    // Restore video surveillance
    if (listing.video_surveillance && Array.isArray(listing.video_surveillance)) {
      listing.video_surveillance.forEach(val => {
        const cb = document.querySelector(`input[name="video_surveillance"][value="${val}"]`);
        if (cb) cb.checked = true;
      });
    }

    // Restore neighborhood
    if (listing.neighborhood && Array.isArray(listing.neighborhood)) {
      listing.neighborhood.forEach(val => {
        const cb = document.querySelector(`input[name="neighborhood"][value="${val}"]`);
        if (cb) cb.checked = true;
      });
    }

    // Restore object purpose
    if (listing.object_purpose && Array.isArray(listing.object_purpose)) {
      listing.object_purpose.forEach(val => {
        const cb = document.querySelector(`input[name="object_purpose"][value="${val}"]`);
        if (cb) cb.checked = true;
      });
    }

    // Restore cottage amenities
    if (listing.amenities && typeof listing.amenities === 'object') {
      const territoryVals = listing.amenities['Услуги на территории'] || [];
      territoryVals.forEach(val => {
        const cb = document.querySelector(`input[name="cottage_amenity_territory"][value="${val}"]`);
        if (cb) cb.checked = true;
      });
      const climateVals = listing.amenities['Климат и коммуникации'] || [];
      climateVals.forEach(val => {
        const cb = document.querySelector(`input[name="cottage_amenity_climate"][value="${val}"]`);
        if (cb) cb.checked = true;
      });
      const leisureVals = listing.amenities['Досуг и развлечения внутри'] || [];
      leisureVals.forEach(val => {
        const cb = document.querySelector(`input[name="cottage_amenity_leisure"][value="${val}"]`);
        if (cb) cb.checked = true;
      });
      const serviceVals = listing.amenities['Сервис и услуги'] || [];
      serviceVals.forEach(val => {
        const cb = document.querySelector(`input[name="cottage_amenity_service"][value="${val}"]`);
        if (cb) cb.checked = true;
      });
      const entertainmentVals = listing.amenities['Развлечения'] || [];
      entertainmentVals.forEach(val => {
        const cb = document.querySelector(`input[name="entertainment"][value="${val}"]`);
        if (cb) cb.checked = true;
      });
    }
  } else {
    // Apartment details
    document.getElementById('floor-input').value = listing.floor || '';
    document.getElementById('total-floors-input').value = listing.total_floors || '';
    document.getElementById('building-material-select').value = listing.building_material || '';

    if (listing.rooms === 0 || listing.apartment_type === 'studio') {
      document.getElementById('studio-check').checked = true;
      document.getElementById('rooms-input').value = '';
      document.getElementById('rooms-input').disabled = true;
      document.getElementById('rooms-input').style.opacity = '0.5';
    } else {
      document.getElementById('studio-check').checked = false;
      document.getElementById('rooms-input').value = listing.rooms || '';
      document.getElementById('rooms-input').disabled = false;
      document.getElementById('rooms-input').style.opacity = '1';
    }

    // Restore apartment amenities
    if (listing.amenities && typeof listing.amenities === 'object') {
      const basicVals = listing.amenities['Основные удобства'] || [];
      basicVals.forEach(val => {
        const cb = document.querySelector(`input[name="amenity_basic"][value="${val}"]`);
        if (cb) cb.checked = true;
      });
      const kitchenVals = listing.amenities['Кухонная зона'] || [];
      kitchenVals.forEach(val => {
        const cb = document.querySelector(`input[name="amenity_kitchen"][value="${val}"]`);
        if (cb) cb.checked = true;
      });
    }
  }

  // Restore bathroom radio
  let mappedBathroomValue = 'combined';
  if (listing.layout === 'Раздельный') mappedBathroomValue = 'separate';
  else if (listing.layout === '2 санузла' || listing.bathrooms_count === 2) mappedBathroomValue = 'two';
  else if (listing.layout === '3+ санузла' || listing.bathrooms_count >= 3) mappedBathroomValue = 'three_plus';
  else if (listing.layout === 'На улице') mappedBathroomValue = 'outside';
  
  const targetRadio = document.querySelector(`input[name="bathroom"][value="${mappedBathroomValue}"]`);
  if (targetRadio) targetRadio.checked = true;

  // Restore balcony checkboxes
  const balconyGlazed = listing.balcony_glazed;
  if (!listing.has_balcony) {
    const cb = document.querySelector('#balcony-container input[value="none"]');
    if (cb) cb.checked = true;
  } else {
    // Uncheck none
    const cbNone = document.querySelector('#balcony-container input[value="none"]');
    if (cbNone) cbNone.checked = false;

    const bType = listing.balcony_type || 'balcony';
    const cb = document.querySelector(`#balcony-container input[value="${bType}"]`);
    if (cb) cb.checked = true;
  }

  // Restore territory & house features
  if (listing.territory_features && Array.isArray(listing.territory_features)) {
    listing.territory_features.forEach(val => {
      const cb = document.querySelector(`input[name="territory"][value="${val}"], input[name="house"][value="${val}"]`);
      if (cb) cb.checked = true;
    });
  }

  // Restore beds list quantities
  if (listing.beds_list && Array.isArray(listing.beds_list)) {
    listing.beds_list.forEach(item => {
      let inputId = '';
      if (item.name === 'Односпальная кровать') inputId = 'bed-single';
      else if (item.name === 'Полутороспальная кровать') inputId = 'bed-double-small';
      else if (item.name === 'Двуспальная кровать') inputId = 'bed-double';
      else if (item.name === 'Двуспальная широкая (king-size)') inputId = 'bed-kingsize';
      else if (item.name === 'Диван-кровать') inputId = 'bed-sofa';
      else if (item.name === 'Кресло-кровать') inputId = 'bed-armchair';
      
      const el = document.getElementById(inputId);
      if (el) el.value = item.count;
    });
  }

  // Address parsing
  if (isCottage) {
    // Restore region
    const regionSelect = document.getElementById('region-select');
    if (regionSelect) {
      regionSelect.value = listing.city || '';
    }
    updateDistricts();
    
    // Restore district
    const districtSelect = document.getElementById('district-select');
    if (districtSelect) {
      districtSelect.value = listing.district || '';
    }
    
    // Restore detailed address
    let detailedAddress = listing.address || '';
    const regionStr = listing.city || '';
    const districtStr = listing.district ? listing.district + ' р-н' : '';
    
    // Clean up from the end
    if (regionStr && detailedAddress.endsWith(', ' + regionStr)) {
      detailedAddress = detailedAddress.slice(0, -(regionStr.length + 2));
    } else if (regionStr && detailedAddress.endsWith(regionStr)) {
      detailedAddress = detailedAddress.slice(0, -regionStr.length);
    }
    
    if (districtStr && detailedAddress.endsWith(', ' + districtStr)) {
      detailedAddress = detailedAddress.slice(0, -(districtStr.length + 2));
    } else if (districtStr && detailedAddress.endsWith(districtStr)) {
      detailedAddress = detailedAddress.slice(0, -districtStr.length);
    }
    
    const detailedInput = document.getElementById('detailed-address-input');
    if (detailedInput) {
      detailedInput.value = detailedAddress.trim();
    }
  } else {
    // Apartment details
    const citySelect = document.getElementById('apartment-city-select');
    const customCityInput = document.getElementById('apartment-city-custom-input');
    const customCityWrap = document.getElementById('apartment-city-custom-wrap');
    const streetNameInput = document.getElementById('street-name-input');
    const houseNumberInput = document.getElementById('house-number-input');
    const houseCorpusInput = document.getElementById('house-corpus-input');
    const streetTypeInput = document.getElementById('street-type-input');

    if (citySelect) {
      const cityVal = listing.city || 'Минск';
      let matchFound = false;
      for (var i = 0; i < citySelect.options.length; i++) {
        if (citySelect.options[i].value === cityVal) {
          citySelect.value = cityVal;
          matchFound = true;
          break;
        }
      }
      if (!matchFound && cityVal) {
        citySelect.value = 'Другой';
        if (customCityInput) customCityInput.value = cityVal;
        if (customCityWrap) customCityWrap.style.display = 'block';
      } else {
        if (customCityInput) customCityInput.value = '';
        if (customCityWrap) customCityWrap.style.display = 'none';
      }
    }

    const addrParts = (listing.address || '').split(', ');
    
    if (addrParts.length > 2) {
      if (citySelect) {
        const cityVal = addrParts[0] || listing.city || 'Минск';
        let matchFound = false;
        for (var i = 0; i < citySelect.options.length; i++) {
          if (citySelect.options[i].value === cityVal) {
            citySelect.value = cityVal;
            matchFound = true;
            break;
          }
        }
        if (!matchFound && cityVal) {
          citySelect.value = 'Другой';
          if (customCityInput) customCityInput.value = cityVal;
          if (customCityWrap) customCityWrap.style.display = 'block';
        } else {
          if (customCityInput) customCityInput.value = '';
          if (customCityWrap) customCityWrap.style.display = 'none';
        }
      }
      
      let streetVal = addrParts[1] || '';
      const types = ['пр-т', 'пр.', 'ул.', 'пер.', 'бул.', 'пл.', 'тр-т', 'ш.'];
      let foundType = '';
      for (const t of types) {
        if (streetVal.toLowerCase().startsWith(t + ' ')) {
          foundType = t;
          streetVal = streetVal.substring(t.length + 1);
          break;
        }
      }
      if (streetTypeInput) streetTypeInput.value = foundType;
      if (streetNameInput) streetNameInput.value = streetVal;

      let houseVal = addrParts[2] || '';
      let corpusVal = '';
      if (houseVal.includes('к')) {
        const parts = houseVal.split('к');
        houseVal = parts[0];
        corpusVal = parts[1];
      }
      if (houseNumberInput) houseNumberInput.value = houseVal;
      if (houseCorpusInput) houseCorpusInput.value = corpusVal;
    } else if (addrParts.length > 1) {
      let streetVal = addrParts[0] || '';
      const types = ['пр-т', 'пр.', 'ул.', 'пер.', 'бул.', 'пл.', 'тр-т', 'ш.'];
      let foundType = '';
      for (const t of types) {
        if (streetVal.toLowerCase().startsWith(t + ' ')) {
          foundType = t;
          streetVal = streetVal.substring(t.length + 1);
          break;
        }
      }
      if (streetTypeInput) streetTypeInput.value = foundType;
      if (streetNameInput) streetNameInput.value = streetVal;

      let houseVal = addrParts[1] || '';
      let corpusVal = '';
      if (houseVal.includes('к')) {
        const parts = houseVal.split('к');
        houseVal = parts[0];
        corpusVal = parts[1];
      }
      if (houseNumberInput) houseNumberInput.value = houseVal;
      if (houseCorpusInput) houseCorpusInput.value = corpusVal;
    } else {
      let streetVal = listing.address || '';
      const types = ['пр-т', 'пр.', 'ул.', 'пер.', 'бул.', 'пл.', 'тр-т', 'ш.'];
      let foundType = '';
      for (const t of types) {
        if (streetVal.toLowerCase().startsWith(t + ' ')) {
          foundType = t;
          streetVal = streetVal.substring(t.length + 1);
          break;
        }
      }
      if (streetTypeInput) streetTypeInput.value = foundType;
      if (streetNameInput) streetNameInput.value = streetVal;
    }

    // Restore apartment districts and metro
    updateApartmentDistricts();
    if (listing.district) {
      const aptDistrictSelect = document.getElementById('apartment-district-select');
      if (aptDistrictSelect) {
        aptDistrictSelect.value = listing.district;
      }
    }
    if (listing.nearest_metro) {
      const metroSelect = document.getElementById('metro-select');
      if (metroSelect) metroSelect.value = listing.nearest_metro;
    }

    // Restore location features (center, station)
    const descLower = (listing.description || '').toLowerCase();
    const addrLower = (listing.address || '').toLowerCase();
    if (descLower.includes('центр') || addrLower.includes('центр')) {
      const cbCenter = document.getElementById('loc-center');
      if (cbCenter) cbCenter.checked = true;
    }
    if (descLower.includes('вокзал') || addrLower.includes('вокзал')) {
      const cbStation = document.getElementById('loc-station');
      if (cbStation) cbStation.checked = true;
    }
  }

  document.getElementById('price-weekday').value = listing.price;
  document.getElementById('preview-price').textContent = listing.price;

  if (listing.weekend_price && parseInt(listing.weekend_price) > 0) {
    document.getElementById('rent-by-hours').checked = true;
    document.getElementById('price-hourly-input').value = listing.weekend_price;
    document.getElementById('hours-price-container').style.display = 'flex';
  } else {
    document.getElementById('rent-by-hours').checked = false;
    document.getElementById('price-hourly-input').value = '';
    document.getElementById('hours-price-container').style.display = 'none';
  }

  // Get current profile for defaults or use default fields
  const profile = serverUser || {};
  document.getElementById('host-name-input').value = listing.host_name || profile.name || '';
  const rawPhone = listing.phone || profile.phone || '';
  const formattedPhone = formatBelarusPhone(rawPhone);
  document.getElementById('phone-input').value = formattedPhone;
  document.getElementById('preview-phone').textContent = formattedPhone ? `+375 ${formattedPhone}` : '+375 XX XXX-XX-XX';
  document.getElementById('telegram-input').value = listing.telegram || profile.telegram || '';
  document.getElementById('whatsapp-input').value = listing.whatsapp || profile.whatsapp || '';
  document.getElementById('viber-input').value = listing.viber || profile.viber || '';

  // Restore photos
  if (listing.photos && Array.isArray(listing.photos) && listing.photos.length > 0) {
    uploadedPhotos = [...listing.photos];
  } else if (listing.image && listing.image.length > 4) {
    uploadedPhotos = [listing.image];
  }

  if (uploadedPhotos.length > 0) {
    renderPhotoThumbnails();
  }

  // Prefill UNP from profile or listing
  if (document.getElementById('unp-input')) {
    document.getElementById('unp-input').value = listing.unp || profile.unp || '';
  }

  // Get existing coords if available
  if (listing.latitude && listing.longitude) {
    const coords = [parseFloat(listing.latitude), parseFloat(listing.longitude)];
    currentCoords = coords;
    
    // Hide placeholder
    const placeholder = document.getElementById('map-placeholder');
    if (placeholder) placeholder.style.display = 'none';

    ymaps.ready(function() {
      initListingMap(coords);
    });
  } else {
    ymaps.ready(function() {
      geocodeAddress();
    });
  }

  // Restore description
  if (document.getElementById('desc-input')) {
    document.getElementById('desc-input').value = listing.description || '';
    countChars('desc-input', 'desc-count', 1200);
  }

  // Restore check-in/out times and rules
  if (listing.check_in_time && document.getElementById('checkin-time')) {
    document.getElementById('checkin-time').value = listing.check_in_time.substring(0, 5);
  }
  if (listing.check_out_time && document.getElementById('checkout-time')) {
    document.getElementById('checkout-time').value = listing.check_out_time.substring(0, 5);
  }
  if (document.getElementById('children-allowed')) {
    document.getElementById('children-allowed').checked = !!listing.children_allowed;
  }
  if (document.getElementById('animals-allowed')) {
    document.getElementById('animals-allowed').checked = !!listing.animals_allowed;
  }
  if (document.getElementById('events-allowed')) {
    document.getElementById('events-allowed').checked = !!listing.events_allowed;
  }

  // Restore checking rules and smoking status
  const comms = Array.isArray(listing.communications) ? listing.communications : [];
  if (document.getElementById('smoking-allowed')) {
    document.getElementById('smoking-allowed').checked = comms.includes('Разрешено курить');
  }

  document.querySelectorAll('input[name="checking_rule"]').forEach(cb => {
    cb.checked = comms.includes(cb.value);
  });

  // Restore payment methods
  let paymentVals = [];
  if (listing.renovation) {
    try {
      if (typeof listing.renovation === 'string') {
        paymentVals = JSON.parse(listing.renovation);
      } else if (Array.isArray(listing.renovation)) {
        paymentVals = listing.renovation;
      }
    } catch (e) {
      paymentVals = [listing.renovation];
    }
  }
  document.querySelectorAll('input[name="payment_method"]').forEach(cb => {
    cb.checked = paymentVals.includes(cb.value);
  });

  // Change final wizard button label
  const finalSubmitBtn = document.querySelector('#step_contacts .btn-submit');
  if (finalSubmitBtn) {
    finalSubmitBtn.innerHTML = `<svg width="18" height="18" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="1.5" d="m9 12l2 2l4-4m6 2a9 9 0 1 1-18 0a9 9 0 0 1 18 0"/></svg> Сохранить изменения`;
  }
}

function nextStep() {
  const seq = getStepSequence();
  const currentStepId = seq[currentStepIndex];

  clearAllFieldErrors();
  let firstErrorEl = null;

  function markError(el, msg) {
    if (!el) return;
    if (!firstErrorEl) firstErrorEl = el;
    markFieldError(el, msg);
  }

  // Validation
  if (currentStepId === 'step_type') {
    const selectedType = selectedObjectType;
    if (!selectedType) {
      showToast('⚠️ Пожалуйста, выберите тип объекта');
      return;
    }
  }

  if (currentStepId === 'step_main') {
    const titleInput = document.getElementById('title-input');
    const title = titleInput.value.trim();
    if (!title) {
      markError(titleInput, 'Укажите заголовок объявления');
    }

    const areaInput = document.getElementById('area-input');
    const area = areaInput.value.trim();
    if (!area || parseFloat(area) <= 0) {
      markError(areaInput, 'Укажите общую площадь');
    }

    const isCottage = (selectedObjectType === 'cottage' || selectedObjectType === 'agrotourism');
    if (isCottage) {
      const regionSelect = document.getElementById('region-select');
      if (!regionSelect.value) {
        markError(regionSelect, 'Выберите область');
      }

      const detailedAddressInput = document.getElementById('detailed-address-input');
      if (!detailedAddressInput.value.trim()) {
        markError(detailedAddressInput, 'Укажите детальный адрес');
      }

      const plotAreaInput = document.getElementById('plot-area-input');
      if (!plotAreaInput.value.trim() || parseFloat(plotAreaInput.value) <= 0) {
        markError(plotAreaInput, 'Укажите площадь участка');
      }

      const floorsInHouseInput = document.getElementById('floors-in-house-input');
      if (!floorsInHouseInput.value.trim() || parseInt(floorsInHouseInput.value) <= 0) {
        markError(floorsInHouseInput, 'Укажите этажность дома');
      }
    } else {
      const citySelect = document.getElementById('apartment-city-select');
      const customCityInput = document.getElementById('apartment-city-custom-input');
      if (citySelect && citySelect.value === 'Другой' && !customCityInput.value.trim()) {
        markError(customCityInput, 'Введите название вашего города');
      }

      const streetInput = document.getElementById('street-name-input');
      if (!streetInput.value.trim()) {
        markError(streetInput, 'Укажите название улицы');
      }

      const houseInput = document.getElementById('house-number-input');
      if (!houseInput.value.trim()) {
        markError(houseInput, 'Укажите номер дома');
      }

      const isStudio = document.getElementById('studio-check').checked;
      const roomsInput = document.getElementById('rooms-input');
      if (!isStudio && (!roomsInput.value.trim() || parseInt(roomsInput.value) <= 0)) {
        markError(roomsInput, 'Укажите количество комнат или выберите «Студия»');
      }

      const floorInput = document.getElementById('floor-input');
      if (!floorInput.value.trim() || parseInt(floorInput.value) <= 0) {
        markError(floorInput, 'Укажите этаж');
      }

      const totalFloorsInput = document.getElementById('total-floors-input');
      if (!totalFloorsInput.value.trim() || parseInt(totalFloorsInput.value) <= 0) {
        markError(totalFloorsInput, 'Укажите этажность дома');
      }
    }

    const guestsSelect = document.getElementById('guests');
    if (!guestsSelect.value) {
      markError(guestsSelect, 'Укажите количество гостей');
    }

    const totalBedsInput = document.getElementById('total-beds');
    if (!totalBedsInput.value.trim() || parseInt(totalBedsInput.value) <= 0) {
      markError(totalBedsInput, 'Укажите общее количество спальных мест');
    }

    if (uploadedPhotos.length === 0) {
      const uploadZone = document.getElementById('upload-zone');
      markError(uploadZone, 'Загрузите хотя бы одну фотографию объекта');
    }

    if (firstErrorEl) {
      firstErrorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
      if (typeof firstErrorEl.focus === 'function') firstErrorEl.focus();
      showToast('⚠️ Пожалуйста, заполните выделенные обязательные поля');
      return;
    }
  }

  if (currentStepId === 'step_chars') {
    const bathroomSelected = document.querySelector('input[name="bathroom"]:checked');
    if (!bathroomSelected) {
      const bathroomBox = document.getElementById('bathroom-container');
      markError(bathroomBox, 'Пожалуйста, выберите тип санузла');
      if (firstErrorEl) {
        firstErrorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        showToast('⚠️ Пожалуйста, выберите тип санузла');
        return;
      }
    }
  }

  if (currentStepId === 'step_rules') {
    const checkin = document.getElementById('checkin-time');
    const checkout = document.getElementById('checkout-time');
    if (!checkin.value) markError(checkin, 'Укажите время заселения');
    if (!checkout.value) markError(checkout, 'Укажите время выселения');
    if (firstErrorEl) {
      firstErrorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
      showToast('⚠️ Укажите время заселения и выселения');
      return;
    }
  }

  if (currentStepId === 'step_payment') {
    const priceInput = document.getElementById('price-weekday');
    const price = priceInput.value.trim();
    if (!price || parseInt(price) <= 0) {
      markError(priceInput, 'Укажите цену за сутки');
    }

    const rentByHours = document.getElementById('rent-by-hours').checked;
    if (rentByHours) {
      const hourlyInput = document.getElementById('price-hourly-input');
      const hourlyPrice = hourlyInput.value.trim();
      if (!hourlyPrice || parseInt(hourlyPrice) <= 0) {
        markError(hourlyInput, 'Укажите цену за час');
      }
    }

    const paymentMethods = document.querySelectorAll('input[name="payment_method"]:checked');
    if (paymentMethods.length === 0) {
      showToast('⚠️ Выберите хотя бы один способ оплаты');
      return;
    }

    if (firstErrorEl) {
      firstErrorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
      if (typeof firstErrorEl.focus === 'function') firstErrorEl.focus();
      showToast('⚠️ Укажите корректную стоимость');
      return;
    }
  }
  
  showStepByIndex(currentStepIndex + 1);
}

function prevStep() {
  showStepByIndex(currentStepIndex - 1);
}

function countChars(inputId, countId, max) {
  const len = document.getElementById(inputId).value.length;
  const el = document.getElementById(countId);
  el.textContent = len + ' / ' + max;
  el.classList.toggle('warn', len > max * 0.9);

  if (inputId === 'title-input') {
    const v = document.getElementById(inputId).value;
    document.getElementById('preview-title').textContent = v || 'Заголовок объявления';
  }
}

function compressImage(file, callback) {
  const reader = new FileReader();
  reader.readAsDataURL(file);
  reader.onload = event => {
    const img = new Image();
    img.src = event.target.result;
    img.onload = () => {
      const canvas = document.createElement('canvas');
      const MAX_WIDTH = 1200;
      const MAX_HEIGHT = 1200;
      let width = img.width;
      let height = img.height;

      if (width > height) {
        if (width > MAX_WIDTH) {
          height *= MAX_WIDTH / width;
          width = MAX_WIDTH;
        }
      } else {
        if (height > MAX_HEIGHT) {
          width *= MAX_HEIGHT / height;
          height = MAX_HEIGHT;
        }
      }

      canvas.width = width;
      canvas.height = height;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(img, 0, 0, width, height);

      // Compress to JPEG with 0.75 quality (highly optimized file size)
      const dataUrl = canvas.toDataURL('image/jpeg', 0.75);
      callback(dataUrl);
    };
  };
}

let draggedPhotoIndex = null;

function renderPhotoThumbnails() {
  const previews = document.getElementById('photo-previews');
  if (!previews) return;

  const addBtn = previews.querySelector('.photo-thumb-add');
  previews.querySelectorAll('.photo-thumb').forEach(el => el.remove());

  uploadedPhotos.forEach((photoPath, index) => {
    const div = document.createElement('div');
    div.className = 'photo-thumb' + (index === 0 ? ' is-cover' : '');
    div.setAttribute('draggable', 'true');
    div.dataset.index = index;

    const coverBadgeHtml = index === 0 ? '<div class="photo-cover-badge">★ Обложка</div>' : '';
    const makeCoverBtnHtml = index > 0 ? `<div class="photo-make-cover-btn" onclick="makePhotoCover(${index}, event)">Сделать обложкой</div>` : '';

    div.innerHTML = `
      <img src="${photoPath}">
      ${coverBadgeHtml}
      <div class="photo-remove" onclick="removePhotoByIndex(${index}, event)" title="Удалить фото">
        <svg width="16" height="16" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m18 6l-12 12M6 6l12 12"/></svg>
      </div>
      ${makeCoverBtnHtml}
    `;

    div.addEventListener('dragstart', handlePhotoDragStart);
    div.addEventListener('dragover', handlePhotoDragOver);
    div.addEventListener('dragleave', handlePhotoDragLeave);
    div.addEventListener('drop', handlePhotoDrop);
    div.addEventListener('dragend', handlePhotoDragEnd);

    previews.insertBefore(div, addBtn);
  });

  // Sync with Mini Preview sidebar
  const previewMini = document.querySelector('.pm-photo');
  if (previewMini) {
    if (uploadedPhotos.length > 0) {
      previewMini.innerHTML = `<img src="${uploadedPhotos[0]}" style="width:100%;height:100%;object-fit:cover;">`;
    } else {
      if (selectedObjectType === 'apartment') previewMini.textContent = '🏢';
      else if (selectedObjectType === 'apartments') previewMini.textContent = '🔑';
      else if (selectedObjectType === 'cottage') previewMini.textContent = '🏡';
      else if (selectedObjectType === 'agrotourism') previewMini.textContent = '🌳';
    }
  }

  // Clear photo error highlight if photos exist
  if (uploadedPhotos.length > 0) {
    const uploadZone = document.getElementById('upload-zone');
    if (uploadZone) clearFieldError(uploadZone);
  }
}

function handlePhotoDragStart(e) {
  draggedPhotoIndex = parseInt(this.dataset.index);
  this.classList.add('dragging');
  e.dataTransfer.effectAllowed = 'move';
  e.dataTransfer.setData('text/plain', draggedPhotoIndex);
}

function handlePhotoDragOver(e) {
  e.preventDefault();
  e.dataTransfer.dropEffect = 'move';
  this.classList.add('drag-over');
}

function handlePhotoDragLeave() {
  this.classList.remove('drag-over');
}

function handlePhotoDrop(e) {
  e.preventDefault();
  this.classList.remove('drag-over');
  const targetIndex = parseInt(this.dataset.index);
  if (draggedPhotoIndex !== null && draggedPhotoIndex !== targetIndex) {
    const moved = uploadedPhotos.splice(draggedPhotoIndex, 1)[0];
    uploadedPhotos.splice(targetIndex, 0, moved);
    renderPhotoThumbnails();
    showToast('Порядок фото обновлен');
  }
}

function handlePhotoDragEnd() {
  this.classList.remove('dragging');
  document.querySelectorAll('.photo-thumb').forEach(el => el.classList.remove('drag-over'));
  draggedPhotoIndex = null;
}

function makePhotoCover(index, event) {
  if (event) event.stopPropagation();
  if (index > 0 && index < uploadedPhotos.length) {
    const cover = uploadedPhotos.splice(index, 1)[0];
    uploadedPhotos.unshift(cover);
    renderPhotoThumbnails();
    showToast('★ Главная обложка установлена');
  }
}

function removePhotoByIndex(index, event) {
  if (event) event.stopPropagation();
  uploadedPhotos.splice(index, 1);
  renderPhotoThumbnails();
}

function handlePhotos(input) {
  const files = Array.from(input.files);
  if (!files.length) return;
  if (uploadedPhotos.length + files.length > 15) {
    showToast('⚠️ Максимально допустимо 15 фотографий');
    return;
  }
  let processed = 0;
  showToast('⚙️ Оптимизируем фото...');
  files.forEach(f => {
    compressImage(f, function(dataUrl) {
      uploadedPhotos.push(dataUrl);
      processed++;
      if (processed === files.length) {
        renderPhotoThumbnails();
        showToast('✓ Фото успешно добавлены!');
        input.value = '';
      }
    });
  });
}
function getFormDataObject() {
  const type = selectedObjectType;
  const isCottage = (type === 'cottage' || type === 'agrotourism');

  const title = document.getElementById('title-input').value.trim();
  
  let city, streetType, streetName, houseNumber, houseCorpus, metro, district, address;
  if (isCottage) {
    const region = document.getElementById('region-select').value;
    const districtVal = document.getElementById('district-select').value;
    const detailedAddress = document.getElementById('detailed-address-input').value.trim();
    
    city = region;
    district = districtVal;
    streetType = '';
    streetName = '';
    houseNumber = '';
    houseCorpus = '';
    metro = '';
    address = detailedAddress + (districtVal ? ', ' + districtVal + ' р-н' : '') + ', ' + region;
  } else {
    const citySelect = document.getElementById('apartment-city-select');
    if (citySelect && citySelect.value === 'Другой') {
      city = document.getElementById('apartment-city-custom-input').value.trim() || 'Другой';
    } else {
      city = citySelect ? citySelect.value : 'Минск';
    }
    streetType = document.getElementById('street-type-input').value.trim();
    streetName = document.getElementById('street-name-input').value.trim();
    houseNumber = document.getElementById('house-number-input').value.trim();
    houseCorpus = document.getElementById('house-corpus-input').value.trim();
    
    const metroSelect = document.getElementById('metro-select');
    metro = (city === 'Минск' && metroSelect) ? metroSelect.value : '';
    const districtSelect = document.getElementById('apartment-district-select');
    district = districtSelect ? districtSelect.value : '';
    address = '';
  }

  const area = document.getElementById('area-input').value.trim();
  const rooms = isCottage ? (document.getElementById('rooms-input').value.trim() || 1) : (document.getElementById('studio-check').checked ? 0 : (document.getElementById('rooms-input').value.trim() || 1));
  
  // Conditionally collect floor & building_material
  const floor = isCottage ? null : document.getElementById('floor-input').value.trim();
  const total_floors = isCottage ? null : document.getElementById('total-floors-input').value.trim();
  const building_material = isCottage ? null : document.getElementById('building-material-select').value;
  
  // Cottage parameters
  const plot_area = isCottage ? document.getElementById('plot-area-input').value.trim() : null;
  const floors_in_house = isCottage ? document.getElementById('floors-in-house-input').value.trim() : null;
  const banquet_hall_capacity = isCottage ? document.getElementById('banquet-hall-input').value.trim() : null;
  const parking_type = isCottage ? document.getElementById('parking-type-select').value : null;
  const cottage_material = isCottage ? document.getElementById('cottage-material-select').value : null;

  const guests = document.getElementById('guests').value;
  const beds = document.getElementById('total-beds').value.trim();
  const desc = document.getElementById('desc-input').value.trim();

  // Location features for quick search & categories
  const location_features = Array.from(document.querySelectorAll('input[name="location_feature"]:checked')).map(el => el.value);

  // Collect beds list details
  const bedsList = [];
  const bedInputs = [
    { id: 'bed-single', name: 'Односпальная кровать', icon: '🛌' },
    { id: 'bed-double-small', name: 'Полутороспальная кровать', icon: '🛏️' },
    { id: 'bed-double', name: 'Двуспальная кровать', icon: '🛏️' },
    { id: 'bed-kingsize', name: 'Двуспальная широкая (king-size)', icon: '🛏️' },
    { id: 'bed-sofa', name: 'Диван-кровать', icon: '🛋️' },
    { id: 'bed-armchair', name: 'Кресло-кровать', icon: '🛋️' }
  ];
  bedInputs.forEach(item => {
    const input = document.getElementById(item.id);
    if (input) {
      const val = parseInt(input.value) || 0;
      if (val > 0) {
        bedsList.push({ name: item.name, count: val, icon: item.icon });
      }
    }
  });
  
  // Step 3 Characteristics
  const bathroomEl = document.querySelector('input[name="bathroom"]:checked');
  const bathroom_type = bathroomEl ? bathroomEl.value : 'combined';
  
  // Balcony glazed / type
  const has_balcony_val = document.querySelector('#balcony-container input[value="none"]').checked ? 0 : 1;
  const balcony_type_el = Array.from(document.querySelectorAll('#balcony-container input:checked')).map(el => el.value).filter(val => val !== 'none')[0] || null;
  
  const territory = Array.from(document.querySelectorAll('input[name="territory"]:checked, input[name="house"]:checked')).map(el => el.value);
  
  // Cottage checklists
  const video_surveillance = isCottage ? Array.from(document.querySelectorAll('input[name="video_surveillance"]:checked')).map(el => el.value) : [];
  const neighborhood = isCottage ? Array.from(document.querySelectorAll('input[name="neighborhood"]:checked')).map(el => el.value) : [];
  const object_purpose = isCottage ? Array.from(document.querySelectorAll('input[name="object_purpose"]:checked')).map(el => el.value) : [];

  // Amenities
  let amenity_basic = [];
  let amenity_kitchen = [];
  let cottage_amenity_territory = [];
  let cottage_amenity_climate = [];
  let cottage_amenity_leisure = [];
  let cottage_amenity_service = [];
  let entertainment = [];

  if (isCottage) {
    cottage_amenity_territory = Array.from(document.querySelectorAll('input[name="cottage_amenity_territory"]:checked')).map(el => el.value);
    cottage_amenity_climate = Array.from(document.querySelectorAll('input[name="cottage_amenity_climate"]:checked')).map(el => el.value);
    cottage_amenity_leisure = Array.from(document.querySelectorAll('input[name="cottage_amenity_leisure"]:checked')).map(el => el.value);
    cottage_amenity_service = Array.from(document.querySelectorAll('input[name="cottage_amenity_service"]:checked')).map(el => el.value);
    entertainment = Array.from(document.querySelectorAll('input[name="entertainment"]:checked')).map(el => el.value);
  } else {
    amenity_basic = Array.from(document.querySelectorAll('input[name="amenity_basic"]:checked')).map(el => el.value);
    amenity_kitchen = Array.from(document.querySelectorAll('input[name="amenity_kitchen"]:checked')).map(el => el.value);
  }
  
  // Step 4 & 5
  const check_in_time = document.getElementById('checkin-time').value;
  const check_out_time = document.getElementById('checkout-time').value;
  const children_allowed = document.getElementById('children-allowed').checked ? 1 : 0;
  const animals_allowed = document.getElementById('animals-allowed').checked ? 1 : 0;
  const events_allowed = document.getElementById('events-allowed').checked ? 1 : 0;
  const smoking_allowed = document.getElementById('smoking-allowed').checked ? 1 : 0;
  
  const checking_rules = Array.from(document.querySelectorAll('input[name="checking_rule"]:checked')).map(el => el.value);
  if (smoking_allowed) {
    checking_rules.push('Разрешено курить');
  }
  
  const price = document.getElementById('price-weekday').value.trim();
  const rentByHours = document.getElementById('rent-by-hours').checked;
  const weekend_price = rentByHours ? (document.getElementById('price-hourly-input').value.trim() || 0) : 0;
  const payments = Array.from(document.querySelectorAll('input[name="payment_method"]:checked')).map(el => el.value);
  
  // Step 6
  const hostName = document.getElementById('host-name-input').value.trim();
  const phoneVal = document.getElementById('phone-input').value.trim();
  const phoneDigits = phoneVal.replace(/\D/g, '').replace(/^375/, '');
  const phone = phoneDigits ? `+375 ${formatBelarusPhone(phoneDigits)}` : '';
  const telegram = document.getElementById('telegram-input').value.trim();
  const whatsapp = document.getElementById('whatsapp-input').value.trim();
  const viber = document.getElementById('viber-input').value.trim();
  const unp = document.getElementById('unp-input') ? document.getElementById('unp-input').value.trim() : '';
  return {
    type, title, city, streetType, streetName, houseNumber, houseCorpus, metro, district, address,
    location_features,
    area, rooms, floor, total_floors, building_material, guests, beds, beds_list: bedsList, desc, price, weekend_price,
    bathroom_type, has_balcony: has_balcony_val, balcony_type: balcony_type_el,
    territory, amenity_basic, amenity_kitchen,
    plot_area, floors_in_house, banquet_hall_capacity, parking_type, cottage_material,
    video_surveillance, neighborhood, object_purpose,
    cottage_amenity_territory, cottage_amenity_climate, cottage_amenity_leisure, cottage_amenity_service, entertainment,
    check_in_time, check_out_time, children_allowed, animals_allowed, events_allowed,
    checking_rules, payments,
    hostName, phone, telegram, whatsapp, viber, unp,
    latitude: currentCoords[0],
    longitude: currentCoords[1],
    photos: uploadedPhotos,
    step: currentStepIndex + 1
  };
}

let isSubmitting = false;

function submitListing() {
  if (isSubmitting) return;

  clearAllFieldErrors();
  let firstErrorEl = null;

  function markError(el, msg) {
    if (!el) return;
    if (!firstErrorEl) firstErrorEl = el;
    markFieldError(el, msg);
  }

  const hostNameInput = document.getElementById('host-name-input');
  const phoneInput = document.getElementById('phone-input');
  const hostName = hostNameInput.value.trim();
  const phoneVal = phoneInput.value.trim();
  const phoneDigits = phoneVal.replace(/\D/g, '').replace(/^375/, '');
  
  if (!hostName) {
    markError(hostNameInput, 'Укажите имя арендодателя');
  }
  if (!phoneDigits || phoneDigits.length < 9) {
    markError(phoneInput, 'Укажите корректный номер телефона (9 цифр)');
  }

  const agree = document.getElementById('agree');
  if (!agree.checked) {
    agree.style.outline = '2px solid #C47D0C';
    if (!firstErrorEl) firstErrorEl = agree;
    showToast('⚠️ Необходимо согласиться с условиями размещения');
  }

  if (firstErrorEl) {
    firstErrorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    if (typeof firstErrorEl.focus === 'function') firstErrorEl.focus();
    return;
  }

  // Compile final listing details
  const fd = getFormDataObject();
  
  if (!fd.title || !fd.price) {
    showToast('⚠️ Заполните основные поля (Заголовок, Цена)');
    return;
  }

  // Disable button and set isSubmitting to true
  const submitBtn = document.querySelector('#step_contacts .btn-submit') || document.querySelector('.btn-submit[onclick="submitListing()"]');
  const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.7';
    submitBtn.style.cursor = 'not-allowed';
    submitBtn.innerHTML = `<span class="spinner"></span> Сохраняем...`;
  }
  isSubmitting = true;

  // Format address nicely
  let formattedAddress = fd.address;
  if (selectedObjectType !== 'cottage' && selectedObjectType !== 'agrotourism') {
    formattedAddress = fd.streetName;
    if (fd.streetType) formattedAddress = `${fd.streetType} ${fd.streetName}`;
    if (fd.houseNumber) formattedAddress = `${fd.city}, ${formattedAddress}, ${fd.houseNumber}`;
    if (fd.houseCorpus) formattedAddress = `${formattedAddress}к${fd.houseCorpus}`;
  }

  const imageBgGrads = [
    "linear-gradient(140deg,#7B1FA2,#E040FB)",
    "linear-gradient(140deg,#2C3E50,#3D5A6B)",
    "linear-gradient(140deg,#1B3A2D,#2E6B4F)",
    "linear-gradient(140deg,#2D1A3D,#4A2A6B)"
  ];
  const randomBg = imageBgGrads[Math.floor(Math.random() * imageBgGrads.length)];

  // Prepare payload for AJAX and server DB
  const payload = {
    ...fd,
    address: formattedAddress,
    imageBg: randomBg,
    photos: uploadedPhotos
  };
  if (isEditMode) {
    payload.id = editListingId;
  }

  // Helper function to reset button on error
  function resetSubmitButton() {
    isSubmitting = false;
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.style.opacity = '';
      submitBtn.style.cursor = '';
      submitBtn.innerHTML = originalBtnHtml;
    }
  }

  // Perform AJAX to server DB
  fetch("{{ route('listings.store') }}", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": "{{ csrf_token() }}"
    },
    body: JSON.stringify(payload)
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      const successTitle = document.getElementById('success-title');
      const successText = document.getElementById('success-text');
      
      if (isEditMode) {
        successTitle.textContent = 'Объявление обновлено!';
        if (data.listing && data.listing.status === 'active') {
          successText.textContent = 'Ваши изменения успешно сохранены! Объявление опубликовано и показывается гостям.';
        } else {
          successText.textContent = 'Ваши изменения успешно сохранены! Не забудьте активировать размещение в личном кабинете, чтобы объявление показывалось гостям.';
        }
      } else {
        if (data.listing && data.listing.status === 'active') {
          successTitle.textContent = 'Объявление опубликовано!';
          successText.textContent = 'Ваше объявление успешно опубликовано и теперь показывается гостям на платформе!';
        } else {
          successTitle.textContent = 'Объявление создано!';
          successText.innerHTML = 'Чтобы оно появилось в поиске и к вам пошли клиенты, перейдите в <strong>Личный кабинет → Мои объявления → Активировать бесплатно</strong>.';
        }
      }
      
      // Trigger success modal overlay
      document.getElementById('success-overlay').classList.add('show');
    } else {
      showToast('❌ Ошибка размещения: ' + data.message);
      resetSubmitButton();
    }
  })
  .catch(() => {
    showToast('❌ Ошибка сети: не удалось сохранить объявление на сервере.');
    resetSubmitButton();
  });
}

function closeSuccess(e) {
  window.location.href = '/dashboard';
}

// Yandex Maps Helpers
function setupMapListeners() {
  const streetInput = document.getElementById('street-name-input');
  const houseInput = document.getElementById('house-number-input');
  const corpusInput = document.getElementById('house-corpus-input');
  const typeInput = document.getElementById('street-type-input');
  const citySelect = document.getElementById('apartment-city-select');
  const customCityWrap = document.getElementById('apartment-city-custom-wrap');
  const customCityInput = document.getElementById('apartment-city-custom-input');

  if (citySelect) {
    citySelect.addEventListener('change', function() {
      if (this.value === 'Другой') {
        if (customCityWrap) customCityWrap.style.display = 'block';
        if (customCityInput) customCityInput.focus();
      } else {
        if (customCityWrap) customCityWrap.style.display = 'none';
        if (customCityInput) customCityInput.value = '';
      }
    });
  }

  if (streetInput) streetInput.addEventListener('blur', geocodeAddress);
  if (houseInput) houseInput.addEventListener('blur', geocodeAddress);
  if (corpusInput) corpusInput.addEventListener('blur', geocodeAddress);
  if (typeInput) typeInput.addEventListener('blur', geocodeAddress);
  if (citySelect) citySelect.addEventListener('change', geocodeAddress);
  if (customCityInput) customCityInput.addEventListener('blur', geocodeAddress);

  const regionSelect = document.getElementById('region-select');
  const districtSelect = document.getElementById('district-select');
  const detailedAddressInput = document.getElementById('detailed-address-input');

  if (regionSelect) regionSelect.addEventListener('change', geocodeAddress);
  if (districtSelect) districtSelect.addEventListener('change', geocodeAddress);
  if (detailedAddressInput) detailedAddressInput.addEventListener('blur', geocodeAddress);

  // Initialize autocomplete suggest boxes (safely in case API key does not support SuggestView)
  ymaps.ready(function() {
    try {
      if (typeof ymaps.SuggestView === 'function') {
        if (streetInput) {
          var streetSuggest = new ymaps.SuggestView('street-name-input', { provider: 'yandex#map' });
          streetSuggest.events.add('select', function(e) {
            setTimeout(geocodeAddress, 150);
          });
        }
        if (detailedAddressInput) {
          var detailedSuggest = new ymaps.SuggestView('detailed-address-input', { provider: 'yandex#map' });
          detailedSuggest.events.add('select', function(e) {
            setTimeout(geocodeAddress, 150);
          });
        }
      }
    } catch (e) {
      // SuggestView may not be enabled on the API key; manual geocoding on blur handles address lookup
    }
  });
}

function geocodeAddress() {
  const isCottage = (selectedObjectType === 'cottage' || selectedObjectType === 'agrotourism');
  let fullAddr = '';

  if (isCottage) {
    const region = document.getElementById('region-select').value;
    const districtVal = document.getElementById('district-select').value;
    const detailedAddress = document.getElementById('detailed-address-input').value.trim();

    if (!detailedAddress) {
      return; // Wait until detailed address is filled
    }

    fullAddr = detailedAddress;
    if (districtVal) fullAddr += ', ' + districtVal + ' район';
    if (region) fullAddr += ', ' + region;
  } else {
    let city = document.getElementById('apartment-city-select').value;
    if (city === 'Другой') {
      city = document.getElementById('apartment-city-custom-input').value.trim();
    }
    const streetType = document.getElementById('street-type-input').value.trim();
    const streetName = document.getElementById('street-name-input').value.trim();
    const houseNumber = document.getElementById('house-number-input').value.trim();
    const houseCorpus = document.getElementById('house-corpus-input').value.trim();

    if (!city || !streetName || !houseNumber) {
      return; // Wait until city, street and house are filled
    }

    fullAddr = city;
    if (streetType) fullAddr += ', ' + streetType + ' ' + streetName;
    else fullAddr += ', ' + streetName;
    fullAddr += ', ' + houseNumber;
    if (houseCorpus) fullAddr += ' корпус ' + houseCorpus;
  }

  ymaps.geocode(fullAddr, { results: 1 }).then(function (res) {
    const firstGeoObject = res.geoObjects.get(0);
    if (firstGeoObject) {
      const coords = firstGeoObject.geometry.getCoordinates();
      currentCoords = coords;
      
      // Hide placeholder
      const placeholder = document.getElementById('map-placeholder');
      if (placeholder) placeholder.style.display = 'none';

      initListingMap(coords);
    }
  }).catch(function(err) {
    console.error("Geocoding error:", err);
  });
}

function initListingMap(coords) {
  if (myMap) {
    myMap.setCenter(coords, 16);
    if (myPlacemark) {
      myPlacemark.geometry.setCoordinates(coords);
    } else {
      createPlacemark(coords);
    }
  } else {
    myMap = new ymaps.Map("listing-map", {
      center: coords,
      zoom: 16,
      controls: ['zoomControl', 'fullscreenControl']
    });
    createPlacemark(coords);
  }
}

function createPlacemark(coords) {
  myPlacemark = new ymaps.Placemark(coords, {
    hintContent: 'Перетащите метку в точное место',
    balloonContent: 'Ваше жилье здесь'
  }, {
    preset: 'islands#amberDotIcon',
    draggable: true
  });

  myPlacemark.events.add('dragend', function () {
    currentCoords = myPlacemark.geometry.getCoordinates();
    updateAddressInputsFromCoords(currentCoords);
  });

  myMap.geoObjects.add(myPlacemark);
}

// Reverse Geocoding Address Auto-Filler
function updateAddressInputsFromCoords(coords) {
  ymaps.geocode(coords).then(function (res) {
    var firstGeoObject = res.geoObjects.get(0);
    if (!firstGeoObject) return;

    var components = firstGeoObject.properties.get('metaDataProperty.GeocoderMetaData.Address.Components');
    if (!components) return;

    var city = '';
    var district = '';
    var street = '';
    var house = '';
    var province = '';

    components.forEach(function (comp) {
      if (comp.kind === 'locality') {
        city = comp.name;
      } else if (comp.kind === 'district') {
        district = comp.name;
      } else if (comp.kind === 'street') {
        street = comp.name;
      } else if (comp.kind === 'house') {
        house = comp.name;
      } else if (comp.kind === 'province') {
        province = comp.name;
      }
    });

    const isCottage = (selectedObjectType === 'cottage' || selectedObjectType === 'agrotourism');

    if (isCottage) {
      var detailedAddr = '';
      if (street) detailedAddr += street;
      if (house) detailedAddr += (detailedAddr ? ', ' : '') + house;
      
      const detailedInput = document.getElementById('detailed-address-input');
      if (detailedInput) {
        detailedInput.value = detailedAddr;
      }

      const regionSelect = document.getElementById('region-select');
      if (regionSelect && city) {
        var matchFound = false;
        for (var i = 0; i < regionSelect.options.length; i++) {
          var optVal = regionSelect.options[i].value;
          if (optVal && (city.toLowerCase().includes(optVal.toLowerCase()) || optVal.toLowerCase().includes(city.toLowerCase()))) {
            regionSelect.value = optVal;
            matchFound = true;
            break;
          }
        }
        if (!matchFound && province) {
          for (var i = 0; i < regionSelect.options.length; i++) {
            var optVal = regionSelect.options[i].value;
            if (optVal && (province.toLowerCase().includes(optVal.toLowerCase()) || optVal.toLowerCase().includes(province.toLowerCase()))) {
              regionSelect.value = optVal;
              break;
            }
          }
        }
      }
      
      if (typeof updateDistricts === 'function') {
        updateDistricts();
      }
      
      const districtSelect = document.getElementById('district-select');
      if (districtSelect && district) {
        var cleanDistrict = district.replace(' район', '').replace(' р-н', '').trim();
        for (var i = 0; i < districtSelect.options.length; i++) {
          var optVal = districtSelect.options[i].value;
          if (optVal && (optVal.toLowerCase().includes(cleanDistrict.toLowerCase()) || cleanDistrict.toLowerCase().includes(optVal.toLowerCase()))) {
            districtSelect.value = optVal;
            break;
          }
        }
      }
    } else {
      const citySelect = document.getElementById('apartment-city-select');
      const customCityInput = document.getElementById('apartment-city-custom-input');
      const customCityWrap = document.getElementById('apartment-city-custom-wrap');
      if (citySelect && city) {
        let matchFound = false;
        for (var i = 0; i < citySelect.options.length; i++) {
          var optVal = citySelect.options[i].value;
          if (optVal && optVal !== 'Другой' && (city.toLowerCase().includes(optVal.toLowerCase()) || optVal.toLowerCase().includes(city.toLowerCase()))) {
            citySelect.value = optVal;
            matchFound = true;
            break;
          }
        }
        if (!matchFound) {
          citySelect.value = 'Другой';
          if (customCityInput) {
            customCityInput.value = city;
          }
          if (customCityWrap) {
            customCityWrap.style.display = 'block';
          }
        } else {
          if (customCityInput) {
            customCityInput.value = '';
          }
          if (customCityWrap) {
            customCityWrap.style.display = 'none';
          }
        }
      }

      if (street) {
        var streetNameInput = document.getElementById('street-name-input');
        var streetTypeInput = document.getElementById('street-type-input');
        
        var streetTypes = [
          { key: 'ул.', match: ['улица', 'ул.'] },
          { key: 'пр-т', match: ['проспект', 'пр-т', 'пр.'] },
          { key: 'пер.', match: ['переулок', 'пер.'] },
          { key: 'бул.', match: ['бульвар', 'бул.'] },
          { key: 'пл.', match: ['площадь', 'пл.'] },
          { key: 'ш.', match: ['шоссе', 'ш.'] },
          { key: 'тр-т', match: ['тракт', 'тр-т'] }
        ];

        var detectedType = 'ул.';
        var parsedName = street;

        for (var t of streetTypes) {
          var matched = false;
          for (var m of t.match) {
            if (street.toLowerCase().startsWith(m + ' ')) {
              detectedType = t.key;
              parsedName = street.substring(m.length + 1);
              matched = true;
              break;
            } else if (street.toLowerCase().endsWith(' ' + m)) {
              detectedType = t.key;
              parsedName = street.substring(0, street.length - (m.length + 1));
              matched = true;
              break;
            }
          }
          if (matched) break;
        }

        if (streetTypeInput) streetTypeInput.value = detectedType;
        if (streetNameInput) streetNameInput.value = parsedName;
      }

      if (house) {
        var houseNumberInput = document.getElementById('house-number-input');
        var houseCorpusInput = document.getElementById('house-corpus-input');

        var num = house;
        var corp = '';

        if (house.includes('к')) {
          var parts = house.split('к');
          num = parts[0];
          corp = parts[1];
        } else if (house.includes('корпус')) {
          var parts = house.split('корпус');
          num = parts[0].trim();
          corp = parts[1].trim();
        }

        if (houseNumberInput) houseNumberInput.value = num;
        if (houseCorpusInput) houseCorpusInput.value = corp;
      }
    }
    showToast('✓ Адрес обновлен по маркеру!');
  }).catch(function (err) {
    console.error("Reverse geocoding error:", err);
  });
}
</script>
@endsection
