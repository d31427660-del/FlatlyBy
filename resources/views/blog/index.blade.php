@extends('layouts.app')

@section('title', $seoTitle ?? 'Блог и гид по Беларуси — Flatly.by')
@section('meta_description', $seoDescription ?? 'Полезные статьи о посуточной аренде жилья и путешествиях по Беларуси.')

@section('head')
<style>
  /* ===== BLOG LAYOUT ===== */
  .blog-container {
    max-width: 1400px;
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

  .blog-header {
    margin-bottom: 48px;
    text-align: center;
  }
  .blog-title {
    font-family: 'Syne', 'Inter', sans-serif;
    font-weight: 800;
    font-size: 42px;
    color: var(--slate-900);
    letter-spacing: -1px;
    margin-bottom: 12px;
    line-height: 1.2;
  }
  .blog-subtitle {
    font-size: 16px;
    color: var(--slate-600);
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.6;
  }

  /* Grid Layout */
  .blog-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 32px;
    margin-bottom: 56px;
  }

  @media (max-width: 992px) {
    .blog-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 24px;
    }
    .blog-title {
      font-size: 32px;
    }
  }

  @media (max-width: 600px) {
    .blog-grid {
      grid-template-columns: 1fr;
      gap: 24px;
    }
    .blog-container {
      padding: 0 16px;
      margin-top: 24px;
    }
    .blog-title {
      font-size: 26px;
    }
  }

  /* Post Card */
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
    position: relative;
  }
  .blog-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .blog-card:hover .blog-img {
    transform: scale(1.05);
  }

  .blog-card-body {
    padding: 24px;
    display: flex;
    flex-direction: column;
    flex: 1;
  }

  .blog-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 12px;
    color: var(--slate-600);
    margin-bottom: 12px;
    font-weight: 500;
  }
  .blog-source-badge {
    background: var(--slate-50);
    border: 1px solid var(--border);
    padding: 2px 8px;
    border-radius: 20px;
    font-weight: 700;
    color: var(--slate-900);
    font-size: 10.5px;
    text-transform: uppercase;
  }
  .blog-source-badge.own {
    border-color: rgba(245, 166, 35, 0.3);
    background: #FFFDF5;
    color: var(--amber-dark);
  }

  .blog-card-title {
    font-size: 19px;
    font-weight: 700;
    color: var(--slate-900);
    line-height: 1.4;
    margin-bottom: 12px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .blog-card-desc {
    font-size: 14px;
    color: var(--slate-600);
    line-height: 1.5;
    margin-bottom: 20px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
  }

  .blog-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 16px;
    border-top: 1px solid var(--border);
    font-size: 13px;
    font-weight: 700;
    color: var(--amber-dark);
  }
  .blog-card-footer span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  /* Custom pagination styling */
  .blog-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 56px;
  }
  .blog-pagination .page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 40px;
    min-width: 40px;
    padding: 0 14px;
    border-radius: 10px;
    border: 1px solid var(--border);
    background: var(--white);
    color: var(--slate-900);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
  }
  .blog-pagination .page-link:hover:not(.disabled) {
    border-color: var(--amber);
    color: var(--amber-dark);
    background: #fffbeb;
  }
  .blog-pagination .page-link.active {
    background: var(--amber);
    border-color: var(--amber);
    color: var(--white);
    cursor: default;
  }
  .blog-pagination .page-link.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #f7fafc;
  }
</style>
@endsection

@section('content')
<div class="blog-container">
  <!-- Breadcrumbs -->
  <div class="breadcrumbs-nav">
    <a href="{{ route('home') }}">Главная</a>
    <span class="breadcrumbs-separator">/</span>
    <span>Блог</span>
  </div>

  <!-- Header -->
  <div class="blog-header">
    <h1 class="blog-title">Гид по Беларуси</h1>
    <p class="blog-subtitle">Полезные статьи о путешествиях, гид по достопримечательностям и практичные советы для хозяев и гостей от команды Flatly.</p>
  </div>

  <!-- Articles Grid -->
  @if($posts && count($posts) > 0)
    <div class="blog-grid">
      @foreach($posts as $post)
        @php
          $isOwn = empty($post->source_url);
        @endphp
        <a href="{{ route('blog.show', $post->slug) }}" class="blog-card" style="text-decoration: none; color: inherit;">
          <div class="blog-img-wrap">
            <img class="blog-img" src="{{ $post->image ?: '/images/news_apartment_appeal.png' }}" alt="{{ $post->title }}" loading="lazy">
          </div>
          <div class="blog-card-body">
            <div class="blog-meta">
              <span>{{ \Carbon\Carbon::parse($post->created_at)->format('d.m.Y') }}</span>
              <span class="blog-source-badge {{ $isOwn ? 'own' : '' }}">
                {{ $post->source_name ?: 'Flatly' }}
              </span>
            </div>
            <h2 class="blog-card-title">{{ $post->title }}</h2>
            <p class="blog-card-desc">{{ $post->summary }}</p>
            <div class="blog-card-footer">
              <span>Читать статью ›</span>
              <span style="color: var(--slate-600); font-weight: 500; font-size:12px; display:inline-flex; align-items:center; gap: 4px;">
                <x-solar-eye-linear width="14" height="14" style="opacity:0.6;" />
                {{ $post->views }}
              </span>
            </div>
          </div>
        </a>
      @endforeach
    </div>

    <!-- Pagination -->
    @if($posts->hasPages())
      <div class="blog-pagination">
        @if($posts->onFirstPage())
          <span class="page-link disabled">« Назад</span>
        @else
          <a href="{{ $posts->previousPageUrl() }}" class="page-link">« Назад</a>
        @endif

        @foreach($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
          @if($page == $posts->currentPage())
            <span class="page-link active">{{ $page }}</span>
          @else
            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
          @endif
        @endforeach

        @if($posts->hasMorePages())
          <a href="{{ $posts->nextPageUrl() }}" class="page-link">Вперед »</a>
        @else
          <span class="page-link disabled">Вперед »</span>
        @endif
      </div>
    @endif
  @else
    <div style="text-align: center; padding: 80px 0; color: var(--slate-600);">
      <x-solar-document-text-linear width="48" height="48" style="margin-bottom:16px; opacity:0.5; display:inline-block;" />
      <h3 style="font-size:20px; font-weight:700; color:var(--slate-900); margin-bottom:8px;">Статьи не найдены</h3>
      <p>Пожалуйста, примените миграции на сервере, чтобы наполнить блог полезными материалами.</p>
    </div>
  @endif
</div>
@endsection
