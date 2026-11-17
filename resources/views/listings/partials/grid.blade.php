@foreach($listings as $index => $item)
  @php
    $itemsPerPage = 20;
    $startIdx = (($currentPage ?? 1) - 1) * $itemsPerPage;
    $endIdx = ($currentPage ?? 1) * $itemsPerPage;
    $visibleOnLoad = ($index >= $startIdx && $index < $endIdx);
    $pageNumber = (int)($index / $itemsPerPage) + 1;
  @endphp
  @include('listings.partials.card', ['item' => $item, 'visibleOnLoad' => $visibleOnLoad, 'isLcp' => ($index < 2)])
  
  @if(($index % $itemsPerPage === 1) || ($index % $itemsPerPage === 0 && $index === count($listings) - 1))
    @include('listings.partials.ad_banner', [
      'visibleOnLoad' => (($currentPage ?? 1) === $pageNumber),
      'pageNumber' => $pageNumber
    ])
  @endif
@endforeach
