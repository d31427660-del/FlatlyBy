{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @php
        $rendered = [];
        
        $addUrl = function($path, $priority = '0.8', $changefreq = 'daily', $lastmod = null) use (&$rendered) {
            $url = (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) ? $path : url($path);
            if (in_array($url, $rendered)) {
                return '';
            }
            $rendered[] = $url;
            $lastmod = $lastmod ?: now()->toAtomString();
            return "    <url>\n" .
                   "        <loc>" . htmlspecialchars($url) . "</loc>\n" .
                   "        <lastmod>" . $lastmod . "</lastmod>\n" .
                   "        <changefreq>" . $changefreq . "</changefreq>\n" .
                   "        <priority>" . $priority . "</priority>\n" .
                   "    </url>\n";
        };
    @endphp

    {!! $addUrl('/', '1.0') !!}
    {!! $addUrl('/apartments', '0.9') !!}
    {!! $addUrl('/cottages', '0.9') !!}
    {!! $addUrl('/hourly', '0.9') !!}

    <!-- Дополнительные разделы по запросу -->
    {!! $addUrl('/blog', '0.8') !!}
    {!! $addUrl('/minsk/s-konditsionerom', '0.8') !!}
    {!! $addUrl('/minsk/s-basseynom', '0.8') !!}
    {!! $addUrl('/brestskaya-oblast/s-basseynom', '0.8') !!}
    {!! $addUrl('/minsk/u-vokzala', '0.8') !!}
    {!! $addUrl('/mogilev/u-vokzala', '0.8') !!}
    {!! $addUrl('/minsk/u-parka', '0.8') !!}
    {!! $addUrl('/minsk/dlya-semyi', '0.8') !!}
    {!! $addUrl('/brest/dlya-semyi', '0.8') !!}
    {!! $addUrl('/gomel/dlya-semyi', '0.8') !!}
    {!! $addUrl('/minsk/dlya-komandirovki', '0.8') !!}
    {!! $addUrl('/mogilev/dlya-komandirovki', '0.8') !!}
    {!! $addUrl('/minsk/na-chas', '0.8') !!}
    {!! $addUrl('/minsk/kvartiry-na-sutki-u-metro', '0.8') !!}
    {!! $addUrl('/grodno/dlya-kompanii', '0.8') !!}
    {!! $addUrl('/minsk/kamennaya-gorka', '0.8') !!}
    {!! $addUrl('/minsk/nemiga', '0.8') !!}
    {!! $addUrl('/minsk/zelenyy-lug', '0.8') !!}
    {!! $addUrl('/minsk/serebryanka', '0.8') !!}

    @if(isset($citySlugs) && is_array($citySlugs))
        @foreach ($citySlugs as $slug)
            <!-- Город -->
            {!! $addUrl('/' . $slug, '0.8') !!}
            
            <!-- Базовые ЧПУ фильтры -->
            @php
                $primaryTags = [
                    'kvartiry', 'kottedzhi', 'na-chasy', 
                    'odnokomnatnye', 'dvuhkomnatnye', 'trehkomnatnye', 'chetyrehkomnatnye',
                    'v-centre', 'metro', 'dzhakuzi'
                ];
                $intersections = [
                    ['rooms' => 'odnokomnatnye', 'filters' => ['na-chasy', 'v-centre', 'metro', 'dzhakuzi']],
                    ['rooms' => 'dvuhkomnatnye', 'filters' => ['na-chasy', 'v-centre', 'metro', 'dzhakuzi']],
                    ['rooms' => 'trehkomnatnye', 'filters' => ['na-chasy', 'v-centre', 'metro', 'dzhakuzi']],
                    ['rooms' => 'chetyrehkomnatnye', 'filters' => ['na-chasy', 'v-centre', 'metro', 'dzhakuzi']]
                ];
            @endphp

            @foreach($primaryTags as $tag)
                @if(isset($validTagsByCity[$slug][$tag]))
                    {!! $addUrl('/' . $slug . '/' . $tag, '0.8') !!}
                @endif
            @endforeach

            <!-- Низкочастотные ЧПУ фильтры -->
            @php
                $seoPagesConfig = config('seo_landing_pages');
                $customTags = isset($seoPagesConfig[$slug]) ? array_keys($seoPagesConfig[$slug]) : [];
            @endphp
            @foreach($customTags as $cTag)
                @if(!in_array($cTag, $primaryTags))
                    @php
                        // Custom tags might map to boolean fields or custom filters
                        $isValid = false;
                        if (isset($validTagsByCity[$slug][$cTag])) {
                            $isValid = true;
                        } else {
                            // Check if the custom tag filter_type is matched in listings
                            $conf = $seoPagesConfig[$slug][$cTag] ?? [];
                            $filterType = $conf['filter_type'] ?? null;
                            if ($filterType === 'conditioner') {
                                $isValid = $listings->where('city', \App\Helpers\CityHelper::getCityNameBySlug($slug))->filter(function($l) {
                                    $amenitiesJson = json_encode($l->amenities, JSON_UNESCAPED_UNICODE) ?: '';
                                    $titleStr = is_string($l->title) ? $l->title : '';
                                    return str_contains(mb_strtolower($amenitiesJson), 'кондиционер') || str_contains(mb_strtolower($titleStr), 'кондиционер');
                                })->isNotEmpty();
                            } elseif ($filterType === 'parking') {
                                $isValid = $listings->where('city', \App\Helpers\CityHelper::getCityNameBySlug($slug))->filter(function($l) {
                                    $amenitiesJson = json_encode($l->amenities, JSON_UNESCAPED_UNICODE) ?: '';
                                    $titleStr = is_string($l->title) ? $l->title : '';
                                    $desc = is_string($l->description) ? $l->description : '';
                                    return str_contains(mb_strtolower($amenitiesJson), 'парковка') || str_contains(mb_strtolower($titleStr), 'парковка') || str_contains(mb_strtolower($desc), 'парковка');
                                })->isNotEmpty();
                            } elseif ($filterType === 'company') {
                                $isValid = $listings->where('city', \App\Helpers\CityHelper::getCityNameBySlug($slug))->where('sleeping_places', '>=', 6)->isNotEmpty();
                            } elseif ($filterType === 'pets') {
                                $isValid = $listings->where('city', \App\Helpers\CityHelper::getCityNameBySlug($slug))->where('animals_allowed', true)->isNotEmpty();
                            } elseif ($filterType === 'price_100') {
                                $isValid = $listings->where('city', \App\Helpers\CityHelper::getCityNameBySlug($slug))->where('price', '<=', 100)->isNotEmpty();
                            }
                        }
                    @endphp
                    @if($isValid)
                        {!! $addUrl('/' . $slug . '/' . $cTag, '0.85') !!}
                    @endif
                @endif
            @endforeach

            @foreach($intersections as $item)
                @foreach($item['filters'] as $filter)
                    @if(isset($validIntersectionsByCity[$slug][$item['rooms'] . '/' . $filter]))
                        {!! $addUrl('/' . $slug . '/' . $item['rooms'] . '/' . $filter, '0.7') !!}
                    @endif
                @endforeach
            @endforeach
        @endforeach
    @endif

    @foreach ($listings as $listing)
        {!! $addUrl(route('listings.show', ['id' => $listing->id, 'slug' => $listing->slug]), '0.7', 'weekly', $listing->updated_at ? $listing->updated_at->toAtomString() : null) !!}
    @endforeach

    @if(isset($blogPosts))
        @foreach ($blogPosts as $post)
            {!! $addUrl(route('blog.show', ['slug' => $post->slug]), '0.6', 'weekly', $post->updated_at ? $post->updated_at->toAtomString() : null) !!}
        @endforeach
    @endif
</urlset>
