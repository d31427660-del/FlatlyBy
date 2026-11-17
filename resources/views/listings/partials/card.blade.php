@php
  $cat = 'apartments';
  $titleLower = mb_strtolower($item->title ?? '');
  $descLower = mb_strtolower($item->description ?? '');
  
  if ($item->type === 'cottage') {
      $cat = 'cottages';
  } elseif ($item->weekend_price > 0 && $item->type !== 'cottage') {
      $cat = 'hourly';
  } else {
      $cat = 'apartments';
  }

  if ($item instanceof \App\Models\Listing) {
      $bedrooms = $item->rooms;
      $beds = $item->sleeping_places;
      $bathrooms = $item->bathrooms_count;
      $city = $item->city ?: 'Минск';
      $filterTags = $item->filter_tags;
      $hostName = $item->host_name ?? ($item->host->name ?? 'Частный владелец');
  } else {
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
      $filterTags = $specs['tags'];
      $hostName = $specs['host'];
  }

  $hasImage = isset($item->image) && !empty($item->image);
  $imgUrl = isset($item->image) ? $item->image : '';
  $bg = '';
  $emoji = '';
  if (!$hasImage) {
      $photos = $item->photos ?? [];
      $bg = $photos[0]['bg'] ?? 'linear-gradient(140deg,#2C3E50,#3D5A6B)';
      $emoji = $photos[0]['emoji'] ?? '🏢';
  }

  $cardPhotos = [];
  if ($item instanceof \App\Models\Listing) {
      $itemPhotos = $item->photos;
      if (is_string($itemPhotos)) {
          $itemPhotos = json_decode($itemPhotos, true) ?: [];
      }
      if (!empty($itemPhotos) && is_array($itemPhotos)) {
          $cardPhotos = array_values(array_unique(array_filter($itemPhotos)));
      }
      if (empty($cardPhotos) && !empty($item->image)) {
          $cardPhotos[] = $item->image;
      }
  } else {
      $cardPhotos = $item->photos ?? [];
      if (empty($cardPhotos) && !empty($item->image)) {
          $cardPhotos[] = $item->image;
      }
  }
  if (empty($cardPhotos)) {
      $cardPhotos[] = ['bg' => $bg ?: 'linear-gradient(140deg,#2C3E50,#3D5A6B)', 'emoji' => $emoji ?: '🏢'];
  }
  $cardPhotos = array_slice($cardPhotos, 0, 5);

  // Compile payment tags
  $paymentsList = [];
  if ($item instanceof \App\Models\Listing) {
      $paymentsList = $item->payments ?? [];
  } else {
      $mockPayments = [
          1 => ['Наличными', 'ЕРИП', 'Иной'],
          2 => ['Наличными', 'Банковской картой', 'ЕРИП'],
          3 => ['Наличными', 'Банковской картой', 'ЕРИП'],
          4 => ['Наличными', 'Безналичный расчет', 'ЕРИП'],
          5 => ['Наличными', 'ЕРИП'],
          6 => ['Наличными', 'Банковской картой', 'Безналичный расчет']
      ];
      $paymentsList = $mockPayments[$item->id] ?? ['Наличными'];
  }
  $paymentTags = [];
  foreach ($paymentsList as $pay) {
      $pLower = mb_strtolower($pay);
      if (str_contains($pLower, 'наличн')) $paymentTags[] = 'cash';
      if (str_contains($pLower, 'карт')) $paymentTags[] = 'card';
      if (str_contains($pLower, 'ерип')) $paymentTags[] = 'erip';
      if (str_contains($pLower, 'безналич') || str_contains($pLower, 'юр. лиц')) $paymentTags[] = 'invoice';
  }
  $paymentTagsStr = implode(',', $paymentTags);

  // Additional listing attributes
  $cardBalcony = 0;
  $cardFloor = '';
  $cardTotalFloors = '';
  $cardNeighborhood = '';
  $cardPurpose = '';
  $cardFloorsInHouse = '';
  $cardPlotArea = '';
  $cardParkingType = '';
  $cardLayout = '';
  $cardVideo = '';

  if ($item instanceof \App\Models\Listing) {
      $cardBalcony = $item->has_balcony ? 1 : 0;
      $cardFloor = $item->floor ?? '';
      $cardTotalFloors = $item->total_floors ?? '';
      $cardFloorsInHouse = $item->floors_in_house ?? '';
      $cardPlotArea = $item->plot_area ?? '';
      $cardParkingType = $item->parking_type ?? '';
      $cardLayout = $item->layout ?? '';
      $cardNeighborhood = is_array($item->neighborhood) ? implode(',', $item->neighborhood) : ($item->neighborhood ?? '');
      $cardPurpose = is_array($item->object_purpose) ? implode(',', $item->object_purpose) : ($item->object_purpose ?? '');
      $cardVideo = is_array($item->video_surveillance) ? implode(',', $item->video_surveillance) : ($item->video_surveillance ?? '');
  } else {
      $mockDetails = [
          1 => ['balcony' => 1, 'floor' => 20, 'total' => 25, 'neighbors' => 'магазин,парк', 'purpose' => 'командировки,отдых', 'layout' => 'Совмещённый'],
          2 => ['balcony' => 0, 'floor' => 4, 'total' => 5, 'neighbors' => 'кафе,метро', 'purpose' => 'отдых,командировки', 'layout' => 'Раздельный'],
          3 => ['balcony' => 0, 'floor' => 2, 'total' => 5, 'neighbors' => 'вокзал', 'purpose' => 'романтический', 'layout' => 'Совмещённый'],
          4 => ['balcony' => 1, 'floor' => 1, 'total' => 2, 'neighbors' => 'лес,озеро', 'purpose' => 'праздники,свадьбы,отдых', 'layout' => '2 санузла', 'floors_in_house' => 2, 'plot_area' => 10, 'parking_type' => 'free'],
          5 => ['balcony' => 1, 'floor' => 14, 'total' => 19, 'neighbors' => 'библиотека', 'purpose' => 'отдых', 'layout' => 'Совмещённый'],
          6 => ['balcony' => 1, 'floor' => 2, 'total' => 3, 'neighbors' => 'озеро,лес', 'purpose' => 'праздники,отдых', 'layout' => '2 санузла', 'floors_in_house' => 2, 'plot_area' => 25, 'parking_type' => 'free']
      ];
      $md = $mockDetails[$item->id] ?? ['balcony' => 0, 'floor' => '', 'total' => '', 'neighbors' => '', 'purpose' => '', 'layout' => '', 'floors_in_house' => '', 'plot_area' => '', 'parking_type' => ''];
      $cardBalcony = $md['balcony'];
      $cardFloor = $md['floor'];
      $cardTotalFloors = $md['total'];
      $cardNeighborhood = $md['neighbors'];
      $cardPurpose = $md['purpose'];
      $cardFloorsInHouse = $md['floors_in_house'] ?? '';
      $cardPlotArea = $md['plot_area'] ?? '';
      $cardParkingType = $md['parking_type'] ?? '';
      $cardLayout = $md['layout'] ?? '';
  }

  // Compile exhaustive all-amenities list
  $allAms = [];
  if ($item instanceof \App\Models\Listing) {
      if (is_array($item->amenities)) {
          foreach ($item->amenities as $group => $list) {
              if (is_array($list)) {
                  foreach ($list as $val) {
                      $allAms[] = mb_strtolower(trim($val));
                  }
              }
          }
      }
      if (is_array($item->territory_features)) {
          foreach ($item->territory_features as $val) {
              $allAms[] = mb_strtolower(trim($val));
          }
      }
      if (is_array($item->communications)) {
          foreach ($item->communications as $val) {
              $allAms[] = mb_strtolower(trim($val));
          }
      }
      if ($item->children_allowed) $allAms[] = 'children_allowed';
      if ($item->animals_allowed) $allAms[] = 'animals_allowed';
      if ($item->events_allowed) $allAms[] = 'events_allowed';
      if ($item->has_balcony) $allAms[] = 'has_balcony';
  } else {
      $mockTags = isset($specs['tags']) ? explode(',', $specs['tags']) : [];
      foreach ($mockTags as $tag) {
          if ($tag === 'wifi') $allAms[] = 'интернет wi-fi';
          if ($tag === 'bath') $allAms[] = 'русская баня';
          if ($tag === 'pool') $allAms[] = 'бассейн';
          if ($tag === 'fireplace') $allAms[] = 'камин';
          if ($tag === 'conditioner') $allAms[] = 'кондиционер';
          if ($tag === 'washing') $allAms[] = 'стиральная машина';
          if ($tag === 'tv') $allAms[] = 'телевизор / smart tv';
          if ($tag === 'parking') $allAms[] = 'парковка';
          if ($tag === 'kitchen') $allAms[] = 'собственная кухня';
          if ($tag === 'grill') $allAms[] = 'мангал / зона барбекю';
          if ($tag === 'instant') $allAms[] = 'мгновенное бронирование';
          if ($tag === 'self') $allAms[] = 'бесконтактное заселение';
          if ($tag === 'pets') $allAms[] = 'animals_allowed';
          if ($tag === 'events') $allAms[] = 'events_allowed';
          if ($tag === 'smoking') $allAms[] = 'разрешено курить';
      }
      $allAms[] = 'children_allowed';
      if ($cardBalcony) $allAms[] = 'has_balcony';
  }
  // Дополнительное сканирование текста на джакузи и детскую кроватку
  $descText = mb_strtolower(isset($item->description) ? $item->description : '');
  $titleText = mb_strtolower(isset($item->title) ? $item->title : '');
  if (str_contains($descText, 'джакузи') || str_contains($titleText, 'джакузи')) {
      $allAms[] = 'джакузи';
  }
  if (str_contains($descText, 'кроватк') || str_contains($titleText, 'кроватк')) {
      $allAms[] = 'детская кроватка';
  }

  $allAmsStr = implode(',', array_unique($allAms));

  // Сбор типов кроватей
  $bedTypes = [];
  if ($item instanceof \App\Models\Listing) {
      $bedsList = $item->beds;
      if (is_array($bedsList)) {
          foreach ($bedsList as $bedInfo) {
              if (isset($bedInfo['count']) && $bedInfo['count'] > 0) {
                  $bedTypes[] = mb_strtolower(trim($bedInfo['name']));
              }
          }
      }
  } else {
      $bedTypes[] = 'двуспальная кровать';
      if ($item->id == 4 || $item->id == 6) {
          $bedTypes[] = 'односпальная кровать';
      }
  }
  $bedTypesStr = implode(',', array_unique($bedTypes));

  if ($item instanceof \App\Models\Listing) {
      $slug = $item->slug;
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
      $slug = \Illuminate\Support\Str::slug($transliterated);
  }

  $displayAddress = $item->address;
  if (!empty($city) && str_starts_with($displayAddress, $city . ', ')) {
      $displayAddress = substr($displayAddress, strlen($city . ', '));
  }
