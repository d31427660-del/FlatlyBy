@extends('layouts.app')

@section('title', $seoTitle ?? 'Статья блога — Flatly.by')
@section('meta_description', $seoDescription ?? 'Полезные статьи о посуточной аренде жилья и путешествиях по Беларуси.')

@section('head')
<style>
  /* ===== BLOG ARTICLE VIEW ===== */
  .article-container {
    max-width: 900px;
    margin: 40px auto 80px;
    padding: 0 32px;
  }

  .breadcrumbs-nav {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    color: var(--slate-600);
    margin-bottom: 24px;
  }
  .breadcrumbs-nav a {
    color: var(--slate-600);
    text-decoration: none;
    transition: color 0.15s;
  }
  .breadcrumbs-nav a:hover {
    color: var(--amber-dark);
  }
  .breadcrumbs-separator {
    color: var(--border);
  }

  .article-header {
    margin-bottom: 32px;
  }

  .article-meta {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 13px;
    color: var(--slate-600);
    margin-bottom: 16px;
    font-weight: 600;
  }
  .article-source-badge {
    background: var(--slate-50);
    border: 1px solid var(--border);
    padding: 3px 10px;
    border-radius: 20px;
    font-weight: 700;
    color: var(--slate-900);
    font-size: 11px;
    text-transform: uppercase;
  }
  .article-source-badge.own {
    border-color: rgba(245, 166, 35, 0.3);
    background: #FFFDF5;
    color: var(--amber-dark);
  }

  .article-title {
    font-family: 'Syne', 'Inter', sans-serif;
    font-weight: 800;
    font-size: 38px;
    color: var(--slate-900);
    letter-spacing: -0.8px;
    line-height: 1.25;
    margin-bottom: 20px;
  }

  /* Big Hero Image */
  .article-hero-wrap {
    width: 100%;
    aspect-ratio: 16/9;
    border-radius: 24px;
    overflow: hidden;
    margin-bottom: 40px;
    border: 1px solid var(--border);
    background: #f7fafc;
  }
  .article-hero-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* Rich Typography */
  .article-content {
    font-size: 16.5px;
    line-height: 1.8;
    color: #2d3748;
    margin-bottom: 48px;
  }
  .article-content p {
    margin-bottom: 24px;
  }
  .article-content h3 {
    font-size: 25px;
    font-weight: 800;
    color: var(--slate-900);
    margin-top: 44px;
    margin-bottom: 18px;
    line-height: 1.3;
  }
  .article-content h4 {
    font-size: 20px;
    font-weight: 700;
    color: var(--slate-900);
    margin-top: 34px;
    margin-bottom: 14px;
  }
  .article-content ul, .article-content ol {
    margin-bottom: 24px;
    padding-left: 24px;
  }
  .article-content li {
    margin-bottom: 8px;
  }
  .article-content blockquote {
    border-left: 4px solid var(--amber);
    background: #FFFDF5;
    border-radius: 0 12px 12px 0;
    padding: 16px 24px;
    font-style: italic;
    color: var(--slate-700);
    margin: 32px 0;
    font-size: 16px;
    line-height: 1.6;
  }
  
  /* Tables styling inside article */
  .article-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 28px 0;
    font-size: 15px;
    background: var(--white);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
    border: 1px solid var(--border);
  }
  .article-content th, .article-content td {
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    text-align: left;
    vertical-align: middle;
  }
  .article-content th {
    background: var(--slate-50);
    font-weight: 700;
    color: var(--slate-900);
    border-bottom: 2px solid var(--border);
  }
  .article-content tr:last-child td {
    border-bottom: none;
  }
  .article-content tr:nth-child(even) td {
    background: #fcfdfe;
  }
  .article-content table strong {
    color: var(--slate-900);
  }
  
  /* Responsive table container */
  .table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 28px;
    border-radius: 12px;
  }

  /* Checklist Styles */
  .article-content ul.checklist {
    list-style: none;
    padding-left: 0;
    margin: 28px 0;
  }
  .article-content ul.checklist li {
    position: relative;
    padding-left: 36px;
    margin-bottom: 20px;
    line-height: 1.6;
  }
  .article-content ul.checklist li input[type="checkbox"] {
    position: absolute;
    left: 4px;
    top: 4px;
    width: 18px;
    height: 18px;
    accent-color: var(--amber);
    cursor: default;
    pointer-events: none;
    border-radius: 4px;
  }

  /* Source link block */
  .article-source-credit {
    background: var(--slate-50);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 56px;
    display: flex;
    align-items: center;
    gap: 16px;
  }
  .article-source-credit svg {
    color: var(--amber);
    flex-shrink: 0;
  }
  .article-source-credit p {
    margin: 0;
    font-size: 14.5px;
    color: var(--slate-700);
    line-height: 1.5;
  }
  .article-source-credit a {
    color: var(--amber-dark);
    text-decoration: underline;
    font-weight: 600;
  }
  .article-source-credit a:hover {
    color: var(--slate-900);
  }

  /* Related section */
  .related-section {
    border-top: 1px solid var(--border);
    padding-top: 48px;
    margin-bottom: 40px;
  }
  .related-section-title {
    font-size: 22px;
    font-weight: 800;
    color: var(--slate-900);
    margin-bottom: 24px;
  }
  .related-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 24px;
  }

  @media (max-width: 768px) {
    .article-container {
      padding: 0 16px;
      margin-top: 24px;
    }
    .article-title {
      font-size: 28px;
    }
    .related-grid {
      grid-template-columns: 1fr;
      gap: 20px;
    }
  }

  /* Compact Card for Related */
  .related-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
    cursor: pointer;
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .related-card:hover {
    border-color: var(--amber);
    box-shadow: 0 8px 24px rgba(0,0,0,0.03);
  }
  .related-img-wrap {
    width: 100%;
    aspect-ratio: 16/10;
    overflow: hidden;
  }
  .related-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .related-body {
    padding: 16px;
    display: flex;
    flex-direction: column;
    flex: 1;
  }
  .related-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--slate-900);
    line-height: 1.4;
    margin-bottom: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
