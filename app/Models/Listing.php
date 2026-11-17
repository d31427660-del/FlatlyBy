<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Listing extends Model
{
    use SoftDeletes; // Поддержка soft-deletes (колонка deleted_at в вашей БД)

    protected $table = 'listings'; // Ваша существующая таблица в MySQL

    /**
     * Поля, разрешенные для массового заполнения (при подаче объявления через форму)
     */
    protected $fillable = [
        'user_id', 'type', 'title', 'description', 'price', 'weekend_price',
        'weekend_price_negotiable', 'holiday_price', 'holiday_price_negotiable',
        'city', 'district', 'address', 'rooms', 'area', 'total_area',
        'sleeping_places', 'children_allowed', 'animals_allowed', 'events_allowed',
        'check_in_time', 'check_out_time', 'minimum_rent_days', 'deposit',
        'apartment_type', 'total_floors', 'layout', 'renovation',
        'building_material', 'has_balcony', 'balcony_type', 'balcony_glazed',
        'cottage_type', 'plot_area', 'floors_in_house', 'cottage_material',
        'bathrooms_count', 'amenities', 'communications', 'territory_features',
        'unp', 'phone', 'telegram', 'whatsapp', 'viber', 'nearest_metro', 'floor',
        'latitude', 'longitude', 'status', 'image', 'moderation_reason', 'featured', 'views_count', 'contacts_count', 'impressions_count',
        'photos', 'beds_list', 'banquet_hall_capacity', 'video_surveillance', 'neighborhood', 'object_purpose', 'parking_type'
    ];

    protected $appends = ['beds', 'guests', 'chars', 'rules', 'payments', 'filter_tags', 'is_paid', 'expires_at', 'featured_expires_at', 'tariff_price', 'slug', 'has_homepage_tariff', 'homepage_expires_at', 'host_name'];

    /**
     * Автоматическое приведение JSON-колонок к массивам PHP
     */
    protected $casts = [
        'amenities' => 'array',
        'communications' => 'array',
        'territory_features' => 'array',
        'weekend_price_negotiable' => 'boolean',
        'holiday_price_negotiable' => 'boolean',
        'children_allowed' => 'boolean',
        'animals_allowed' => 'boolean',
        'events_allowed' => 'boolean',
        'has_balcony' => 'boolean',
        'balcony_glazed' => 'boolean',
        'featured' => 'boolean',
        'latitude' => 'double',
        'longitude' => 'double',
        'photos' => 'array',
        'beds_list' => 'array',
        'video_surveillance' => 'array',
        'neighborhood' => 'array',
        'object_purpose' => 'array',
    ];

    /**
     * Связь с хозяином объявления (таблица users)
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'listing_id')->orderBy('created_at', 'desc');
    }

    /**
     * Динамическое вычисление спальных мест для фронтенда
     */
    public function getBedsAttribute(): array
    {
        if (!empty($this->beds_list) && is_array($this->beds_list)) {
            return $this->beds_list;
        }

        $places = $this->sleeping_places ?: 2;
        if ($places <= 2) {
            return [['name' => 'Двуспальная кровать', 'count' => 1, 'icon' => '🛏️']];
        }
        $doubleBeds = floor($places / 2);
        $singleBeds = $places % 2;
        $beds = [];
        if ($doubleBeds > 0) {
            $beds[] = ['name' => 'Двуспальная кровать', 'count' => (int)$doubleBeds, 'icon' => '🛏️'];
        }
        if ($singleBeds > 0) {
            $beds[] = ['name' => 'Односпальная кровать', 'count' => (int)$singleBeds, 'icon' => '🛏️'];
        }
        return $beds;
    }

    /**
     * Динамическое вычисление количества гостей
     */
    public function getGuestsAttribute(): int
    {
        return (int)($this->sleeping_places ?: 2);
    }

    /**
     * Динамическое конструирование характеристик для фронтенда
     */
    public function getCharsAttribute(): array
    {
        $layout = $this->layout;
        $bathroom = 'Совмещённый';
        if ($layout === 'separate' || $layout === 'Раздельный') {
            $bathroom = 'Раздельный';
        } elseif ($layout === 'two' || $layout === '2 санузла') {
            $bathroom = '2 санузла';
        } elseif ($layout === 'ordinary' || $layout === 'combined' || $layout === 'Совмещённый') {
            $bathroom = 'Совмещённый';
        } elseif (!empty($layout)) {
            $bathroom = $layout;
        } else {
            if ($this->bathrooms_count > 1) {
                $bathroom = 'Раздельный (' . $this->bathrooms_count . ' шт.)';
            }
        }
        
        $balcony = 'Нет балкона';
        if ($this->has_balcony) {
            $balcony = 'Есть балкон';
            if ($this->balcony_type === 'loggia') {
                $balcony = 'Есть лоджия';
            } elseif ($this->balcony_type === 'terrace') {
                $balcony = 'Есть терраса';
            }
        }
        
        $territory = is_array($this->territory_features) ? $this->territory_features : [];
        if (empty($territory)) {
            $territory = ['Домофон', 'Видеонаблюдение'];
        }
        
        return [
            'bathroom' => $bathroom,
            'balcony' => $balcony,
            'parking' => 'Бесплатная во дворе',
            'view' => 'Во двор',
            'territory' => $territory
        ];
    }

    /**
     * Динамическое конструирование правил размещения для фронтенда
     */
    public function getRulesAttribute(): array
    {
        $checkingRules = is_array($this->communications) ? $this->communications : [];
        
        $isSmokingAllowed = false;
        $checkingCleaned = [];
        foreach ($checkingRules as $rule) {
            if ($rule === 'Разрешено курить') {
                $isSmokingAllowed = true;
            } else {
                $checkingCleaned[] = ['text' => $rule, 'ok' => true];
            }
        }
        
        $hosting = [];
        if ($this->children_allowed) {
            $hosting[] = ['text' => 'Можно с детьми', 'ok' => true];
        } else {
            $hosting[] = ['text' => 'Нельзя с детьми', 'ok' => false];
        }
        
        if ($isSmokingAllowed) {
            $hosting[] = ['text' => 'Разрешено курить', 'ok' => true];
        } else {
            $hosting[] = ['text' => 'Курить запрещено', 'ok' => false];
        }
        
        if ($this->events_allowed) {
            $hosting[] = ['text' => 'Разрешены мероприятия', 'ok' => true];
        } else {
            $hosting[] = ['text' => 'Мероприятия запрещены', 'ok' => false];
        }
        
        if ($this->animals_allowed) {
            $hosting[] = ['text' => 'Можно с питомцами', 'ok' => true];
        } else {
            $hosting[] = ['text' => 'С животными нельзя', 'ok' => false];
        }
        
        if (empty($checkingCleaned)) {
            $checkingCleaned = [
                ['text' => 'Бесконтактное заселение', 'ok' => true],
                ['text' => 'Отчётные документы', 'ok' => true]
            ];
        }
        
        return [
            'checkin' => 'с ' . ($this->check_in_time ?: '14:00'),
            'checkout' => 'до ' . ($this->check_out_time ?: '12:00'),
            'hosting' => $hosting,
            'checking' => $checkingCleaned
        ];
    }

    /**
     * Динамическое конструирование способов оплаты для фронтенда
     */
    public function getPaymentsAttribute(): array
    {
        $ren = $this->renovation;
        if (is_string($ren)) {
            $decoded = json_decode($ren, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        } elseif (is_array($ren)) {
            return $ren;
        }
        return [];
    }

    /**
     * Динамические теги для фильтрации на главной странице
     */
    public function getFilterTagsAttribute(): string
    {
        $tags = [];
        
        $basic = $this->amenities['Основные удобства'] ?? [];
        $kitchen = $this->amenities['Кухонная зона'] ?? [];
        $bathroom = $this->amenities['Ванная комната'] ?? [];
        $allAmenities = array_merge(
            is_array($basic) ? $basic : [],
            is_array($kitchen) ? $kitchen : [],
            is_array($bathroom) ? $bathroom : []
        );
        $territory = is_array($this->territory_features) ? $this->territory_features : [];
        $checking = is_array($this->communications) ? $this->communications : [];

        // Filter out non-strings to prevent TypeErrors in mb_strtolower
        $allAmenities = array_filter($allAmenities, 'is_string');
        $territory = array_filter($territory, 'is_string');
        $checking = array_filter($checking, 'is_string');
        
        foreach ($allAmenities as $a) {
            $aLower = mb_strtolower($a);
            if (str_contains($aLower, 'wi-fi') || str_contains($aLower, 'интернет') || str_contains($aLower, 'wifi')) {
                $tags[] = 'wifi';
                break;
            }
        }
        
        $hasBath = false;
        foreach (array_merge($allAmenities, $territory) as $item) {
            $itemLower = mb_strtolower($item);
            if (str_contains($itemLower, 'баня') || str_contains($itemLower, 'сауна')) {
                $hasBath = true;
                break;
            }
        }
        if ($hasBath) $tags[] = 'bath';
        
        $hasPool = false;
        foreach (array_merge($allAmenities, $territory) as $item) {
            $itemLower = mb_strtolower($item);
            if (str_contains($itemLower, 'бассейн') || str_contains($itemLower, 'купель') || str_contains($itemLower, 'бассеин')) {
                $hasPool = true;
                break;
            }
        }
        if ($hasPool) $tags[] = 'pool';
        
        foreach ($allAmenities as $a) {
            $aLower = mb_strtolower($a);
            if (str_contains($aLower, 'камин')) {
                $tags[] = 'fireplace';
                break;
            }
        }
        
        foreach ($allAmenities as $a) {
            $aLower = mb_strtolower($a);
            if (str_contains($aLower, 'кондиционер')) {
                $tags[] = 'conditioner';
                break;
            }
        }
        
        foreach ($allAmenities as $a) {
            $aLower = mb_strtolower($a);
            if (str_contains($aLower, 'стиральн')) {
                $tags[] = 'washing';
                break;
            }
        }
        
        foreach ($allAmenities as $a) {
            $aLower = mb_strtolower($a);
            if (str_contains($aLower, 'телевизор') || str_contains($aLower, 'tv') || str_contains($aLower, 'тв')) {
                $tags[] = 'tv';
                break;
            }
        }
        
        $hasParking = false;
        foreach (array_merge($allAmenities, $territory) as $item) {
            $itemLower = mb_strtolower($item);
            if (str_contains($itemLower, 'парковка') || str_contains($itemLower, 'гараж') || str_contains($itemLower, 'стоянка') || str_contains($itemLower, 'машиноместо')) {
                $hasParking = true;
                break;
            }
        }
        if ($hasParking) $tags[] = 'parking';
        
        $hasKitchen = false;
        foreach ($allAmenities as $a) {
            $aLower = mb_strtolower($a);
            if (str_contains($aLower, 'кухня') || str_contains($aLower, 'кухонн') || str_contains($aLower, 'плита') || str_contains($aLower, 'холодильник')) {
                $hasKitchen = true;
                break;
            }
        }
        if ($hasKitchen) $tags[] = 'kitchen';
        
        $hasGrill = false;
        foreach (array_merge($allAmenities, $territory) as $item) {
            $itemLower = mb_strtolower($item);
            if (str_contains($itemLower, 'мангал') || str_contains($itemLower, 'гриль') || str_contains($itemLower, 'барбекю') || str_contains($itemLower, 'беседка')) {
                $hasGrill = true;
                break;
            }
        }
        if ($hasGrill) $tags[] = 'grill';
        
        foreach ($checking as $c) {
            $cLower = mb_strtolower($c);
            if (str_contains($cLower, 'мгновенн') || str_contains($cLower, 'моментальн')) {
                $tags[] = 'instant';
                break;
            }
        }
        
        foreach ($checking as $c) {
            $cLower = mb_strtolower($c);
            if (str_contains($cLower, 'бесконтактн') || str_contains($cLower, 'самостоятельн')) {
                $tags[] = 'self';
                break;
            }
        }
        
        if ($this->animals_allowed) {
            $tags[] = 'pets';
        }
        
        if ($this->events_allowed) {
            $tags[] = 'events';
        }
        
        foreach ($checking as $c) {
            if ($c === 'Разрешено курить') {
                $tags[] = 'smoking';
                break;
            }
        }
        
        return implode(',', $tags);
    }

    public function userTariffs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserTariff::class, 'listing_id');
    }

    public function currentTariff(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserTariff::class, 'listing_id')
            ->where('status', 'active')
            ->whereIn('tariff_id', [1, 2])
            ->where('expires_at', '>', now())
            ->latest();
    }

    public function currentPromotion(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserTariff::class, 'listing_id')
            ->where('status', 'active')
            ->where('tariff_id', 3)
            ->where('expires_at', '>', now())
            ->latest();
    }

    public function currentHomepageTariff(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserTariff::class, 'listing_id')
            ->where('status', 'active')
            ->where('tariff_id', 4)
            ->where('expires_at', '>', now())
            ->latest();
    }

    public function getHasHomepageTariffAttribute(): bool
    {
        try {
            if ($this->relationLoaded('userTariffs')) {
                $now = now();
                return $this->userTariffs
                    ->where('status', 'active')
                    ->where('tariff_id', 4)
                    ->filter(fn($ut) => $ut->expires_at && $ut->expires_at > $now)
                    ->isNotEmpty();
            }
            if (!\Illuminate\Support\Facades\Schema::hasTable('user_tariffs')) {
                return false;
            }
            return $this->currentHomepageTariff()->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getHomepageExpiresAtAttribute()
    {
        try {
            if ($this->relationLoaded('userTariffs')) {
                $now = now();
                $tariff = $this->userTariffs
                    ->where('status', 'active')
                    ->where('tariff_id', 4)
                    ->filter(fn($ut) => $ut->expires_at && $ut->expires_at > $now)
                    ->sortByDesc('id')
                    ->first();
                return $tariff && $tariff->expires_at ? (\Illuminate\Support\Carbon::parse($tariff->expires_at))->format('d.m.Y') : null;
            }
            if (!\Illuminate\Support\Facades\Schema::hasTable('user_tariffs')) {
                return null;
            }
            $tariff = $this->currentHomepageTariff()->first();
            return $tariff && $tariff->expires_at ? $tariff->expires_at->format('d.m.Y') : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getIsPaidAttribute(): bool
    {
        try {
            if ($this->relationLoaded('userTariffs')) {
                $now = now();
                $tariff = $this->userTariffs
                    ->where('status', 'active')
                    ->whereIn('tariff_id', [1, 2])
                    ->filter(fn($ut) => $ut->expires_at && $ut->expires_at > $now)
                    ->sortByDesc('id')
                    ->first();
                if (!$tariff) {
                    return false;
                }
                $expectedType = in_array($this->type, ['cottage', 'agrotourism']) ? 'cottage' : 'apartment';
                return $tariff->tariff ? $tariff->tariff->property_type === $expectedType : true;
            }
            if (!\Illuminate\Support\Facades\Schema::hasTable('user_tariffs')) {
                return false;
            }
            $tariff = $this->currentTariff()->first();
            if (!$tariff) {
                return false;
            }
            $expectedType = in_array($this->type, ['cottage', 'agrotourism']) ? 'cottage' : 'apartment';
            return $tariff->tariff ? $tariff->tariff->property_type === $expectedType : true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getExpiresAtAttribute()
    {
        try {
            if ($this->relationLoaded('userTariffs')) {
                $now = now();
                $tariff = $this->userTariffs
                    ->where('status', 'active')
                    ->whereIn('tariff_id', [1, 2])
                    ->filter(fn($ut) => $ut->expires_at && $ut->expires_at > $now)
                    ->sortByDesc('id')
                    ->first();
                return $tariff && $tariff->expires_at ? (\Illuminate\Support\Carbon::parse($tariff->expires_at))->format('d.m.Y') : null;
            }
            if (!\Illuminate\Support\Facades\Schema::hasTable('user_tariffs')) {
                return null;
            }
            $tariff = $this->currentTariff()->first();
            return $tariff && $tariff->expires_at ? $tariff->expires_at->format('d.m.Y') : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFeaturedExpiresAtAttribute()
    {
        try {
            if ($this->relationLoaded('userTariffs')) {
                $now = now();
                $promo = $this->userTariffs
                    ->where('status', 'active')
                    ->where('tariff_id', 3)
                    ->filter(fn($ut) => $ut->expires_at && $ut->expires_at > $now)
                    ->sortByDesc('id')
                    ->first();
                return $promo && $promo->expires_at ? (\Illuminate\Support\Carbon::parse($promo->expires_at))->format('d.m.Y') : null;
            }
            if (!\Illuminate\Support\Facades\Schema::hasTable('user_tariffs')) {
                return null;
            }
            $promo = $this->currentPromotion()->first();
            return $promo && $promo->expires_at ? $promo->expires_at->format('d.m.Y') : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getTariffPriceAttribute(): int
    {
        return in_array($this->type, ['cottage', 'agrotourism']) ? 30 : 10;
    }

    public function getSlugAttribute(): string
    {
        $rus = [
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
            'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'
        ];
        $lat = [
            'A', 'B', 'V', 'G', 'D', 'E', 'E', 'Zh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'Ts', 'Ch', 'Sh', 'Sch', '', 'Y', '', 'E', 'Yu', 'Ya',
            'a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ya'
        ];
        $transliterated = str_replace($rus, $lat, $this->title ?? '');
        return \Illuminate\Support\Str::slug($transliterated);
    }

    /**
     * Динамическое получение имени владельца объявления
     */
    public function getHostNameAttribute(): string
    {
        if ($this->host && !empty($this->host->name)) {
            return $this->host->name;
        }
        return 'Частный владелец';
    }

    /**
     * Телефон владельца (приоритет — актуальные данные хозяина из связи host)
     */
    public function getPhoneAttribute($value): string
    {
        if ($this->host && !empty($this->host->phone)) {
            return $this->host->phone;
        }
        return $value ?? '';
    }

    /**
     * Telegram владельца (приоритет — актуальные данные хозяина из связи host)
     */
    public function getTelegramAttribute($value): string
    {
        if ($this->host && !empty($this->host->telegram)) {
            return $this->host->telegram;
        }
        return $value ?? '';
    }

    /**
     * Viber владельца (приоритет — актуальные данные хозяина из связи host)
     */
    public function getViberAttribute($value): string
    {
        if ($this->host && !empty($this->host->viber)) {
            return $this->host->viber;
        }
        return $value ?? '';
    }

    /**
     * WhatsApp владельца (приоритет — актуальные данные хозяина из связи host)
     */
    public function getWhatsappAttribute($value): string
    {
        if ($this->host && !empty($this->host->whatsapp)) {
            return $this->host->whatsapp;
        }
        return $value ?? '';
    }

    public function getTelegramUrlAttribute(): string
    {
        return \App\Helpers\ContactHelper::formatTelegramUrl($this->telegram);
    }

    public function getWhatsappUrlAttribute(): string
    {
        return \App\Helpers\ContactHelper::formatWhatsappUrl($this->whatsapp);
    }

    public function getViberUrlAttribute(): string
    {
        return \App\Helpers\ContactHelper::formatViberUrl($this->viber);
    }

    /**
     * УНП владельца (приоритет — актуальные данные хозяина из связи host)
     */
    public function getUnpAttribute($value): string
    {
        if ($this->host && !empty($this->host->unp)) {
            return $this->host->unp;
        }
        return $value ?? '';
    }

    /**
     * Проверка, принадлежит ли объявление агентству
     */
    public function isAgencyListing(): bool
    {
        $phone = $this->phone;
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        return in_array($cleanPhone, ['375293197117', '375447076448']) 
            || str_contains($phone, '3197117') 
            || str_contains($phone, '7076448');
    }
}