@endphp

<div class="card {{ (($item->featured ?? false) || $item->id == 1) ? 'highlighted' : '' }}" onclick="openDetails({{ $item->id }}, '{{ $slug }}')" 
     style="{{ isset($visibleOnLoad) && !$visibleOnLoad ? 'display: none !important;' : '' }}"
     data-id="{{ $item->id }}" 
     data-slug="{{ $slug }}" 
     data-category="{{ $cat }}" 
     data-type="{{ $item->type }}" 
     data-title="{{ mb_strtolower($item->title ?? '') }}" 
     data-price="{{ $item->price }}" 
     data-guests="{{ $item->guests ?? $beds }}" 
     data-bedrooms="{{ $bedrooms }}" 
     data-beds="{{ $beds }}" 
     data-bathrooms="{{ $bathrooms }}" 
     data-city="{{ $city }}" 
     data-lat="{{ $item->latitude }}"
     data-lng="{{ $item->longitude }}"
     data-amenities="{{ $allAmsStr }}"
     data-payments="{{ $paymentTagsStr }}"
     data-banquet="{{ $item->banquet_hall_capacity ?? 0 }}"
     data-balcony="{{ $cardBalcony }}"
     data-balcony-type="{{ $item->balcony_type ?? 'none' }}"
     data-floor="{{ $cardFloor }}"
     data-total-floors="{{ $cardTotalFloors }}"
     data-floors-in-house="{{ $cardFloorsInHouse }}"
     data-plot-area="{{ $cardPlotArea }}"
     data-parking-type="{{ $cardParkingType }}"
     data-layout="{{ $cardLayout }}"
     data-video="{{ $cardVideo }}"
     data-neighborhood="{{ $cardNeighborhood }}"
     data-purpose="{{ $cardPurpose }}"
     data-bed-types="{{ $bedTypesStr }}">

  <div class="card-img" style="background: none;">
    <div class="card-carousel" data-current-index="0">
      <div class="card-carousel-track">
        @foreach($cardPhotos as $index => $photo)
          <div class="card-carousel-slide">
            @if(is_array($photo) && isset($photo['bg']))
              <div class="card-photo-fake" style="background: {{ $photo['bg'] }};">
                {{ $photo['emoji'] }}
              </div>
            @elseif(is_string($photo) && !empty($photo))
              @if(isset($isLcp) && $isLcp && $index === 0)
                <img class="card-photo" src="{{ $photo }}" alt="Снять посуточно: {{ $item->title }}, {{ $city }}" fetchpriority="high" loading="eager">
              @else
                <img class="card-photo" src="{{ $photo }}" alt="Снять посуточно: {{ $item->title }}, {{ $city }}" loading="lazy">
              @endif
            @else
              <div class="card-photo-fake" style="background: linear-gradient(140deg,#2C3E50,#3D5A6B);">
                🏢
              </div>
            @endif
          </div>
        @endforeach
      </div>
      @if(count($cardPhotos) > 1)
        <button class="carousel-arrow prev" onclick="prevCardPhoto(event, this)" aria-label="Предыдущее фото">
          <x-solar-alt-arrow-left-linear />
        </button>
        <button class="carousel-arrow next" onclick="nextCardPhoto(event, this)" aria-label="Следующее фото">
          <x-solar-alt-arrow-right-linear />
        </button>
        <div class="carousel-dots">
          @foreach($cardPhotos as $index => $photo)
            <span class="carousel-dot {{ $index === 0 ? 'active' : '' }}" onclick="setCardPhoto(event, this, {{ $index }})" role="button" aria-label="Показать фото {{ $index + 1 }}"></span>
          @endforeach
        </div>
      @endif
    </div>

    <button class="fav-btn" onclick="toggleFav(this)" aria-label="Добавить квартиру в избранное">
      <x-solar-heart-linear />
    </button>
  </div>
  <div class="card-body">
    <div class="cb-top">
      <div class="cb-title">{{ $item->title }}</div>
    </div>
    
    <div class="card-address-row">
      <x-solar-home-2-linear class="address-icon" />
      <span>{{ $displayAddress }}</span>
    </div>

    @if(!empty($item->nearest_metro) && $item->nearest_metro !== 'Нет' && $item->nearest_metro !== 'Метро не указано')
    <div class="card-metro-row">
      <svg class="metro-icon" viewBox="0 0 24 24"><path d="M2 4h4l6 10 6-10h4v16h-3V8l-7 11-7-11v12H2z" fill="var(--amber-dark)"/></svg>
      <span>{{ $item->nearest_metro }}</span>
    </div>
    @else
    <div class="card-metro-row" style="color: #4A5568;">
      <svg class="metro-icon" viewBox="0 0 24 24"><path d="M2 4h4l6 10 6-10h4v16h-3V8l-7 11-7-11v12H2z" fill="#4A5568"/></svg>
      <span>Метро не указано</span>
    </div>
    @endif

    <div class="card-price-box">
      <div class="price-section left">
        <div class="price-label">
          <span class="price-prefix">от</span>
          <span class="price-amount price-val" data-base-price="{{ $item->price }}">{{ $item->price }} Б̶</span>
        </div>
        <div class="price-period">за сутки <span class="info-icon" title="Стоимость зависит от дней недели и количества гостей">ⓘ</span></div>
      </div>
      <div class="price-divider"></div>
      <div class="price-section right">
        @if($item->weekend_price > 0)
          <div class="price-label">
            <span class="price-amount price-val" data-base-price="{{ $item->weekend_price }}">{{ $item->weekend_price }} Б̶</span>
          </div>
          <div class="price-period">за часы <span class="info-icon" title="Минимальный заказ от 2 часов">ⓘ</span></div>
        @else
          <div class="price-label">
            <span class="price-amount no-hourly">—</span>
          </div>
          <div class="price-period">за часы <span class="info-icon" title="Сдача по часам недоступна">ⓘ</span></div>
        @endif
      </div>
    </div>

    <div class="card-badges-row">
      @if(!empty($item->latitude) && !empty($item->longitude))
        <button class="badge-pill map-pill" onclick="centerMapOnListing(event, {{ $item->latitude }}, {{ $item->longitude }}, {{ $item->id }})">
          <x-solar-map-point-linear />
          На карте
        </button>
      @endif
      <span class="badge-pill">{{ $item->area ?? 35 }} м²</span>
      <span class="badge-pill">
        {{ $bedrooms === 0 ? 'Студия' : $bedrooms . ' ' . ($bedrooms == 1 ? 'комната' : ($bedrooms <= 4 ? 'комнаты' : 'комнат')) }}
      </span>
      <span class="badge-pill">
        {{ $beds }} {{ $beds == 1 ? 'гость' : ($beds <= 4 ? 'гостя' : 'гостей') }}
      </span>
    </div>

    <div class="contact-block">
      <button class="primary-action-btn" id="phone-btn-{{ $item->id }}" onclick="toggleCardContact(event, {{ $item->id }})">
        Показать контакты
      </button>
      <div class="card-messenger-row" id="messengers-{{ $item->id }}" style="display:none;" onclick="event.stopPropagation()">
        <span class="revealed-phone">{{ $item->phone }}</span>
        <div class="messenger-icons-small">
          @if(isset($item->whatsapp) && !empty($item->whatsapp))
            @php $waLink = \App\Helpers\ContactHelper::formatWhatsappUrl($item->whatsapp); @endphp
            @if(!empty($waLink))
              <a href="{{ $waLink }}" class="wa-s" target="_blank" onclick="event.stopPropagation()" aria-label="Связаться через WhatsApp"><svg viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg></a>
            @endif
          @endif
          @if(isset($item->viber) && !empty($item->viber))
            @php $vbLink = \App\Helpers\ContactHelper::formatViberUrl($item->viber); @endphp
            @if(!empty($vbLink))
              <a href="{{ $vbLink }}" class="vb-s" target="_blank" onclick="event.stopPropagation()" aria-label="Связаться через Viber"><svg viewBox="0 0 24 24"><path d="M11.398.002C9.473.028 5.331.344 3.014 2.467 1.294 4.177.693 6.698.623 9.82c-.06 3.11-.13 8.95 5.5 10.541v2.42s-.038.97.602 1.17c.79.25 1.24-.499 1.99-1.299l1.4-1.58c3.85.32 6.8-.419 7.14-.529.78-.25 5.181-.811 5.901-6.652.74-6.031-.36-9.831-2.34-11.551l-.01-.002c-.6-.55-3-2.3-8.37-2.32 0 0-.396-.025-1.038-.016zm.067 1.697c.545-.003.88.02.88.02 4.54.01 6.711 1.38 7.221 1.84 1.67 1.429 2.528 4.856 1.9 9.892-.6 4.88-4.17 5.19-4.83 5.4-.28.09-2.88.73-6.152.52 0 0-2.439 2.941-3.199 3.701-.12.13-.26.17-.35.15-.13-.03-.17-.19-.16-.41l.02-4.019c-4.771-1.32-4.491-6.302-4.441-8.902.06-2.6.55-4.732 2-6.172 1.957-1.77 5.475-2.01 7.11-2.02zm.36 2.6a.299.299 0 0 0-.3.299.3.3 0 0 0 .3.3 5.631 5.631 0 0 1 4.03 1.59c1.09 1.06 1.621 2.48 1.641 4.34a.3.3 0 0 0 .3.3v-.009a.3.3 0 0 0 .3-.3 6.451 6.451 0 0 0-1.81-4.76c-1.19-1.16-2.692-1.76-4.462-1.76zm-3.954.69a.955.955 0 0 0-.615.12h-.012c-.41.24-.788.54-1.148.94-.27.32-.421.639-.461.949a1.24 1.24 0 0 0 .05.541l.02.01a13.722 13.722 0 0 0 1.2 2.6 15.383 15.383 0 0 0 2.32 3.171l.03.04.04.03.03.03.03.03a15.603 15.603 0 0 0 3.18 2.33c1.32.72 2.122 1.06 2.602 1.2v.01c.14.04.268.06.398.06a1.84 1.84 0 0 0 1.102-.472c.39-.35.7-.738.93-1.148v-.01c.23-.43.15-.841-.18-1.121a13.632 13.632 0 0 0-2.15-1.54c-.51-.28-1.03-.11-1.24.17l-.45.569c-.23.28-.65.24-.65.24l-.012.01c-3.12-.8-3.95-3.959-3.95-3.959s-.04-.43.25-.65l.56-.45c.27-.22.46-.74.17-1.25a13.522 13.522 0 0 0-1.54-2.15.843.843 0 0 0-.504-.3zm4.473.89a.3.3 0 0 0 .002.6 3.78 3.78 0 0 1 2.65 1.15 3.5 3.5 0 0 1 .9 2.57.3.3 0 0 0 .3.299l.01.012a.3.3 0 0 0 .3-.301c.03-1.19-.34-2.19-1.07-2.99-.73-.8-1.75-1.25-3.05-1.34a.3.3 0 0 0-.042 0zm.49 1.619a.305.305 0 0 0-.018.611c.99.05 1.47.55 1.53 1.58a.3.3 0 0 0 .3.29h.01a.3.3 0 0 0 .29-.32c-.07-1.34-.8-2.091-2.1-2.161a.305.305 0 0 0-.012 0z"/></svg></a>
            @endif
          @endif
          @if(isset($item->telegram) && !empty($item->telegram))
            @php $tgLink = \App\Helpers\ContactHelper::formatTelegramUrl($item->telegram); @endphp
            @if(!empty($tgLink))
              <a href="{{ $tgLink }}" class="tg-s" target="_blank" onclick="event.stopPropagation()" aria-label="Связаться через Telegram"><svg viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12l-6.87 4.326-2.96-.924c-.643-.204-.658-.643.136-.953l11.57-4.46c.537-.204.997.12.825.861z"/></svg></a>
            @endif
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