</style>
@endsection

@section('content')
<div class="article-container">
  <!-- Breadcrumbs -->
  <div class="breadcrumbs-nav">
    <a href="{{ route('home') }}">Главная</a>
    <span class="breadcrumbs-separator">/</span>
    <a href="{{ route('blog.index') }}">Блог</a>
    <span class="breadcrumbs-separator">/</span>
    <span style="display:inline-block; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; vertical-align:bottom;">{{ $post->title }}</span>
  </div>

  <!-- Header -->
  <div class="article-header">
    <div class="article-meta">
      <span>{{ \Carbon\Carbon::parse($post->created_at)->format('d.m.Y') }}</span>
      @php
        $v = (int) ($post->views ?? 0);
        $vMod10 = $v % 10;
        $vMod100 = $v % 100;
        if ($vMod10 == 1 && $vMod100 != 11) {
            $viewsSuffix = 'просмотр';
        } elseif (in_array($vMod10, [2, 3, 4]) && !in_array($vMod100, [12, 13, 14])) {
            $viewsSuffix = 'просмотра';
        } else {
            $viewsSuffix = 'просмотров';
        }
      @endphp
      <span style="display:inline-flex; align-items:center; gap: 4px;">
        <x-solar-eye-linear width="14" height="14" style="opacity:0.6;" />
        {{ $post->views }} {{ $viewsSuffix }}
      </span>
      <span class="article-source-badge {{ empty($post->source_url) ? 'own' : '' }}">
        {{ $post->source_name ?: 'Flatly' }}
      </span>
    </div>
    <h1 class="article-title">{{ $post->title }}</h1>
  </div>

  <!-- Hero Image -->
  <div class="article-hero-wrap">
    <img class="article-hero-img" src="{{ $post->image ?: '/images/news_apartment_appeal.png' }}" alt="{{ $post->title }}">
  </div>

  <!-- Article Content -->
  <div class="article-content">
    {!! $post->content !!}
  </div>

  <!-- Quick Rent Links -->
  <div style="background: #F8FAFC; border: 1px solid var(--border); border-radius: 20px; padding: 24px; margin-bottom: 32px; text-align: left;">
    <h4 style="font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 800; color: var(--slate-900); margin: 0 0 14px 0;">Планируете поездку по Беларуси? Снимайте жилье напрямую:</h4>
    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 16px;">
      <a href="/minsk/kvartiry-na-sutki-u-metro" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 6px 14px; font-size: 13px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Минск у метро</a>
      <a href="/minsk/s-konditsionerom" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 6px 14px; font-size: 13px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Минск с кондиционером</a>
      <a href="/minsk/do-100" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 6px 14px; font-size: 13px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Квартиры в Минске до 100 BYN</a>
      <a href="/brest/u-vokzala" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 6px 14px; font-size: 13px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Брест у вокзала</a>
      <a href="/brest/dlya-semyi" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 6px 14px; font-size: 13px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Семейные квартиры в Бресте</a>
      <a href="/grodno/v-centre" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 6px 14px; font-size: 13px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Гродно в центре</a>
      <a href="/gomel/u-parka" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 6px 14px; font-size: 13px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Гомель у парка</a>
    </div>

    <h4 style="font-family: 'Outfit', sans-serif; font-size: 15px; font-weight: 800; color: var(--slate-900); margin: 18px 0 12px 0; border-top: 1px solid var(--border); padding-top: 16px;">Популярные районы Минска:</h4>
    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
      <a href="/minsk/uruchye" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 5px 12px; font-size: 12.5px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Уручье</a>
      <a href="/minsk/kamennaya-gorka" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 5px 12px; font-size: 12.5px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Каменная Горка</a>
      <a href="/minsk/nemiga" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 5px 12px; font-size: 12.5px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Немига</a>
      <a href="/minsk/suharevo" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 5px 12px; font-size: 12.5px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Сухарево</a>
      <a href="/minsk/zelenyy-lug" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 5px 12px; font-size: 12.5px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Зелёный Луг</a>
      <a href="/minsk/malinovka" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 5px 12px; font-size: 12.5px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Малиновка</a>
      <a href="/minsk/serebryanka" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 5px 12px; font-size: 12.5px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Серебрянка</a>
      <a href="/minsk/yugo-zapad" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 5px 12px; font-size: 12.5px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Юго-Запад</a>
      <a href="/minsk/vesnyanka" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 5px 12px; font-size: 12.5px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Веснянка</a>
      <a href="/minsk/kuntsevschina" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 5px 12px; font-size: 12.5px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Кунцевщина</a>
      <a href="/minsk/minsk-mir" style="background: var(--white); border: 1px solid var(--border); border-radius: 20px; padding: 5px 12px; font-size: 12.5px; font-weight: 600; color: var(--slate-700); text-decoration: none; transition: all 0.2s;" onmouseover="this.style.borderColor='var(--amber)'; this.style.color='var(--amber-dark)'" onmouseout="this.style.borderColor='var(--border)'; this.style.color='var(--slate-700)'">Минск Мир</a>
    </div>
  </div>

  <!-- Source credit block -->
  <div class="article-source-credit">
    <x-solar-book-linear width="24" height="24" />
    <div>
      @if(!empty($post->source_url))
        <p>Материал заимствован из источника <b>{{ $post->source_name }}</b> для ознакомительных целей. Оригинал статьи вы можете прочитать на первоисточнике по ссылке: <a href="{{ $post->source_url }}" target="_blank" rel="noopener noreferrer">{{ $post->source_name }}</a>.</p>
      @else
        <p>Материал подготовлен редакцией <b>Flatly.by</b> — белорусского сервиса прямой краткосрочной аренды квартир и коттеджей без посредников. Копирование материалов разрешено только с указанием активной ссылки на наш сайт.</p>
      @endif
    </div>
  </div>

  <!-- Related section -->
  @if($related && count($related) > 0)
    <div class="related-section">
      <h3 class="related-section-title">Читать также</h3>
      <div class="related-grid">
        @foreach($related as $rel)
          <div class="related-card" onclick="window.location.href='{{ route('blog.show', $rel->slug) }}'">
            <div class="related-img-wrap">
              <img class="related-img" src="{{ $rel->image ?: '/images/news_apartment_appeal.png' }}" alt="{{ $rel->title }}" loading="lazy">
            </div>
            <div class="related-body">
              <h4 class="related-title">{{ $rel->title }}</h4>
              <span style="font-size:12px; font-weight:700; color:var(--amber-dark); margin-top:auto; display:inline-block; padding-top:8px;">Читать статью ›</span>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

  <!-- Back to blog button -->
  <div style="text-align: center; margin-top: 56px;">
    <a href="{{ route('blog.index') }}" class="btn-action" style="padding: 10px 24px; border-radius: 8px; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
      ← Вернуться к списку статей
    </a>
  </div>
</div>
@endsection
