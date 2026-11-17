@if(isset($breadcrumbs) && count($breadcrumbs) > 0)
  <nav class="breadcrumbs-nav" aria-label="Breadcrumb" style="margin-bottom: 12px; overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch;">
    <ol class="breadcrumbs-list" itemscope itemtype="https://schema.org/BreadcrumbList" style="display: flex; flex-wrap: nowrap; list-style: none; padding: 0; margin: 0; font-size: 13px; color: var(--gray-text);">
      @foreach($breadcrumbs as $index => $bc)
        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" style="display: flex; align-items: center;">
          @if($index > 0)
            <span class="breadcrumb-separator" style="margin: 0 8px; color: var(--gray-text); opacity: 0.6;">/</span>
          @endif
          @if($bc['url'] && $index < count($breadcrumbs) - 1)
            <a itemprop="item" href="{{ $bc['url'] }}" style="color: var(--slate-600); text-decoration: none; transition: color 0.2s;">
              <span itemprop="name">{{ $bc['title'] }}</span>
            </a>
          @else
            <span itemprop="name" class="breadcrumb-active" style="color: var(--slate-900); font-weight: 500;">{{ $bc['title'] }}</span>
            <link itemprop="item" href="{{ request()->url() }}" />
          @endif
          <meta itemprop="position" content="{{ $index + 1 }}" />
        </li>
      @endforeach
    </ol>
  </nav>
@endif
