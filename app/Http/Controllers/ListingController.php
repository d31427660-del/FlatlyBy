<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Listing;
use Illuminate\Support\Facades\DB;

class ListingController extends Controller
{
    /**
     * Отображение главной страницы со списком объявлений и интерактивной картой
     */
    public function index(Request $request)
    {
        $this->ensureLoggedUser();
        $this->expireFeaturedListings();

        // 301 Редиректы со старых GET-параметров (?city=..., ?type=...) на новые ЧПУ-адреса для SEO
        $city = $request->query('city');
        $type = $request->query('type');

        if ($city !== null || $type !== null) {
            $citySlug = null;
            if ($city) {
                $city = trim($city);
                $citySlug = \App\Helpers\CityHelper::slugify($city);
                // Проверяем, существует ли город в нашей системе
                if (!\App\Helpers\CityHelper::getCityNameBySlug($citySlug)) {
                    $citySlug = null;
                }
            }

            // Маппинг типов жилья
            $typeSlug = null;
            if ($type) {
                $type = strtolower(trim($type));
                if (in_array($type, ['apartment', 'apartments'])) {
                    $typeSlug = 'apartments';
                } elseif (in_array($type, ['cottage', 'cottages', 'agrotourism'])) {
                    $typeSlug = 'cottages';
                } elseif (in_array($type, ['hourly', 'hour'])) {
                    $typeSlug = 'hourly';
                }
            }

            // Формируем редирект
            $queryParams = $request->query();
            unset($queryParams['city'], $queryParams['type']);

            if ($citySlug && $typeSlug) {
                return redirect()->route('listings.category', array_merge(['city_slug' => $citySlug, 'filter_slug' => $typeSlug], $queryParams), 301);
            } elseif ($citySlug) {
                return redirect()->route('listings.city', array_merge(['city_slug' => $citySlug], $queryParams), 301);
            } elseif ($typeSlug) {
                return redirect()->route('listings.' . $typeSlug, $queryParams, 301);
            } else {
                return redirect()->route('home', $queryParams, 301);
            }
        }

        try {
            $featuredListings = Listing::where('status', 'active')
                ->whereHas('userTariffs', function($query) {
                    $query->where('tariff_id', 4)
                          ->where('status', 'active')
                          ->where('expires_at', '>', now());
                })
                ->inRandomOrder()
                ->take(10)
                ->get();
        } catch (\Exception $e) {
            $featuredListings = collect();
        }

        return view('listings.home', compact('featuredListings'));
    }

    /**
     * Страница объявлений конкретного города
     */
    public function cityPage(Request $request, $city_slug)
    {
        $this->ensureLoggedUser();
        $this->expireFeaturedListings();

        $city = \App\Helpers\CityHelper::getCityNameBySlug($city_slug);
        if (!$city) {
            return redirect()->route('home');
        }

        try {
            $query = Listing::where('status', 'active')
                ->where('city', $city);
            $this->applyRequestFilters($query, $request);
            $listings = $query->inRandomOrder(date('YmdH'))->get();
        } catch (\Exception $e) {
            $listings = collect();
        }

        $selectedCity = $city;
        $inCity = \App\Helpers\CityHelper::getInCityName($city);
        $mapCenter = \App\Helpers\CityHelper::getCityCoordinates($city, $listings);

        return $this->renderCatalogView($request, compact('listings', 'selectedCity', 'inCity', 'mapCenter'));
    }

    /**
     * Страница категории/фильтра для конкретного города (SEO Landing Page)
     */
    public function categoryPage(Request $request, $city_slug, $filter_slug)
    {
        $this->ensureLoggedUser();
        $this->expireFeaturedListings();

        $city = \App\Helpers\CityHelper::getCityNameBySlug($city_slug);
        if (!$city) {
            return redirect()->route('home');
        }

        $categories = [
            'apartments' => [
                'title' => 'Снять квартиру посуточно :inCity без посредников | Flatly',
                'description' => 'Снять квартиру на сутки :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Квартиры на сутки :inCity',
                'filter' => function($query) {
                    $query->where('type', '!=', 'cottage')
                          ->where(function($q) {
                              $q->whereNull('weekend_price')
                                ->orWhere('weekend_price', '<=', 0);
                          });
                }
            ],
            'cottages' => [
                'title' => 'Снять коттедж или усадьбу посуточно :inCity без посредников | Flatly',
                'description' => 'Снять коттедж или загородный дом на сутки :inCity. Прямые контакты хозяев, цены без посредников.',
                'h1' => 'Коттеджи и усадьбы на сутки :inCity',
                'filter' => function($query) {
                    $query->whereIn('type', ['cottage', 'agrotourism']);
                }
            ],
            'hourly' => [
                'title' => 'Аренда квартир на часы :inCity почасово | Flatly',
                'description' => 'Снять квартиру на несколько часов или почасово :inCity. Прямые контакты хозяев, актуальные цены.',
                'h1' => 'Квартиры на часы (почасово) :inCity',
                'filter' => function($query) {
                    $query->where('type', '!=', 'cottage')
                          ->where('weekend_price', '>', 0);
                }
            ],
            'dzhakuzi' => [
                'title' => 'Снять квартиру посуточно с джакузи :inCity без посредников | Flatly',
                'description' => 'Снять квартиру на сутки с джакузи и гидромассажем :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Квартиры на сутки с джакузи :inCity',
                'filter' => function($query) {
                    $query->where('type', '!=', 'cottage')
                          ->where(function($q) {
                              $q->where('amenities', 'like', '%джакузи%')
                                ->orWhere('amenities', 'like', '%Гидромассаж%')
                                ->orWhere('title', 'like', '%джакузи%')
                                ->orWhere('description', 'like', '%джакузи%');
                          });
                }
            ],
            'jacuzzi' => [
                'title' => 'Снять квартиру посуточно с джакузи :inCity без посредников | Flatly',
                'description' => 'Снять квартиру на сутки с джакузи и гидромассажем :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Квартиры на сутки с джакузи :inCity',
                'filter' => function($query) {
                    $query->where('type', '!=', 'cottage')
                          ->where(function($q) {
                              $q->where('amenities', 'like', '%джакузи%')
                                ->orWhere('amenities', 'like', '%Гидромассаж%')
                                ->orWhere('title', 'like', '%джакузи%')
                                ->orWhere('description', 'like', '%джакузи%');
                          });
                }
            ],
            'odnokomnatnaya' => [
                'title' => 'Снять 1-комнатную квартиру посуточно :inCity без посредников | Flatly',
                'description' => 'Снять однокомнатную квартиру на сутки :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Однокомнатные квартиры на сутки :inCity',
                'filter' => function($query) {
                    $query->where('type', '!=', 'cottage')
                          ->where('rooms', 1);
                }
            ],
            '1-room' => [
                'title' => 'Снять 1-комнатную квартиру посуточно :inCity без посредников | Flatly',
                'description' => 'Снять однокомнатную квартиру на сутки :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Однокомнатные квартиры на сутки :inCity',
                'filter' => function($query) {
                    $query->where('type', '!=', 'cottage')
                          ->where('rooms', 1);
                }
            ],
            'dvuhkomnatnaya' => [
                'title' => 'Снять 2-комнатную квартиру посуточно :inCity без посредников | Flatly',
                'description' => 'Снять двухкомнатную квартиру на сутки :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Двухкомнатные квартиры на сутки :inCity',
                'filter' => function($query) {
                    $query->where('type', '!=', 'cottage')
                          ->where('rooms', 2);
                }
            ],
            '2-room' => [
                'title' => 'Снять 2-комнатную квартиру посуточно :inCity без посредников | Flatly',
                'description' => 'Снять двухкомнатную квартиру на сутки :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Двухкомнатные квартиры на сутки :inCity',
                'filter' => function($query) {
                    $query->where('type', '!=', 'cottage')
                          ->where('rooms', 2);
                }
            ],
            'trehkomnatnaya' => [
                'title' => 'Снять 3-комнатную квартиру посуточно :inCity без посредников | Flatly',
                'description' => 'Снять трехкомнатную квартиру на сутки :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Трехкомнатные квартиры на сутки :inCity',
                'filter' => function($query) {
                    $query->where('type', '!=', 'cottage')
                          ->where('rooms', 3);
                }
            ],
            '3-room' => [
                'title' => 'Снять 3-комнатную квартиру посуточно :inCity без посредников | Flatly',
                'description' => 'Снять трехкомнатную квартиру на сутки :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Трехкомнатные квартиры на сутки :inCity',
                'filter' => function($query) {
                    $query->where('type', '!=', 'cottage')
                          ->where('rooms', 3);
                }
            ],
            'chetyrehkomnatnaya' => [
                'title' => 'Снять 4-комнатную квартиру посуточно :inCity без посредников | Flatly',
                'description' => 'Снять четырехкомнатную квартиру на сутки :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Четырехкомнатные квартиры на сутки :inCity',
                'filter' => function($query) {
                    $query->where('type', '!=', 'cottage')
                          ->where('rooms', '>=', 4);
                }
            ],
            '4-room' => [
                'title' => 'Снять 4-комнатную квартиру посуточно :inCity без посредников | Flatly',
                'description' => 'Снять четырехкомнатную квартиру на сутки :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Четырехкомнатные квартиры на сутки :inCity',
                'filter' => function($query) {
                    $query->where('type', '!=', 'cottage')
                          ->where('rooms', '>=', 4);
                }
            ],
            'vokzal' => [
                'title' => 'Снять квартиру на сутки возле вокзала :inCity без посредников | Flatly',
                'description' => 'Снять квартиру посуточно рядом с ж/д вокзалом :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Квартиры на сутки возле вокзала :inCity',
                'filter' => function($query) {
                    $query->where(function($q) {
                        $q->where('address', 'like', '%вокзал%')
                          ->orWhere('description', 'like', '%вокзал%')
                          ->orWhere('title', 'like', '%вокзал%')
                          ->orWhere('address', 'like', '%ж/д%')
                          ->orWhere('description', 'like', '%ж/д%')
                          ->orWhere('address', 'like', '%жд %')
                          ->orWhere('description', 'like', '%жд %');
                    });
                }
            ],
            'near-station' => [
                'title' => 'Снять квартиру на сутки возле вокзала :inCity без посредников | Flatly',
                'description' => 'Снять квартиру посуточно рядом с ж/д вокзалом :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Квартиры на сутки возле вокзала :inCity',
                'filter' => function($query) {
                    $query->where(function($q) {
                        $q->where('address', 'like', '%вокзал%')
                          ->orWhere('description', 'like', '%вокзал%')
                          ->orWhere('title', 'like', '%вокзал%')
                          ->orWhere('address', 'like', '%ж/д%')
                          ->orWhere('description', 'like', '%ж/д%')
                          ->orWhere('address', 'like', '%жд %')
                          ->orWhere('description', 'like', '%жд %');
                    });
                }
            ],
            'metro' => [
                'title' => 'Снять квартиру на сутки возле метро :inCity без посредников | Flatly',
                'description' => 'Снять квартиру посуточно рядом со станцией метро :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Квартиры на сутки возле метро :inCity',
                'filter' => function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('nearest_metro')
                          ->where('nearest_metro', '!=', '')
                          ->where('nearest_metro', '!=', 'Нет')
                          ->where('nearest_metro', '!=', 'Метро не указано')
                          ->orWhere('address', 'like', '%метро%')
                          ->orWhere('description', 'like', '%метро%')
                          ->orWhere('title', 'like', '%метро%');
                    });
                }
            ],
            'near-metro' => [
                'title' => 'Снять квартиру на сутки возле метро :inCity без посредников | Flatly',
                'description' => 'Снять квартиру посуточно рядом со станцией метро :inCity. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.',
                'h1' => 'Квартиры на сутки возле метро :inCity',
                'filter' => function($query) {
                    $query->where(function($q) {
                        $q->whereNotNull('nearest_metro')
                          ->where('nearest_metro', '!=', '')
                          ->where('nearest_metro', '!=', 'Нет')
                          ->where('nearest_metro', '!=', 'Метро не указано')
                          ->orWhere('address', 'like', '%метро%')
                          ->orWhere('description', 'like', '%метро%')
                          ->orWhere('title', 'like', '%метро%');
                    });
                }
            ],
            's-baney' => [
                'title' => 'Снять коттедж на сутки с баней :inCity без посредников | Flatly',
                'description' => 'Снять дом, усадьбу или коттедж посуточно с русской баней или финской сауной :inCity. Прямые контакты, цены без посредников.',
                'h1' => 'Коттеджи на сутки с баней :inCity',
                'filter' => function($query) {
                    $query->whereIn('type', ['cottage', 'agrotourism'])
                          ->where(function($q) {
                              $q->where('amenities', 'like', '%баня%')
                                ->orWhere('amenities', 'like', '%сауна%')
                                ->orWhere('territory_features', 'like', '%баня%')
                                ->orWhere('territory_features', 'like', '%сауна%')
                                ->orWhere('title', 'like', '%баня%')
                                ->orWhere('title', 'like', '%сауна%')
                                ->orWhere('description', 'like', '%баня%')
                                ->orWhere('description', 'like', '%сауна%');
                          });
                }
            ],
            'with-sauna' => [
                'title' => 'Снять коттедж на сутки с баней :inCity без посредников | Flatly',
                'description' => 'Снять дом, усадьбу или коттедж посуточно с русской баней или финской сауной :inCity. Прямые контакты, цены без посредников.',
                'h1' => 'Коттеджи на сутки с баней :inCity',
                'filter' => function($query) {
                    $query->whereIn('type', ['cottage', 'agrotourism'])
                          ->where(function($q) {
                              $q->where('amenities', 'like', '%баня%')
                                ->orWhere('amenities', 'like', '%сауна%')
                                ->orWhere('territory_features', 'like', '%баня%')
                                ->orWhere('territory_features', 'like', '%сауна%')
                                ->orWhere('title', 'like', '%баня%')
                                ->orWhere('title', 'like', '%сауна%')
                                ->orWhere('description', 'like', '%баня%')
                                ->orWhere('description', 'like', '%сауна%');
                          });
                }
            ],
            's-basseynom' => [
                'title' => 'Снять коттедж на сутки с бассейном :inCity без посредников | Flatly',
                'description' => 'Снять дом, усадьбу или коттедж посуточно с бассейном или купелью :inCity. Прямые контакты хозяев, актуальные цены, без посредников.',
                'h1' => 'Коттеджи на сутки с бассейном :inCity',
                'filter' => function($query) {
                    $query->whereIn('type', ['cottage', 'agrotourism'])
                          ->where(function($q) {
                              $q->where('amenities', 'like', '%бассейн%')
                                ->orWhere('amenities', 'like', '%бассеин%')
                                ->orWhere('amenities', 'like', '%купель%')
                                ->orWhere('territory_features', 'like', '%бассейн%')
                                ->orWhere('territory_features', 'like', '%бассеин%')
                                ->orWhere('territory_features', 'like', '%купель%')
                                ->orWhere('title', 'like', '%бассейн%')
                                ->orWhere('title', 'like', '%бассеин%')
                                ->orWhere('title', 'like', '%купель%')
                                ->orWhere('description', 'like', '%бассейн%')
                                ->orWhere('description', 'like', '%бассеин%')
                                ->orWhere('description', 'like', '%купель%');
                          });
                }
            ],
            'with-pool' => [
                'title' => 'Снять коттедж на сутки с бассейном :inCity без посредников | Flatly',
                'description' => 'Снять дом, усадьбу или коттедж посуточно с бассейном или купелью :inCity. Прямые контакты хозяев, актуальные цены, без посредников.',
                'h1' => 'Коттеджи на сутки с бассейном :inCity',
                'filter' => function($query) {
                    $query->whereIn('type', ['cottage', 'agrotourism'])
                          ->where(function($q) {
                              $q->where('amenities', 'like', '%бассейн%')
                                ->orWhere('amenities', 'like', '%бассеин%')
                                ->orWhere('amenities', 'like', '%купель%')
                                ->orWhere('territory_features', 'like', '%бассейн%')
                                ->orWhere('territory_features', 'like', '%бассеин%')
                                ->orWhere('territory_features', 'like', '%купель%')
                                ->orWhere('title', 'like', '%бассейн%')
                                ->orWhere('title', 'like', '%бассеин%')
                                ->orWhere('title', 'like', '%купель%')
                                ->orWhere('description', 'like', '%бассейн%')
                                ->orWhere('description', 'like', '%бассеин%')
                                ->orWhere('description', 'like', '%купель%');
                          });
                }
            ],
        ];

        $filter_slug = strtolower(trim($filter_slug));
        if (!isset($categories[$filter_slug])) {
            return redirect()->route('listings.city', ['city_slug' => $city_slug]);
        }

        $catData = $categories[$filter_slug];
        $inCityName = \App\Helpers\CityHelper::getInCityName($city);

        // Build SEO fields
        $seoTitle = str_replace(':inCity', $inCityName, $catData['title']);
        $seoDescription = str_replace(':inCity', $inCityName, $catData['description']);
        $seoH1 = str_replace(':inCity', $inCityName, $catData['h1']);
        $seoCategory = $filter_slug;

        try {
            $query = Listing::where('status', 'active')->where('city', $city);
            $catData['filter']($query);
            $listings = $query->inRandomOrder(date('YmdH'))->get();
        } catch (\Exception $e) {
            $listings = collect();
        }

        $selectedCity = $city;
        $inCity = $inCityName;
        $mapCenter = \App\Helpers\CityHelper::getCityCoordinates($city, $listings);

        return $this->renderCatalogView($request, compact(
            'listings', 'selectedCity', 'inCity', 'mapCenter',
            'seoTitle', 'seoDescription', 'seoH1', 'seoCategory'
        ));
    }

    /**
     * Возвращает белый список разрешенных SEO-тегов и правила их фильтрации
     */
    protected function getSeoTagsMap()
    {
        return [
            // Количество комнат (Группа А: rooms)
            'odnokomnatnye'      => ['group' => 'rooms', 'field' => 'rooms', 'value' => 1, 'label' => 'однокомнатные', 'filter' => function($q) { $q->where('rooms', 1); }],
            '1-komnatnye'        => ['group' => 'rooms', 'field' => 'rooms', 'value' => 1, 'label' => 'однокомнатные', 'filter' => function($q) { $q->where('rooms', 1); }],
            'odnokomnatnaya'     => ['group' => 'rooms', 'field' => 'rooms', 'value' => 1, 'label' => 'однокомнатные', 'filter' => function($q) { $q->where('rooms', 1); }],
            '1-room'             => ['group' => 'rooms', 'field' => 'rooms', 'value' => 1, 'label' => 'однокомнатные', 'filter' => function($q) { $q->where('rooms', 1); }],

            'dvuhkomnatnye'      => ['group' => 'rooms', 'field' => 'rooms', 'value' => 2, 'label' => 'двухкомнатные', 'filter' => function($q) { $q->where('rooms', 2); }],
            '2-komnatnye'        => ['group' => 'rooms', 'field' => 'rooms', 'value' => 2, 'label' => 'двухкомнатные', 'filter' => function($q) { $q->where('rooms', 2); }],
            'dvuhkomnatnaya'     => ['group' => 'rooms', 'field' => 'rooms', 'value' => 2, 'label' => 'двухкомнатные', 'filter' => function($q) { $q->where('rooms', 2); }],
            '2-room'             => ['group' => 'rooms', 'field' => 'rooms', 'value' => 2, 'label' => 'двухкомнатные', 'filter' => function($q) { $q->where('rooms', 2); }],

            'trehkomnatnye'      => ['group' => 'rooms', 'field' => 'rooms', 'value' => 3, 'label' => 'трехкомнатные', 'filter' => function($q) { $q->where('rooms', 3); }],
            '3-komnatnye'        => ['group' => 'rooms', 'field' => 'rooms', 'value' => 3, 'label' => 'трехкомнатные', 'filter' => function($q) { $q->where('rooms', 3); }],
            'trehkomnatnaya'     => ['group' => 'rooms', 'field' => 'rooms', 'value' => 3, 'label' => 'трехкомнатные', 'filter' => function($q) { $q->where('rooms', 3); }],
            '3-room'             => ['group' => 'rooms', 'field' => 'rooms', 'value' => 3, 'label' => 'трехкомнатные', 'filter' => function($q) { $q->where('rooms', 3); }],

            'chetyrehkomnatnye'  => ['group' => 'rooms', 'field' => 'rooms', 'value' => 4, 'label' => 'четырехкомнатные', 'filter' => function($q) { $q->where('rooms', '>=', 4); }],
            '4-komnatnye'        => ['group' => 'rooms', 'field' => 'rooms', 'value' => 4, 'label' => 'четырехкомнатные', 'filter' => function($q) { $q->where('rooms', '>=', 4); }],
            'chetyrehkomnatnaya' => ['group' => 'rooms', 'field' => 'rooms', 'value' => 4, 'label' => 'четырехкомнатные', 'filter' => function($q) { $q->where('rooms', '>=', 4); }],
            '4-room'             => ['group' => 'rooms', 'field' => 'rooms', 'value' => 4, 'label' => 'четырехкомнатные', 'filter' => function($q) { $q->where('rooms', '>=', 4); }],

            // На часы / почасово (Группа B: filters)
            'na-chasy'           => ['group' => 'filters', 'field' => 'hourly', 'value' => true, 'label' => 'на часы', 'filter' => function($q) {
                $q->where('type', '!=', 'cottage')->where('weekend_price', '>', 0);
            }],
            'nachasy'            => ['group' => 'filters', 'field' => 'hourly', 'value' => true, 'label' => 'на часы', 'filter' => function($q) {
                $q->where('type', '!=', 'cottage')->where('weekend_price', '>', 0);
            }],

            // В центре (Группа B: filters)
            'v-centre'           => ['group' => 'filters', 'field' => 'center', 'value' => true, 'label' => 'в центре', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('address', 'like', '%центр%')
                        ->orWhere('description', 'like', '%центр%')
                        ->orWhere('title', 'like', '%центр%');
                });
            }],

            // Возле метро (Группа B: filters)
            'metro'              => ['group' => 'filters', 'field' => 'metro', 'value' => true, 'label' => 'возле метро', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->whereNotNull('nearest_metro')
                        ->where('nearest_metro', '!=', '')
                        ->where('nearest_metro', '!=', 'Нет')
                        ->where('nearest_metro', '!=', 'Метро не указано')
                        ->orWhere('address', 'like', '%метро%')
                        ->orWhere('description', 'like', '%метро%')
                        ->orWhere('title', 'like', '%метро%');
                });
            }],

            // С джакузи (Группа B: filters)
            'dzhakuzi'           => ['group' => 'filters', 'field' => 'jacuzzi', 'value' => true, 'label' => 'с джакузи', 'filter' => function($q) {
                $q->where('type', '!=', 'cottage')->where(function($sub) {
                    $sub->where('amenities', 'like', '%джакузи%')
                        ->orWhere('amenities', 'like', '%Гидромассаж%')
                        ->orWhere('title', 'like', '%джакузи%')
                        ->orWhere('description', 'like', '%джакузи%');
                });
            }],
            's-dzakuzi'          => ['group' => 'filters', 'field' => 'jacuzzi', 'value' => true, 'label' => 'с джакузи', 'filter' => function($q) {
                $q->where('type', '!=', 'cottage')->where(function($sub) {
                    $sub->where('amenities', 'like', '%джакузи%')
                        ->orWhere('amenities', 'like', '%Гидромассаж%')
                        ->orWhere('title', 'like', '%джакузи%')
                        ->orWhere('description', 'like', '%джакузи%');
                });
            }],
            'jacuzzi'            => ['group' => 'filters', 'field' => 'jacuzzi', 'value' => true, 'label' => 'с джакузи', 'filter' => function($q) {
                $q->where('type', '!=', 'cottage')->where(function($sub) {
                    $sub->where('amenities', 'like', '%джакузи%')
                        ->orWhere('amenities', 'like', '%Гидромассаж%')
                        ->orWhere('title', 'like', '%джакузи%')
                        ->orWhere('description', 'like', '%джакузи%');
                });
            }],

            // С баней (Группа B: filters)
            's-baney'            => ['group' => 'filters', 'field' => 'sauna', 'value' => true, 'label' => 'с баней', 'filter' => function($q) {
                $q->whereIn('type', ['cottage', 'agrotourism'])->where(function($sub) {
                    $sub->where('amenities', 'like', '%баня%')
                        ->orWhere('amenities', 'like', '%сауна%')
                        ->orWhere('territory_features', 'like', '%баня%')
                        ->orWhere('territory_features', 'like', '%сауна%')
                        ->orWhere('title', 'like', '%баня%')
                        ->orWhere('title', 'like', '%сауна%')
                        ->orWhere('description', 'like', '%баня%')
                        ->orWhere('description', 'like', '%сауна%');
                });
            }],
            'with-sauna'         => ['group' => 'filters', 'field' => 'sauna', 'value' => true, 'label' => 'с баней', 'filter' => function($q) {
                $q->whereIn('type', ['cottage', 'agrotourism'])->where(function($sub) {
                    $sub->where('amenities', 'like', '%баня%')
                        ->orWhere('amenities', 'like', '%сауна%')
                        ->orWhere('territory_features', 'like', '%баня%')
                        ->orWhere('territory_features', 'like', '%сауна%')
                        ->orWhere('title', 'like', '%баня%')
                        ->orWhere('title', 'like', '%сауна%')
                        ->orWhere('description', 'like', '%баня%')
                        ->orWhere('description', 'like', '%сауна%');
                });
            }],

            // С бассейном (Группа B: filters)
            's-basseynom'        => ['group' => 'filters', 'field' => 'pool', 'value' => true, 'label' => 'с бассейном', 'filter' => function($q) {
                $q->whereIn('type', ['cottage', 'agrotourism'])->where(function($sub) {
                    $sub->where('amenities', 'like', '%бассейн%')
                        ->orWhere('amenities', 'like', '%бассеин%')
                        ->orWhere('amenities', 'like', '%купель%')
                        ->orWhere('territory_features', 'like', '%бассейн%')
                        ->orWhere('territory_features', 'like', '%бассеин%')
                        ->orWhere('territory_features', 'like', '%купель%')
                        ->orWhere('title', 'like', '%бассейн%')
                        ->orWhere('title', 'like', '%бассеин%')
                        ->orWhere('title', 'like', '%купель%')
                        ->orWhere('description', 'like', '%бассейн%')
                        ->orWhere('description', 'like', '%бассеин%')
                        ->orWhere('description', 'like', '%купель%');
                });
            }],
            'with-pool'          => ['group' => 'filters', 'field' => 'pool', 'value' => true, 'label' => 'с бассейном', 'filter' => function($q) {
                $q->whereIn('type', ['cottage', 'agrotourism'])->where(function($sub) {
                    $sub->where('amenities', 'like', '%бассейн%')
                        ->orWhere('amenities', 'like', '%бассеин%')
                        ->orWhere('amenities', 'like', '%купель%')
                        ->orWhere('territory_features', 'like', '%бассейн%')
                        ->orWhere('territory_features', 'like', '%бассеин%')
                        ->orWhere('territory_features', 'like', '%купель%')
                        ->orWhere('title', 'like', '%бассейн%')
                        ->orWhere('title', 'like', '%бассеин%')
                        ->orWhere('title', 'like', '%купель%')
                        ->orWhere('description', 'like', '%бассейн%')
                        ->orWhere('description', 'like', '%бассеин%')
                        ->orWhere('description', 'like', '%купель%');
                });
            }],

            // Типы жилья (Группа B: filters)
            'kottedzhi'          => ['group' => 'filters', 'field' => 'type', 'value' => 'cottages', 'label' => 'коттеджи', 'filter' => function($q) {
                $q->whereIn('type', ['cottage', 'agrotourism']);
            }],
            'doma'               => ['group' => 'filters', 'field' => 'type', 'value' => 'cottages', 'label' => 'коттеджи', 'filter' => function($q) {
                $q->whereIn('type', ['cottage', 'agrotourism']);
            }],
            'cottages'           => ['group' => 'filters', 'field' => 'type', 'value' => 'cottages', 'label' => 'коттеджи', 'filter' => function($q) {
                $q->whereIn('type', ['cottage', 'agrotourism']);
            }],

            'kvartiry'           => ['group' => 'filters', 'field' => 'type', 'value' => 'apartments', 'label' => 'квартиры', 'filter' => function($q) {
                $q->where('type', '!=', 'cottage')->where(function($sub) {
                    $sub->whereNull('weekend_price')->orWhere('weekend_price', '<=', 0);
                });
            }],
            'apartments'         => ['group' => 'filters', 'field' => 'type', 'value' => 'apartments', 'label' => 'квартиры', 'filter' => function($q) {
                $q->where('type', '!=', 'cottage')->where(function($sub) {
                    $sub->whereNull('weekend_price')->orWhere('weekend_price', '<=', 0);
                });
            }],

            // Низкочастотные (НЧ) SEO фильтры
            's-konditsionerom'   => ['group' => 'filters', 'field' => 'conditioner', 'value' => true, 'label' => 'с кондиционером', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('amenities', 'like', '%кондиционер%')
                        ->orWhere('title', 'like', '%кондиционер%')
                        ->orWhere('description', 'like', '%кондиционер%');
                });
            }],
            'kvartiry-na-sutki-u-metro' => ['group' => 'filters', 'field' => 'metro', 'value' => true, 'label' => 'у метро', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->whereNotNull('nearest_metro')
                        ->where('nearest_metro', '!=', '')
                        ->where('nearest_metro', '!=', 'Нет')
                        ->where('nearest_metro', '!=', 'Метро не указано')
                        ->orWhere('address', 'like', '%метро%')
                        ->orWhere('description', 'like', '%метро%')
                        ->orWhere('title', 'like', '%метро%');
                });
            }],
            'u-metro'            => ['group' => 'filters', 'field' => 'metro', 'value' => true, 'label' => 'у метро', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->whereNotNull('nearest_metro')
                        ->where('nearest_metro', '!=', '')
                        ->where('nearest_metro', '!=', 'Нет')
                        ->where('nearest_metro', '!=', 'Метро не указано')
                        ->orWhere('address', 'like', '%метро%')
                        ->orWhere('description', 'like', '%метро%')
                        ->orWhere('title', 'like', '%метро%');
                });
            }],
            's-parkovkoy'        => ['group' => 'filters', 'field' => 'parking', 'value' => true, 'label' => 'с парковкой', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('amenities', 'like', '%парковк%')
                        ->orWhere('territory_features', 'like', '%парковк%')
                        ->orWhere('title', 'like', '%парковк%')
                        ->orWhere('description', 'like', '%парковк%')
                        ->orWhere('parking_type', 'like', '%парковка%')
                        ->orWhere('parking_type', '!=', '')
                        ->orWhereNotNull('parking_type');
                });
            }],
            'dlya-kompanii'      => ['group' => 'filters', 'field' => 'events_allowed', 'value' => true, 'label' => 'для компании', 'filter' => function($q) {
                $q->where('events_allowed', true)
                  ->orWhere('sleeping_places', '>=', 4)
                  ->orWhere('title', 'like', '%компани%')
                  ->orWhere('description', 'like', '%компани%');
            }],
            's-zhivotnymi'       => ['group' => 'filters', 'field' => 'animals_allowed', 'value' => true, 'label' => 'с животными', 'filter' => function($q) {
                $q->where('animals_allowed', true);
            }],
            'na-chas'            => ['group' => 'filters', 'field' => 'hourly', 'value' => true, 'label' => 'на час', 'filter' => function($q) {
                $q->where('type', '!=', 'cottage')->where('weekend_price', '>', 0);
            }],
            'u-vokzala'          => ['group' => 'filters', 'field' => 'station', 'value' => true, 'label' => 'у вокзала', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('address', 'like', '%вокзал%')
                        ->orWhere('address', 'like', '%ж/д%')
                        ->orWhere('address', 'like', '%жд%')
                        ->orWhere('description', 'like', '%вокзал%')
                        ->orWhere('description', 'like', '%ж/д%')
                        ->orWhere('description', 'like', '%жд%')
                        ->orWhere('title', 'like', '%вокзал%')
                        ->orWhere('title', 'like', '%ж/д%')
                        ->orWhere('title', 'like', '%жд%');
                });
            }],
            'dlya-semyi'         => ['group' => 'filters', 'field' => 'family', 'value' => true, 'label' => 'для семьи', 'filter' => function($q) {
                $q->where('children_allowed', true)
                  ->where('sleeping_places', '>=', 3);
            }],
            'u-parka'            => ['group' => 'filters', 'field' => 'park', 'value' => true, 'label' => 'у парка', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('address', 'like', '%парк%')
                        ->orWhere('description', 'like', '%парк%')
                        ->orWhere('title', 'like', '%парк%');
                });
            }],
            'dlya-komandirovki'  => ['group' => 'filters', 'field' => 'business', 'value' => true, 'label' => 'для командировки', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('description', 'like', '%документ%')
                        ->orWhere('description', 'like', '%отчетн%')
                        ->orWhere('description', 'like', '%командир%')
                        ->orWhere('communications', 'like', '%документ%')
                        ->orWhere('communications', 'like', '%отчетн%')
                        ->orWhere('amenities', 'like', '%документ%')
                        ->orWhere('amenities', 'like', '%отчетн%');
                });
            }],

            // Минские районы (Микрорайоны)
            'uruchye' => ['group' => 'filters', 'field' => 'district', 'value' => 'uruchye', 'label' => 'Уручье', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('district', 'like', '%Уручье%')
                        ->orWhere('address', 'like', '%Уручье%')
                        ->orWhere('title', 'like', '%Уручье%')
                        ->orWhere('description', 'like', '%Уручье%')
                        ->orWhere('nearest_metro', 'like', '%Уручье%');
                });
            }],
            'kamennaya-gorka' => ['group' => 'filters', 'field' => 'district', 'value' => 'kamennaya-gorka', 'label' => 'Каменная Горка', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('district', 'like', '%Каменн%')
                        ->orWhere('address', 'like', '%Каменн%')
                        ->orWhere('title', 'like', '%Каменн%')
                        ->orWhere('description', 'like', '%Каменн%')
                        ->orWhere('nearest_metro', 'like', '%Каменная горка%');
                });
            }],
            'nemiga' => ['group' => 'filters', 'field' => 'district', 'value' => 'nemiga', 'label' => 'Немига', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('district', 'like', '%Немиг%')
                        ->orWhere('address', 'like', '%Немиг%')
                        ->orWhere('title', 'like', '%Немиг%')
                        ->orWhere('description', 'like', '%Немиг%')
                        ->orWhere('nearest_metro', 'like', '%Немига%');
                });
            }],
            'suharevo' => ['group' => 'filters', 'field' => 'district', 'value' => 'suharevo', 'label' => 'Сухарево', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('district', 'like', '%Сухарев%')
                        ->orWhere('address', 'like', '%Сухарев%')
                        ->orWhere('title', 'like', '%Сухарев%')
                        ->orWhere('description', 'like', '%Сухарев%');
                });
            }],
            'zelenyy-lug' => ['group' => 'filters', 'field' => 'district', 'value' => 'zelenyy-lug', 'label' => 'Зеленый Луг', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('district', 'like', '%Зелен%')
                        ->orWhere('district', 'like', '%Луг%')
                        ->orWhere('address', 'like', '%Зелен%')
                        ->orWhere('address', 'like', '%Луг%')
                        ->orWhere('title', 'like', '%Зелен%')
                        ->orWhere('title', 'like', '%Луг%')
                        ->orWhere('description', 'like', '%Зелен%')
                        ->orWhere('description', 'like', '%Луг%');
                });
            }],
            'malinovka' => ['group' => 'filters', 'field' => 'district', 'value' => 'malinovka', 'label' => 'Малиновка', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('district', 'like', '%Малиновк%')
                        ->orWhere('address', 'like', '%Малиновк%')
                        ->orWhere('title', 'like', '%Малиновк%')
                        ->orWhere('description', 'like', '%Малиновк%')
                        ->orWhere('nearest_metro', 'like', '%Малиновка%');
                });
            }],
            'serebryanka' => ['group' => 'filters', 'field' => 'district', 'value' => 'serebryanka', 'label' => 'Серебрянка', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('district', 'like', '%Серебрянк%')
                        ->orWhere('address', 'like', '%Серебрянк%')
                        ->orWhere('title', 'like', '%Серебрянк%')
                        ->orWhere('description', 'like', '%Серебрянк%');
                });
            }],
            'yugo-zapad' => ['group' => 'filters', 'field' => 'district', 'value' => 'yugo-zapad', 'label' => 'Юго-Запад', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('district', 'like', '%Юго-Запад%')
                        ->orWhere('district', 'like', '%Югозапад%')
                        ->orWhere('address', 'like', '%Юго-Запад%')
                        ->orWhere('address', 'like', '%Югозапад%')
                        ->orWhere('title', 'like', '%Юго-Запад%')
                        ->orWhere('title', 'like', '%Югозапад%')
                        ->orWhere('description', 'like', '%Юго-Запад%')
                        ->orWhere('description', 'like', '%Югозапад%');
                });
            }],
            'vesnyanka' => ['group' => 'filters', 'field' => 'district', 'value' => 'vesnyanka', 'label' => 'Веснянка', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('district', 'like', '%Веснянк%')
                        ->orWhere('address', 'like', '%Веснянк%')
                        ->orWhere('title', 'like', '%Веснянк%')
                        ->orWhere('description', 'like', '%Веснянк%');
                });
            }],
            'kuntsevschina' => ['group' => 'filters', 'field' => 'district', 'value' => 'kuntsevschina', 'label' => 'Кунцевщина', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('district', 'like', '%Кунцевщин%')
                        ->orWhere('address', 'like', '%Кунцевщин%')
                        ->orWhere('title', 'like', '%Кунцевщин%')
                        ->orWhere('description', 'like', '%Кунцевщин%')
                        ->orWhere('nearest_metro', 'like', '%Кунцевщина%');
                });
            }],
            'minsk-mir' => ['group' => 'filters', 'field' => 'district', 'value' => 'minsk-mir', 'label' => 'Минск Мир', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('district', 'like', '%Минск Мир%')
                        ->orWhere('district', 'like', '%Минск-Мир%')
                        ->orWhere('address', 'like', '%Минск Мир%')
                        ->orWhere('address', 'like', '%Минск-Мир%')
                        ->orWhere('title', 'like', '%Минск Мир%')
                        ->orWhere('title', 'like', '%Минск-Мир%')
                        ->orWhere('description', 'like', '%Минск Мир%')
                        ->orWhere('description', 'like', '%Минск-Мир%');
                });
            }],
            'frunzenskiy' => ['group' => 'filters', 'field' => 'district', 'value' => 'frunzenskiy', 'label' => 'Фрунзенский район', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('district', 'like', '%Фрунзенск%')
                        ->orWhere('address', 'like', '%Фрунзенск%')
                        ->orWhere('title', 'like', '%Фрунзенск%')
                        ->orWhere('description', 'like', '%Фрунзенск%');
                });
            }],
            'vokzal' => ['group' => 'filters', 'field' => 'station', 'value' => true, 'label' => 'у вокзала', 'filter' => function($q) {
                $q->where(function($sub) {
                    $sub->where('address', 'like', '%вокзал%')
                        ->orWhere('address', 'like', '%ж/д%')
                        ->orWhere('address', 'like', '%жд%')
                        ->orWhere('description', 'like', '%вокзал%')
                        ->orWhere('description', 'like', '%ж/д%')
                        ->orWhere('description', 'like', '%жд%')
                        ->orWhere('title', 'like', '%вокзал%')
                        ->orWhere('title', 'like', '%ж/д%')
                        ->orWhere('title', 'like', '%жд%');
                });
            }],
        ];
    }

    /**
     * Обработка ЧПУ/SEO страниц приземления (город + теги)
     */
    public function seoPage(Request $request, $city_slug)
    {
        $this->ensureLoggedUser();
        $this->expireFeaturedListings();

        $city_slug_lower = strtolower(trim($city_slug));
        $cityRedirects = [
            'ive' => 'ivye',
            'marina-gorka' => 'maryina-gorka',
        ];

        if (isset($cityRedirects[$city_slug_lower])) {
            $correctSlug = $cityRedirects[$city_slug_lower];
            $parts = [$correctSlug];
            
            $tag1 = $request->route('tag1');
            $tag2 = $request->route('tag2');
            $tag3 = $request->route('tag3');
            $page = (int)($request->route('page') ?? 1);
            
            if ($tag1) $parts[] = $tag1;
            if ($tag2) $parts[] = $tag2;
            if ($tag3) $parts[] = $tag3;
            if ($page > 1) $parts[] = 'page-' . $page;
            
            $queryString = $request->getQueryString();
            return redirect('/' . implode('/', $parts) . ($queryString ? '?' . $queryString : ''), 301);
        }

        $canonicalUrl = null;

        // 1. Проверяем город
        $city = \App\Helpers\CityHelper::getCityNameBySlug($city_slug);
        if (!$city) {
            abort(404);
        }

        $inCityName = \App\Helpers\CityHelper::getInCityName($city);
        $tagsMap = $this->getSeoTagsMap();

        // Извлекаем параметры ЧПУ
        $tag1 = $request->route('tag1');
        $tag2 = $request->route('tag2');
        $tag3 = $request->route('tag3');
        $page = (int)($request->route('page') ?? 1);

        // Проверяем редиректы для устаревших тегов
        $redirectSlugMap = [
            'u-vokzala' => 'vokzal',
            'near-station' => 'vokzal',
            'kvartiry-na-sutki-u-metro' => 'metro',
            'u-metro' => 'metro',
            'sutki-uruchye' => 'uruchye',
            'sutki-kamennaya-gorka' => 'kamennaya-gorka',
            'sutki-nemiga' => 'nemiga',
            'sutki-suharevo' => 'suharevo',
            'sutki-zelenyy-lug' => 'zelenyy-lug',
            'sutki-malinovka' => 'malinovka',
            'sutki-serebryanka' => 'serebryanka',
            'sutki-yugo-zapad' => 'yugo-zapad',
            'sutki-vesnyanka' => 'vesnyanka',
            'sutki-kuntsevschina' => 'kuntsevschina',
            'sutki-minsk-mir' => 'minsk-mir',
        ];

        $hasRedirect = false;
        $checkTags = [$tag1, $tag2, $tag3];
        foreach ($checkTags as $idx => $t) {
            if ($t && isset($redirectSlugMap[strtolower($t)])) {
                $checkTags[$idx] = $redirectSlugMap[strtolower($t)];
                $hasRedirect = true;
            }
        }

        if ($hasRedirect) {
            $parts = [$city_slug];
            foreach ($checkTags as $t) {
                if ($t) $parts[] = $t;
            }
            if ($page > 1) {
                $parts[] = 'page-' . $page;
            }
            $queryString = $request->getQueryString();
            return redirect('/' . implode('/', $parts) . ($queryString ? '?' . $queryString : ''), 301);
        }

        // Проверяем наличие GET-параметров для редиректа на ЧПУ
        $queryParams = $request->query();
        if (count($queryParams) > 0) {
            $mappedTags = [];
            $roomsVal = $request->query('rooms');
            if ($roomsVal) {
                $roomsMap = [
                    1 => 'odnokomnatnye',
                    2 => 'dvuhkomnatnye',
                    3 => 'trehkomnatnye',
                    4 => 'chetyrehkomnatnye'
                ];
                if (isset($roomsMap[$roomsVal])) {
                    $mappedTags[] = $roomsMap[$roomsVal];
                    unset($queryParams['rooms']);
                }
            }

            $typeVal = $request->query('type');
            if ($typeVal) {
                $typeMap = [
                    'apartments' => 'kvartiry',
                    'apartment' => 'kvartiry',
                    'cottages' => 'kottedzhi',
                    'cottage' => 'kottedzhi',
                    'hourly' => 'na-chasy'
                ];
                if (isset($typeMap[$typeVal])) {
                    $mappedTags[] = $typeMap[$typeVal];
                    unset($queryParams['type']);
                }
            }

            // Дополнительные булевы фильтры
            $boolFilters = [
                'metro' => 'metro',
                'near_metro' => 'metro',
                'jacuzzi' => 'dzhakuzi',
                'center' => 'v-centre',
                'sauna' => 's-baney',
                'pool' => 's-basseynom'
            ];
            foreach ($boolFilters as $param => $tag) {
                if ($request->query($param) == 1 || $request->query($param) === 'true') {
                    $mappedTags[] = $tag;
                    unset($queryParams[$param]);
                }
            }

            $priceMaxVal = $request->query('price_max');
            if ($priceMaxVal && is_numeric($priceMaxVal) && (int)$priceMaxVal > 0) {
                $mappedTags[] = 'do-' . (int)$priceMaxVal;
                unset($queryParams['price_max']);
            }

            if (count($mappedTags) > 0) {
                // Извлекаем уже существующие теги из пути и объединяем с новыми из GET
                $existingTags = array_filter([$tag1, $tag2, $tag3]);
                $allTags = array_merge($existingTags, $mappedTags);
                
                // Нормализуем/очищаем массив тегов (удаляем дубликаты)
                $allTags = array_unique(array_map('strtolower', $allTags));

                // Разделяем на теги цены и обычные теги
                $priceLimit = null;
                $cleanTags = [];
                foreach ($allTags as $t) {
                    if (preg_match('/^do-(\d+)$/', $t, $matches)) {
                        $priceLimit = (int)$matches[1];
                    } else {
                        $cleanTags[] = $t;
                    }
                }

                // Сортируем обычные теги согласно каноническому порядку
                $t1Name = $cleanTags[0] ?? null;
                $t2Name = $cleanTags[1] ?? null;
                
                if ($t1Name && $t2Name) {
                    $t1D = $tagsMap[$t1Name] ?? null;
                    $t2D = $tagsMap[$t2Name] ?? null;
                    if ($t1D && $t2D) {
                        if ($t1D['group'] === 'filters' && $t2D['group'] === 'rooms') {
                            $temp = $t1Name;
                            $t1Name = $t2Name;
                            $t2Name = $temp;
                        } elseif ($t1D['group'] === 'filters' && $t2D['group'] === 'filters') {
                            if (strcmp($t1Name, $t2Name) > 0) {
                                $temp = $t1Name;
                                $t1Name = $t2Name;
                                $t2Name = $temp;
                            }
                        }
                    }
                }

                // Строим итоговый URL
                $parts = [$city_slug];
                if ($t1Name) $parts[] = $t1Name;
                if ($t2Name) $parts[] = $t2Name;
                if ($priceLimit) $parts[] = 'do-' . $priceLimit;
                
                $redirectUrl = '/' . implode('/', $parts);
                if (count($queryParams) > 0) {
                    $redirectUrl .= '?' . http_build_query($queryParams);
                }

                if ($request->ajax() || $request->has('ajax')) {
                    $canonicalUrl = $redirectUrl;
                    $tag1 = $t1Name;
                    $tag2 = $t2Name;
                    $tag3 = $priceLimit ? 'do-' . $priceLimit : null;
                } else {
                    return redirect($redirectUrl, 301);
                }
            }
        }

        if ($request->route('page') !== null && (int)$request->route('page') === 1) {
            // Редиректим с /page-1 на канонический вид без страницы
            $rawTags = array_filter([$tag1, $tag2, $tag3]);
            $priceLimit = null;
            $cleanTags = [];
            foreach ($rawTags as $t) {
                $t = strtolower(trim($t));
                if (preg_match('/^do-(\d+)$/', $t, $matches)) {
                    $priceLimit = (int)$matches[1];
                } else {
                    $cleanTags[] = $t;
                }
            }
            $parts = [$city_slug];
            if (isset($cleanTags[0])) $parts[] = $cleanTags[0];
            if (isset($cleanTags[1])) $parts[] = $cleanTags[1];
            if ($priceLimit) $parts[] = 'do-' . $priceLimit;

            $redirectUrl = '/' . implode('/', $parts);
            if ($request->ajax() || $request->has('ajax')) {
                $canonicalUrl = $redirectUrl;
            } else {
                return redirect($redirectUrl, 301);
            }
        }

        // Разбираем теги и извлекаем цену do-X
        $rawTags = array_filter([$tag1, $tag2, $tag3]);
        $priceLimit = null;
        $cleanTags = [];

        foreach ($rawTags as $t) {
            $t = strtolower(trim($t));
            if (preg_match('/^do-(\d+)$/', $t, $matches)) {
                $priceLimit = (int)$matches[1];
            } else {
                $cleanTags[] = $t;
            }
        }

        if (count($cleanTags) > 2) {
            abort(404);
        }

        $t1Data = null;
        $t2Data = null;

        if (count($cleanTags) >= 1) {
            $t1Name = $cleanTags[0];
            if (!isset($tagsMap[$t1Name])) {
                abort(404);
            }
            $t1Data = $tagsMap[$t1Name];
        }

        if (count($cleanTags) >= 2) {
            $t2Name = $cleanTags[1];
            if (!isset($tagsMap[$t2Name])) {
                abort(404);
            }
            $t2Data = $tagsMap[$t2Name];
        }

        // Вспомогательная функция для построения канонического URL
        $buildCanonicalUrl = function($city_slug, $t1, $t2, $priceLimit, $page) {
            $parts = [$city_slug];
            if ($t1) $parts[] = $t1;
            if ($t2) $parts[] = $t2;
            if ($priceLimit) $parts[] = 'do-' . $priceLimit;
            if ($page > 1) $parts[] = 'page-' . $page;
            return '/' . implode('/', $parts);
        };

        // Проверка на корректность комбинаций и редирект на канонический порядок
        if ($t1Data && $t2Data) {
            if ($t1Data['group'] === $t2Data['group']) {
                abort(404);
            }

            if ($t1Data['group'] === 'filters' && $t2Data['group'] === 'rooms') {
                return redirect($buildCanonicalUrl($city_slug, $cleanTags[1], $cleanTags[0], $priceLimit, $page), 301);
            }

            if ($t1Data['group'] === 'filters' && $t2Data['group'] === 'filters') {
                if (strcmp($cleanTags[0], $cleanTags[1]) > 0) {
                    return redirect($buildCanonicalUrl($city_slug, $cleanTags[1], $cleanTags[0], $priceLimit, $page), 301);
                }
            }
        }

        // Build the canonical URL pointing to the current page (preserving page segment)
        $canonicalPath = '/' . implode('/', array_filter([
            $city_slug,
            $cleanTags[0] ?? null,
            $cleanTags[1] ?? null,
            $priceLimit ? 'do-' . $priceLimit : null,
            $page > 1 ? 'page-' . $page : null
        ]));
        $canonicalUrl = url($canonicalPath);

        // 3. Формируем SQL-запрос
        $query = Listing::where('status', 'active')->where('city', $city);
        
        $activeCategory = null;
        if ($t1Data) {
            $t1Data['filter']($query);
            $activeCategory = $cleanTags[0];
        }
        if ($t2Data) {
            $t2Data['filter']($query);
        }

        if ($priceLimit) {
            $query->where('price', '<=', $priceLimit);
        }

        // 4. Формируем SEO метатеги (натуральный русский язык)
        $seoH1 = '';
        $seoTitle = '';

        $mbUcfirst = function($str) {
            return mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($str, 1, null, 'UTF-8');
        };

        if (!$t1Data) {
            $seoH1 = "Аренда квартир посуточно " . $inCityName;
            $seoTitle = "Квартиры на сутки " . $inCityName . " без посредников | Flatly";
        } elseif ($t1Data && !$t2Data) {
            if ($t1Data['group'] === 'rooms') {
                $seoH1 = $mbUcfirst($t1Data['label']) . " квартиры на сутки " . $inCityName;
                $seoTitle = "Снять " . mb_substr($t1Data['label'], 0, -2) . "ую квартиру посуточно " . $inCityName . " без посредников | Flatly";
            } else {
                if ($cleanTags[0] === 'na-chasy' || $cleanTags[0] === 'nachasy') {
                    $seoH1 = "Квартиры на часы (почасово) " . $inCityName;
                    $seoTitle = "Аренда квартир на часы " . $inCityName . " почасово | Flatly";
                } elseif ($cleanTags[0] === 'v-centre') {
                    $seoH1 = "Квартиры в центре " . $inCityName;
                    $seoTitle = "Снять квартиру посуточно в центре " . $inCityName . " без посредников | Flatly";
                } elseif ($cleanTags[0] === 'metro') {
                    $seoH1 = "Квартиры возле метро " . $inCityName;
                    $seoTitle = "Снять квартиру посуточно возле метро " . $inCityName . " без посредников | Flatly";
                } elseif ($t1Data['field'] === 'type') {
                    $seoH1 = $mbUcfirst($t1Data['label']) . " на сутки " . $inCityName;
                    $seoTitle = "Снять " . ($cleanTags[0] === 'kvartiry' || $cleanTags[0] === 'apartments' ? 'квартиру' : 'коттедж или усадьбу') . " посуточно " . $inCityName . " без посредников | Flatly";
                } else {
                    $seoH1 = "Квартиры " . $t1Data['label'] . " " . $inCityName;
                    $seoTitle = "Снять квартиру посуточно " . $t1Data['label'] . " " . $inCityName . " без посредников | Flatly";
                }
            }
        } else {
            if ($t1Data['group'] === 'rooms' && $t2Data['field'] === 'hourly') {
                $seoH1 = $mbUcfirst($t1Data['label']) . " квартиры на часы " . $inCityName;
                $seoTitle = "Снять " . mb_substr($t1Data['label'], 0, -2) . "ую квартиру на часы " . $inCityName . " почасово | Flatly";
            } elseif ($t1Data['group'] === 'rooms' && $cleanTags[1] === 'v-centre') {
                $seoH1 = $mbUcfirst($t1Data['label']) . " квартиры в центре " . $inCityName;
                $seoTitle = "Снять " . mb_substr($t1Data['label'], 0, -2) . "ую квартиру посуточно в центре " . $inCityName . " без посредников | Flatly";
            } elseif ($t1Data['group'] === 'rooms' && $cleanTags[1] === 'metro') {
                $seoH1 = $mbUcfirst($t1Data['label']) . " квартиры возле метро " . $inCityName;
                $seoTitle = "Снять " . mb_substr($t1Data['label'], 0, -2) . "ую квартиру посуточно возле метро " . $inCityName . " без посредников | Flatly";
            } elseif ($t1Data['group'] === 'rooms' && $t2Data['field'] === 'jacuzzi') {
                $seoH1 = $mbUcfirst($t1Data['label']) . " квартиры с джакузи " . $inCityName;
                $seoTitle = "Снять " . mb_substr($t1Data['label'], 0, -2) . "ую квартиру посуточно с джакузи " . $inCityName . " без посредников | Flatly";
            } else {
                $seoH1 = $mbUcfirst($t1Data['label']) . " квартиры " . $t2Data['label'] . " " . $inCityName;
                $seoTitle = "Снять " . mb_substr($t1Data['label'], 0, -2) . "ую квартиру посуточно " . $t2Data['label'] . " " . $inCityName . " без посредников | Flatly";
            }
        }

        if ($priceLimit) {
            $seoH1 .= " до " . $priceLimit . " руб.";
            $seoTitle = str_replace(" | Flatly", " до " . $priceLimit . " BYN | Flatly", $seoTitle);
        }

        if ($page > 1) {
            $seoTitle .= " — Страница " . $page;
        }

        $seoDescription = "Предложения по запросу: " . mb_strtolower($seoH1) . " напрямую от хозяев без комиссий. Реальные фото, цены и контакты владельцев на Flatly.";
        if ($page > 1) {
            $seoDescription .= " Страница " . $page . " списка предложений.";
        }

        if ($page > 1) {
            $seoH1 .= " — Страница " . $page;
        }

        // Проверка НЧ-страниц (низкочастотных запросов) и подгрузка кастомных данных
        $landingPage = null;
        $canonicalTagToCheck = null;
        if (isset($cleanTags[0])) {
            $canonicalTagToCheck = $cleanTags[0];
            if ($canonicalTagToCheck === 'u-metro' || $canonicalTagToCheck === 'metro') {
                $canonicalTagToCheck = 'kvartiry-na-sutki-u-metro';
            } elseif ($canonicalTagToCheck === 'na-chasy' || $canonicalTagToCheck === 'nachasy') {
                $canonicalTagToCheck = 'na-chas';
            }
        } elseif ($priceLimit === 100) {
            $canonicalTagToCheck = 'do-100';
        }

        $seoPagesConfig = config('seo_landing_pages');
        $cityKey = strtolower($city_slug);
        $tagKey = isset($cleanTags[0]) ? $cleanTags[0] : null;

        if (!$tagKey && isset($seoPagesConfig[$cityKey]['_main'])) {
            $landingPage = $seoPagesConfig[$cityKey]['_main'];
        } elseif ($tagKey && isset($seoPagesConfig[$cityKey][$tagKey])) {
            $landingPage = $seoPagesConfig[$cityKey][$tagKey];
        } elseif ($canonicalTagToCheck && isset($seoPagesConfig[$cityKey][$canonicalTagToCheck])) {
            $landingPage = $seoPagesConfig[$cityKey][$canonicalTagToCheck];
        }

        if ($landingPage) {
            $seoH1 = $landingPage['h1'];
            $seoTitle = $landingPage['title'];
            $seoDescription = $landingPage['description'];
            
            if ($page > 1) {
                $seoTitle .= " — Страница " . $page;
                $seoH1 .= " — Страница " . $page;
                $seoDescription .= " Страница " . $page . " списка предложений.";
            }
        }

        $this->applyRequestFilters($query, $request);
        try {
            $listings = $query->inRandomOrder(date('YmdH'))->get();
        } catch (\Exception $e) {
            $listings = collect();
        }

        // 5. Динамические Breadcrumbs
        $breadcrumbs = [
            ['url' => '/', 'title' => 'Главная'],
            ['url' => '/' . $city_slug, 'title' => 'Жилье ' . $inCityName]
        ];
        $currentPath = '/' . $city_slug;
        if ($t1Data) {
            $currentPath .= '/' . $cleanTags[0];
            $breadcrumbs[] = ['url' => $currentPath, 'title' => $mbUcfirst($t1Data['label'])];
        }
        if ($t2Data) {
            $currentPath .= '/' . $cleanTags[1];
            $breadcrumbs[] = ['url' => $currentPath, 'title' => $mbUcfirst($t2Data['label'])];
        }
        if ($priceLimit) {
            $currentPath .= '/do-' . $priceLimit;
            $breadcrumbs[] = ['url' => $currentPath, 'title' => 'До ' . $priceLimit . ' руб.'];
        }
        if ($page > 1) {
            $breadcrumbs[] = ['url' => null, 'title' => 'Страница ' . $page];
        }

        $selectedCity = $city;
        $inCity = $inCityName;
        $mapCenter = \App\Helpers\CityHelper::getCityCoordinates($city, $listings);
        $seoCategory = $activeCategory;
        $currentPage = $page;

        return $this->renderCatalogView($request, compact(
            'listings', 'selectedCity', 'inCity', 'mapCenter',
            'seoTitle', 'seoDescription', 'seoH1', 'seoCategory',
            'breadcrumbs', 'currentPage', 'canonicalUrl', 'landingPage'
        ));
    }

    /**
     * Выделенная страница категории "Квартиры посуточно" (глобальная для Беларуси)
     */
    public function apartmentsPage(Request $request)
    {
        $this->ensureLoggedUser();
        $this->expireFeaturedListings();

        try {
            $query = Listing::where('status', 'active')
                ->where('type', '!=', 'cottage')
                ->where(function($q) {
                    $q->whereNull('weekend_price')
                      ->orWhere('weekend_price', '<=', 0);
                });
            $this->applyRequestFilters($query, $request);
            $listings = $query->inRandomOrder(date('YmdH'))->get();
        } catch (\Exception $e) {
            $listings = collect();
        }

        $seoPagesConfig = config('seo_landing_pages');
        $landingPage = $seoPagesConfig['apartments']['_main'] ?? null;

        $seoTitle = $landingPage['title'] ?? 'Снять квартиру посуточно в Беларуси — каталог на карте';
        $seoDescription = $landingPage['description'] ?? 'Каталог квартир на сутки по Беларуси: Минск и регионы, цены, фото, телефон хозяина. Откройте карту и бронируйте напрямую.';
        $seoH1 = $landingPage['h1'] ?? 'Квартиры посуточно в Беларуси';
        $seoCategory = 'apartments';
        $breadcrumbs = [
            ['url' => '/', 'title' => 'Главная'],
            ['url' => null, 'title' => 'Квартиры посуточно']
        ];

        return $this->renderCatalogView($request, compact('listings', 'seoTitle', 'seoDescription', 'seoH1', 'seoCategory', 'landingPage', 'breadcrumbs'));
    }

    /**
     * Выделенная страница категории "Коттеджи на сутки" (глобальная для Беларуси)
     */
    public function cottagesPage(Request $request)
    {
        $this->ensureLoggedUser();
        $this->expireFeaturedListings();

        try {
            $query = Listing::where('status', 'active')
                ->whereIn('type', ['cottage', 'agrotourism']);
            $this->applyRequestFilters($query, $request);
            $listings = $query->inRandomOrder(date('YmdH'))->get();
        } catch (\Exception $e) {
            $listings = collect();
        }

        $seoPagesConfig = config('seo_landing_pages');
        $landingPage = $seoPagesConfig['cottages']['_main'] ?? null;

        $seoTitle = $landingPage['title'] ?? 'Снять дом или коттедж на сутки в Беларуси';
        $seoDescription = $landingPage['description'] ?? 'Дома, коттеджи и усадьбы на сутки по Беларуси: цены, фото, вместимость. Для выходных и компании — прямой контакт с хозяином.';
        $seoH1 = $landingPage['h1'] ?? 'Аренда домов и коттеджей на сутки';
        $seoCategory = 'cottages';
        $breadcrumbs = [
            ['url' => '/', 'title' => 'Главная'],
            ['url' => null, 'title' => 'Коттеджи на сутки']
        ];

        return $this->renderCatalogView($request, compact('listings', 'seoTitle', 'seoDescription', 'seoH1', 'seoCategory', 'landingPage', 'breadcrumbs'));
    }

    /**
     * Страницы категорий коттеджей (например, /cottages/s-basseynom, /cottages/s-baney)
     */
    public function cottageCategoryPage(Request $request)
    {
        $this->ensureLoggedUser();
        $this->expireFeaturedListings();

        $path = trim($request->path(), '/');
        $filter = str_contains($path, 's-basseynom') ? 's-basseynom' : 's-baney';

        try {
            $query = Listing::where('status', 'active')
                ->whereIn('type', ['cottage', 'agrotourism']);

            if ($filter === 's-basseynom') {
                $query->where(function($q) {
                    $q->where('amenities', 'like', '%бассейн%')
                      ->orWhere('amenities', 'like', '%бассеин%')
                      ->orWhere('amenities', 'like', '%купель%')
                      ->orWhere('territory_features', 'like', '%бассейн%')
                      ->orWhere('territory_features', 'like', '%бассеин%')
                      ->orWhere('title', 'like', '%бассейн%')
                      ->orWhere('description', 'like', '%бассейн%');
                });
            } else {
                $query->where(function($q) {
                    $q->where('amenities', 'like', '%баня%')
                      ->orWhere('amenities', 'like', '%сауна%')
                      ->orWhere('territory_features', 'like', '%баня%')
                      ->orWhere('territory_features', 'like', '%сауна%')
                      ->orWhere('title', 'like', '%баня%')
                      ->orWhere('description', 'like', '%баня%');
                });
            }

            $this->applyRequestFilters($query, $request);
            $listings = $query->inRandomOrder(date('YmdH'))->get();
        } catch (\Exception $e) {
            $listings = collect();
        }

        $seoPagesConfig = config('seo_landing_pages');
        $landingPage = $seoPagesConfig['cottages'][$filter] ?? null;

        if ($landingPage) {
            $seoTitle = $landingPage['title'];
            $seoDescription = $landingPage['description'];
            $seoH1 = $landingPage['h1'];
        } else {
            $seoTitle = ($filter === 's-basseynom')
                ? 'Аренда дома и коттеджа с бассейном на сутки посуточно в Беларуси от хозяина | Flatly'
                : 'Снять коттедж и дом с баней посуточно на сутки в Беларуси от хозяина | Flatly';
            $seoDescription = ($filter === 's-basseynom')
                ? 'Снять дом или коттедж с бассейном посуточно в Беларуси без посредников. Загородные усадьбы с крытым или открытым бассейном: фото и цены на Flatly.by.'
                : 'Снять коттедж или дом с баней (сауной) на сутки в Беларуси без комиссий. Прямые контакты хозяев усадеб с баней: фото и цены на Flatly.by.';
            $seoH1 = ($filter === 's-basseynom')
                ? 'Снять дом или коттедж с бассейном посуточно'
                : 'Аренда дома и коттеджа с баней на сутки';
        }

        $seoCategory = $filter;

        return $this->renderCatalogView($request, compact('listings', 'seoTitle', 'seoDescription', 'seoH1', 'seoCategory', 'landingPage'));
    }

    /**
     * Выделенная страница категории "Квартиры на часы" (почасово, глобальная для Беларуси)
     */
    public function hourlyPage(Request $request)
    {
        $this->ensureLoggedUser();
        $this->expireFeaturedListings();

        try {
            $query = Listing::where('status', 'active')
                ->where('type', '!=', 'cottage')
                ->where('weekend_price', '>', 0);
            $this->applyRequestFilters($query, $request);
            $listings = $query->inRandomOrder(date('YmdH'))->get();
        } catch (\Exception $e) {
            $listings = collect();
        }

        $seoTitle = 'Аренда квартир на часы в Беларуси посуточно/почасово | Flatly';
        $seoDescription = 'Снять квартиру на несколько часов или почасово в Минске и городах Беларуси. Прямые контакты хозяев, актуальные цены.';
        $seoH1 = 'Почасовая аренда квартир в Беларуси';
        $seoCategory = 'hourly';
        $breadcrumbs = [
            ['url' => '/', 'title' => 'Главная'],
            ['url' => null, 'title' => 'Квартиры на часы']
        ];

        return $this->renderCatalogView($request, compact('listings', 'seoTitle', 'seoDescription', 'seoH1', 'seoCategory', 'breadcrumbs'));
    }

    /**
     * Страница деталей квартиры
     */
    public function show($id, $slug = null)
    {
        try {
            $listing = Listing::find($id);
            if (!$listing) {
                $featuredListings = Listing::where('status', 'active')
                    ->whereHas('userTariffs', function($query) {
                        $query->where('tariff_id', 4)
                              ->where('status', 'active')
                              ->where('expires_at', '>', now());
                    })
                    ->inRandomOrder()
                    ->take(5)
                    ->get();

                if ($featuredListings->isEmpty()) {
                    $featuredListings = Listing::where('status', 'active')
                        ->inRandomOrder()
                        ->take(5)
                        ->get();
                }

                return response()->view('errors.410', [
                    'type' => 'listing',
                    'featuredListings' => $featuredListings
                ], 410);
            }

            // Если объявление неактивно, разрешаем просмотр только его владельцу
            if ($listing->status !== 'active') {
                $isOwner = session('logged_in') && session('user_id') == $listing->user_id;
                if (!$isOwner) {
                    $featuredListings = Listing::where('status', 'active')
                        ->whereHas('userTariffs', function($query) {
                            $query->where('tariff_id', 4)
                                  ->where('status', 'active')
                                  ->where('expires_at', '>', now());
                        })
                        ->inRandomOrder()
                        ->take(5)
                        ->get();

                    if ($featuredListings->isEmpty()) {
                        $featuredListings = Listing::where('status', 'active')
                            ->inRandomOrder()
                            ->take(5)
                            ->get();
                    }

                    return response()->view('errors.410', [
                        'type' => 'listing',
                        'featuredListings' => $featuredListings
                    ], 410);
                }
            }

            // Редирект на канонический URL при несовпадении или отсутствии slug
            $correctSlug = $listing->slug ?: 'listing';
            if (empty($slug) || $slug !== $correctSlug) {
                return redirect()->route('listings.show', [
                    'id' => $listing->id,
                    'slug' => $correctSlug
                ], 301);
            }


            // Increment views count and send view notification only if valid guest view
            try {
                $userAgent = request()->header('User-Agent', '');
                $isBot = $this->isBotUserAgent($userAgent);
                $isOwner = session('logged_in') && session('user_id') == $listing->user_id;
                $isAdmin = session('admin_logged_in') == true;

                if (!$isOwner && !$isAdmin && !$isBot) {
                    $sessionKey = 'viewed_listing_' . $listing->id;
                    $cacheKey = 'view_notif_' . $listing->id . '_' . md5(request()->ip() . '_' . $userAgent);

                    $alreadyViewedSession = session()->has($sessionKey);
                    $alreadyViewedCache = \Illuminate\Support\Facades\Cache::has($cacheKey);

                    if (!$alreadyViewedSession && !$alreadyViewedCache) {
                        session()->put($sessionKey, time());
                        \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addMinutes(30));

                        $listing->increment('views_count');
                        
                        $deviceType = 'desktop';
                        if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $userAgent)) {
                            $deviceType = 'tablet';
                        } elseif (preg_match('/(up\.browser|up\.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile|iphone|ipad|ipod)/i', $userAgent)) {
                            $deviceType = 'mobile';
                        }
                        
                        \App\Models\ListingInteraction::create([
                            'listing_id' => $listing->id,
                            'interaction_type' => 'view',
                            'device_type' => $deviceType,
                        ]);

                        // Send Telegram admin notification about view
                        $this->sendTelegramViewNotification($listing, $deviceType);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to increment views_count: ' . $e->getMessage());
            }

            $amenities = is_string($listing->amenities) ? json_decode($listing->amenities, true) : $listing->amenities;
            if (!is_array($amenities)) {
                $amenities = [];
            }
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Объявление не найдено');
        }

        // Выбираем похожие объявления в том же городе с учетом типа, района и цены
        try {
            $currentPrice = (float) $listing->price;
            $priceMin = $currentPrice * 0.7;
            $priceMax = $currentPrice * 1.3;

            $similarQuery = Listing::where('city', $listing->city)
                ->where('id', '!=', $listing->id)
                ->where('status', 'active');

            if (!empty($listing->type)) {
                $similarQuery->orderByRaw('CASE WHEN type = ? THEN 0 ELSE 1 END', [$listing->type]);
            }
            if (!empty($listing->district)) {
                $similarQuery->orderByRaw('CASE WHEN district = ? THEN 0 ELSE 1 END', [$listing->district]);
            }

            $similarListings = $similarQuery
                ->orderByRaw('CASE WHEN price BETWEEN ? AND ? THEN 0 ELSE 1 END', [$priceMin, $priceMax])
                ->inRandomOrder()
                ->take(4)
                ->get();
        } catch (\Exception $e) {
            $similarListings = collect();
        }

        // Загрузка реальных отзывов из базы данных
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('reviews')) {
                $dbReviews = \App\Models\Review::where('listing_id', $listing->id)->orderBy('created_at', 'desc')->get();
                if ($dbReviews->isNotEmpty()) {
                    $listing->reviews = $dbReviews->map(function($r) {
                        return [
                            'id' => $r->id,
                            'name' => $r->author_name,
                            'rating' => $r->rating,
                            'content' => $r->comment,
                            'date' => $r->created_at->format('d.m.Y'),
                            'elapsed' => $r->created_at->locale('ru')->diffForHumans(),
                        ];
                    })->toArray();
                }
            }
        } catch (\Exception $e) {}

        return view('listings.show', compact('listing', 'amenities', 'similarListings'));
    }

    /**
     * Сохранение нового отзыва к объявлению
     */
    public function storeReview(Request $request, $id)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'author_name' => 'required|string|max:100',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:1000',
        ], [
            'author_name.required' => 'Пожалуйста, введите ваше имя.',
            'rating.required' => 'Укажите оценку от 1 до 5 звезд.',
            'comment.required' => 'Напишите текст отзыва.',
            'comment.min' => 'Текст отзыва должен содержать не менее 3 символов.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $listing = Listing::findOrFail($id);

        try {
            $review = \App\Models\Review::create([
                'listing_id' => $listing->id,
                'user_id' => session('logged_in') ? session('user_id') : null,
                'author_name' => trim($request->input('author_name')),
                'rating' => (int)$request->input('rating'),
                'comment' => trim($request->input('comment')),
            ]);

            // Send Telegram notification to owner about new review
            $host = $listing->host;
            $ownerChatId = $host ? $host->getTelegramChatId() : null;
            $botToken = env('TELEGRAM_BOT_TOKEN');
            if ($botToken && !empty($ownerChatId)) {
                $stars = str_repeat('⭐', $review->rating);
                $listingUrl = route('listings.show', ['id' => $listing->id, 'slug' => $listing->slug ?: 'listing']);
                $reviewText = "⭐️ <b>Новый отзыв к вашему объявлению!</b>\n\n"
                            . "<b>Объявление:</b> <a href=\"{$listingUrl}\">" . htmlspecialchars($listing->title) . "</a>\n"
                            . "<b>Оценка:</b> {$stars} ({$review->rating}/5)\n"
                            . "<b>Гость:</b> " . htmlspecialchars($review->author_name) . "\n"
                            . "<b>Текст:</b> «" . htmlspecialchars($review->comment) . "»\n\n"
                            . "<b>Время:</b> " . now()->format('H:i, d.m.Y');

                try {
                    \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                        'chat_id' => $ownerChatId,
                        'text' => $reviewText,
                        'parse_mode' => 'HTML',
                        'reply_markup' => json_encode([
                            'inline_keyboard' => [
                                [['text' => '🔗 Посмотреть отзывы на сайте', 'url' => $listingUrl]]
                            ]
                        ])
                    ]);
                } catch (\Exception $e) {
                    \Log::warning('Failed to send review Telegram notification: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Ваш отзыв успешно опубликован!',
                'review' => [
                    'id' => $review->id,
                    'name' => $review->author_name,
                    'rating' => $review->rating,
                    'content' => $review->comment,
                    'date' => $review->created_at->format('d.m.Y'),
                    'elapsed' => $review->created_at->locale('ru')->diffForHumans(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Не удалось сохранить отзыв: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX endpoint to track messenger and call interactions
     */
    public function trackInteraction(Request $request, $id)
    {
        try {
            $listing = Listing::findOrFail($id);
            $type = $request->input('type'); // 'phone', 'telegram', 'viber', 'whatsapp', or 'contact'

            // Map legacy 'contact' if passed
            if ($type === 'contact') {
                $type = 'phone';
            }

            $validTypes = ['phone', 'telegram', 'viber', 'whatsapp'];
            if (in_array($type, $validTypes)) {
                // Detect device
                $userAgent = $request->header('User-Agent', '');
                $deviceType = 'desktop';
                if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $userAgent)) {
                    $deviceType = 'tablet';
                } elseif (preg_match('/(up\.browser|up\.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile|iphone|ipad|ipod)/i', $userAgent)) {
                    $deviceType = 'mobile';
                }

                \App\Models\ListingInteraction::create([
                    'listing_id' => $listing->id,
                    'interaction_type' => $type,
                    'device_type' => $deviceType,
                ]);

                // Increment legacy counter for backwards compatibility
                $listing->increment('contacts_count');

                // Anti-spam cooldown: notify owner once per 5 minutes per guest session for same listing+type
                $cooldownKey = 'contact_notified_' . $listing->id . '_' . $type;
                if (!session()->has($cooldownKey)) {
                    session()->put($cooldownKey, time());
                    // Send Telegram notification about contact click
                    $this->sendTelegramContactClickNotification($listing, $type, $deviceType);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function isBotUserAgent(?string $userAgent): bool
    {
        if (empty($userAgent)) {
            return false;
        }
        return (bool) preg_match('/bot|crawl|spider|slurp|facebookexternalhit|telegrambot|whatsapp|vkshare|twitterbot|pinterest|googlebot|yandex|bingbot|duckduckbot|baidu|curl|wget|python-requests/i', $userAgent);
    }

    private function sendTelegramViewNotification(Listing $listing, string $deviceType)
    {
        $userAgent = request()->header('User-Agent', '');
        if ($this->isBotUserAgent($userAgent)) {
            return;
        }

        $isOwner = session('logged_in') && session('user_id') == $listing->user_id;
        $isAdmin = session('admin_logged_in') == true;
        if ($isOwner || $isAdmin) {
            return;
        }

        $botToken = env('TELEGRAM_BOT_TOKEN');
        if (!$botToken) {
            return;
        }

        $deviceLabel = $deviceType === 'mobile' ? '📱 Мобильное устройство' : ($deviceType === 'tablet' ? '📑 Планшет' : '💻 Компьютер');
        $listingUrl = route('listings.show', ['id' => $listing->id, 'slug' => $listing->slug ?: 'listing']);
        $host = $listing->host;

        $ownerChatId = $host ? $host->getTelegramChatId() : null;

        // If host is registered in Telegram bot, send notification to owner instead of admin
        if (!empty($ownerChatId)) {
            $text = "👁️ <b>Просмотр вашего объявления на сайте!</b>\n\n"
                  . "<b>Объявление:</b> <a href=\"{$listingUrl}\">" . htmlspecialchars($listing->title) . "</a>\n"
                  . "<b>Адрес:</b> " . htmlspecialchars($listing->address ?? 'Минск') . "\n"
                  . "<b>Устройство гостя:</b> {$deviceLabel}\n"
                  . "<b>Время:</b> " . now()->format('H:i, d.m.Y');

            if ($listing->isAgencyListing()) {
                $text = "<b>АГЕНТСТВО</b>\n\n" . $text;
            }

            $ownerButtons = [
                'inline_keyboard' => [
                    [
                        ['text' => '🔗 Открыть на сайте', 'url' => $listingUrl],
                        ['text' => '📊 Личный кабинет', 'url' => 'https://flatly.by/dashboard']
                    ]
                ]
            ];

            try {
                \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $ownerChatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode($ownerButtons),
                ]);
            } catch (\Exception $e) {
                \Log::warning('Failed to send Telegram view notification to owner: ' . $e->getMessage());
            }
            return;
        }

        // Fallback: Send to admin if owner is not registered in bot
        $adminChatIdString = env('TELEGRAM_ADMIN_CHAT_ID');
        if (!$adminChatIdString) {
            return;
        }

        $text = "👁️ <b>Просмотр объявления на сайте!</b>\n\n"
              . "<b>Объявление:</b> <a href=\"{$listingUrl}\">" . htmlspecialchars($listing->title) . "</a>\n"
              . "<b>Адрес:</b> " . htmlspecialchars($listing->address ?? 'Минск') . "\n"
              . "<b>Устройство:</b> {$deviceLabel}\n"
              . "<b>Хозяин:</b> " . htmlspecialchars($host ? $host->name : 'Неизвестный') . " (" . htmlspecialchars($listing->phone ?? ($host->phone ?? 'Нет телефона')) . ")";

        if ($listing->isAgencyListing()) {
            $text = "<b>АГЕНТСТВО</b>\n\n" . $text;
        }

        $adminButtons = [
            'inline_keyboard' => [
                [
                    ['text' => '🔗 Открыть на сайте', 'url' => $listingUrl],
                    ['text' => '⚙️ Админка', 'url' => route('admin.dashboard')]
                ]
            ]
        ];

        $chatIds = array_filter(array_map('trim', explode(',', $adminChatIdString)));
        foreach ($chatIds as $chatId) {
            try {
                \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode($adminButtons),
                ]);
            } catch (\Exception $e) {
                \Log::warning('Failed to send Telegram view notification: ' . $e->getMessage());
            }
        }
    }

    private function sendTelegramContactClickNotification(Listing $listing, string $type, string $deviceType)
    {
        $userAgent = request()->header('User-Agent', '');
        if ($this->isBotUserAgent($userAgent)) {
            return;
        }

        $isOwner = session('logged_in') && session('user_id') == $listing->user_id;
        $isAdmin = session('admin_logged_in') == true;
        if ($isOwner || $isAdmin) {
            return;
        }

        $botToken = env('TELEGRAM_BOT_TOKEN');
        if (!$botToken) {
            return;
        }

        $typeNames = [
            'phone' => '📞 Телефон',
            'telegram' => '✈️ Telegram',
            'viber' => '💜 Viber',
            'whatsapp' => '💬 WhatsApp'
        ];
        $channelName = $typeNames[$type] ?? '📞 Контакты';
        $deviceLabel = $deviceType === 'mobile' ? '📱 Мобильный' : ($deviceType === 'tablet' ? '📑 Планшет' : '💻 Компьютер');
        $listingUrl = route('listings.show', ['id' => $listing->id, 'slug' => $listing->slug ?: 'listing']);
        $host = $listing->host;

        $ownerChatId = $host ? $host->getTelegramChatId() : null;

        // If host is registered in Telegram bot, send notification to owner instead of admin
        if (!empty($ownerChatId)) {
            $text = "🎯 <b>Гость кликнул по вашим контактам!</b>\n\n"
                  . "<b>Канал связи:</b> {$channelName}\n"
                  . "<b>Объявление:</b> <a href=\"{$listingUrl}\">" . htmlspecialchars($listing->title) . "</a>\n"
                  . "<b>Адрес:</b> " . htmlspecialchars($listing->address ?? 'Минск') . "\n"
                  . "<b>Устройство гостя:</b> {$deviceLabel}\n"
                  . "<b>Время:</b> " . now()->format('H:i, d.m.Y');

            if ($listing->isAgencyListing()) {
                $text = "<b>АГЕНТСТВО</b>\n\n" . $text;
            }

            $ownerButtons = [
                'inline_keyboard' => [
                    [
                        ['text' => '🔗 Открыть на сайте', 'url' => $listingUrl],
                        ['text' => '📊 Личный кабинет', 'url' => 'https://flatly.by/dashboard']
                    ]
                ]
            ];

            try {
                \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $ownerChatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode($ownerButtons),
                ]);
            } catch (\Exception $e) {
                \Log::warning('Failed to send Telegram contact click notification to owner: ' . $e->getMessage());
            }
            return;
        }

        // Fallback: Send to admin if owner is not registered in bot
        $adminChatIdString = env('TELEGRAM_ADMIN_CHAT_ID');
        if (!$adminChatIdString) {
            return;
        }

        $text = "🎯 <b>Клик по контактам!</b>\n\n"
              . "<b>Канал связи:</b> {$channelName}\n"
              . "<b>Объявление:</b> <a href=\"{$listingUrl}\">" . htmlspecialchars($listing->title) . "</a>\n"
              . "<b>Адрес:</b> " . htmlspecialchars($listing->address ?? 'Минск') . "\n"
              . "<b>Устройство:</b> {$deviceLabel}\n"
              . "<b>Хозяин:</b> " . htmlspecialchars($host ? $host->name : 'Неизвестный') . " (" . htmlspecialchars($listing->phone ?? ($host->phone ?? 'Нет телефона')) . ")";

        if ($listing->isAgencyListing()) {
            $text = "<b>АГЕНТСТВО</b>\n\n" . $text;
        }

        $adminButtons = [
            'inline_keyboard' => [
                [
                    ['text' => '🔗 Открыть на сайте', 'url' => $listingUrl],
                    ['text' => '⚙️ Админка', 'url' => route('admin.dashboard')]
                ]
            ]
        ];

        $chatIds = array_filter(array_map('trim', explode(',', $adminChatIdString)));
        foreach ($chatIds as $chatId) {
            try {
                \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode($adminButtons),
                ]);
            } catch (\Exception $e) {
                \Log::warning('Failed to send Telegram contact click notification: ' . $e->getMessage());
            }
        }
    }

    /**
     * Личный кабинет арендодателя
     */
    public function dashboard()
    {
        if (!session('logged_in')) {
            return redirect()->route('login')->with('warning', 'Для доступа к кабинету требуется авторизация.');
        }

        $user = $this->ensureLoggedUser();
        $this->expireFeaturedListings();

        // Expired listings fallback checker
        try {
            $activeListings = Listing::where('user_id', $user->id)->where('status', 'active')->get();
            foreach ($activeListings as $listing) {
                if (!$listing->is_paid) {
                    $listing->status = 'pending_payment';
                    $listing->save();
                }
            }
        } catch (\Exception $e) {
            \Log::error('Dashboard expiration checker fallback error: ' . $e->getMessage());
        }

        // Fetch user's listings ordered by creation date with subscription details
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('user_tariffs')) {
                $listings = Listing::with('currentTariff')->where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
            } else {
                $listings = Listing::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
            }
        } catch (\Exception $e) {
            $listings = Listing::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        }

        // Calculate real time database statistics
        $listingIds = $listings->pluck('id');
        $allInteractions = \App\Models\ListingInteraction::whereIn('listing_id', $listingIds)->get();

        foreach ($listings as $listing) {
            $listingInteractions = $allInteractions->where('listing_id', $listing->id);
            
            $listing->real_views_count = $listingInteractions->where('interaction_type', 'view')->count();
            $listing->real_phone_count = $listingInteractions->where('interaction_type', 'phone')->count();
            $listing->real_telegram_count = $listingInteractions->where('interaction_type', 'telegram')->count();
            $listing->real_whatsapp_count = $listingInteractions->where('interaction_type', 'whatsapp')->count();
            $listing->real_viber_count = $listingInteractions->where('interaction_type', 'viber')->count();
            
            $listing->real_messengers_count = $listing->real_telegram_count + $listing->real_whatsapp_count + $listing->real_viber_count;
            $listing->real_contacts_count = $listing->real_phone_count + $listing->real_messengers_count;
            $listing->real_conversion_rate = $listing->real_views_count > 0 
                ? round(($listing->real_contacts_count / $listing->real_views_count) * 100, 1) 
                : 0;
        }

        $now = \Carbon\Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Aggregates for totals
        $allViews = $allInteractions->where('interaction_type', 'view');
        $allContacts = $allInteractions->whereIn('interaction_type', ['phone', 'whatsapp', 'telegram', 'viber']);
        $allMessengers = $allInteractions->whereIn('interaction_type', ['whatsapp', 'telegram', 'viber']);

        // Channel breakdown
        $channelsBreakdown = [
            'phone' => $allInteractions->where('interaction_type', 'phone')->count(),
            'telegram' => $allInteractions->where('interaction_type', 'telegram')->count(),
            'whatsapp' => $allInteractions->where('interaction_type', 'whatsapp')->count(),
            'viber' => $allInteractions->where('interaction_type', 'viber')->count(),
        ];

        // 1. Week chart (Monday to Sunday of current week with views + contacts)
        $weekInteractions = $allInteractions->filter(function($item) use ($startOfWeek, $endOfWeek) {
            return $item->created_at >= $startOfWeek && $item->created_at <= $endOfWeek;
        });
        $weekDays = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'];
        $weekData = [];
        foreach ($weekDays as $idx => $label) {
            $dayNum = $idx + 1; // 1 = Monday, 7 = Sunday
            $dayItems = $weekInteractions->filter(function($item) use ($dayNum) {
                return $item->created_at->isoFormat('E') == $dayNum;
            });
            $viewsCount = $dayItems->where('interaction_type', 'view')->count();
            $contactsCount = $dayItems->whereIn('interaction_type', ['phone', 'whatsapp', 'telegram', 'viber'])->count();

            $weekData[] = [
                'label' => $label,
                'val' => $viewsCount,
                'contacts' => $contactsCount
            ];
        }

        // 2. Month chart (Weeks of current month)
        $monthViews = $allViews->filter(function($item) use ($startOfMonth, $endOfMonth) {
            return $item->created_at >= $startOfMonth && $item->created_at <= $endOfMonth;
        });
        $monthData = [];
        $weeks = [
            ['label' => 'Нед 1', 'start' => 1, 'end' => 7],
            ['label' => 'Нед 2', 'start' => 8, 'end' => 14],
            ['label' => 'Нед 3', 'start' => 15, 'end' => 21],
            ['label' => 'Нед 4', 'start' => 22, 'end' => 31],
        ];
        foreach ($weeks as $w) {
            $count = $monthViews->filter(function($item) use ($w) {
                $day = $item->created_at->day;
                return $day >= $w['start'] && $day <= $w['end'];
            })->count();
            $monthData[] = ['label' => $w['label'], 'val' => $count];
        }

        // 3. All time chart (Previous year + current year months)
        $currentYear = $now->year;
        $prevYear = $currentYear - 1;
        $allData = [];

        $prevYearCount = $allViews->filter(function($item) use ($prevYear) {
            return $item->created_at->year == $prevYear;
        })->count();
        $allData[] = ['label' => (string)$prevYear, 'val' => $prevYearCount];

        $ruMonths = [
            1 => 'Янв', 2 => 'Фев', 3 => 'Мар', 4 => 'Апр', 5 => 'Май', 6 => 'Июн',
            7 => 'Июл', 8 => 'Авг', 9 => 'Сен', 10 => 'Окт', 11 => 'Ноя', 12 => 'Дек'
        ];
        for ($m = 1; $m <= $now->month; $m++) {
            $count = $allViews->filter(function($item) use ($currentYear, $m) {
                return $item->created_at->year == $currentYear && $item->created_at->month == $m;
            })->count();
            $allData[] = ['label' => $ruMonths[$m], 'val' => $count];
        }

        // 4. Hourly Views (last 24 hours of views grouped in 2 hour brackets)
        $hourlyData = [];
        $ranges = [
            ['label' => '0-2', 'start' => 0, 'end' => 1],
            ['label' => '2-4', 'start' => 2, 'end' => 3],
            ['label' => '4-6', 'start' => 4, 'end' => 5],
            ['label' => '6-8', 'start' => 6, 'end' => 7],
            ['label' => '8-10', 'start' => 8, 'end' => 9],
            ['label' => '10-12', 'start' => 10, 'end' => 11],
            ['label' => '12-14', 'start' => 12, 'end' => 13],
            ['label' => '14-16', 'start' => 14, 'end' => 15],
            ['label' => '16-18', 'start' => 16, 'end' => 17],
            ['label' => '18-20', 'start' => 18, 'end' => 19],
            ['label' => '20-22', 'start' => 20, 'end' => 21],
            ['label' => '22-24', 'start' => 22, 'end' => 23],
        ];
        foreach ($ranges as $r) {
            $count = $allViews->filter(function($item) use ($r) {
                $hour = $item->created_at->hour;
                return $hour >= $r['start'] && $hour <= $r['end'];
            })->count();
            $hourlyData[] = $count;
        }

        // 5. Device Split
        $mobileCount = $allViews->where('device_type', 'mobile')->count();
        $desktopCount = $allViews->where('device_type', 'desktop')->count();
        $tabletCount = $allViews->where('device_type', 'tablet')->count();

        // 6. Totals calculation
        $weekViewsCount = $allViews->filter(function($item) use ($startOfWeek, $endOfWeek) {
            return $item->created_at >= $startOfWeek && $item->created_at <= $endOfWeek;
        })->count();
        $weekContactsCount = $allContacts->filter(function($item) use ($startOfWeek, $endOfWeek) {
            return $item->created_at >= $startOfWeek && $item->created_at <= $endOfWeek;
        })->count();

        $monthViewsCount = $allViews->filter(function($item) use ($startOfMonth, $endOfMonth) {
            return $item->created_at >= $startOfMonth && $item->created_at <= $endOfMonth;
        })->count();
        $monthContactsCount = $allContacts->filter(function($item) use ($startOfMonth, $endOfMonth) {
            return $item->created_at >= $startOfMonth && $item->created_at <= $endOfMonth;
        })->count();

        $allViewsCount = $allViews->count();
        $allContactsCount = $allContacts->count();
        $overallConversionRate = $allViewsCount > 0 ? round(($allContactsCount / $allViewsCount) * 100, 1) : 0;

        $stats = [
            'totals' => [
                'week' => [
                    'views' => $weekViewsCount,
                    'messengers' => $weekContactsCount,
                ],
                'month' => [
                    'views' => $monthViewsCount,
                    'messengers' => $monthContactsCount,
                ],
                'all' => [
                    'views' => $allViewsCount,
                    'messengers' => $allContactsCount,
                ]
            ],
            'conversion_rate' => $overallConversionRate,
            'channels' => $channelsBreakdown,
            'charts' => [
                'week' => $weekData,
                'month' => $monthData,
                'all' => $allData,
            ],
            'hourly' => $hourlyData,
            'devices' => [
                'mobile' => $mobileCount,
                'desktop' => $desktopCount,
                'tablet' => $tabletCount,
            ]
        ];

        $telegramLinked = $user ? (!empty($user->getTelegramChatId())) : false;
        $telegramLink = 'https://t.me/' . env('TELEGRAM_BOT_USERNAME', 'FLATLYNOT_bot') . ($user ? '?start=bind_' . $user->id : '');

        $activeHomepageListingsCount = Listing::where('status', 'active')
            ->whereHas('userTariffs', function($query) {
                $query->where('tariff_id', 4)
                      ->where('status', 'active')
                      ->where('expires_at', '>', now());
            })
            ->count();

        return view('dashboard', compact('user', 'listings', 'stats', 'telegramLinked', 'telegramLink', 'activeHomepageListingsCount'));
    }

    /**
     * Форма создания объявления (Шаг 1 - 6)
     */
    public function create(Request $request)
    {
        if (!session('logged_in')) {
            return redirect()->route('login')->with('warning', 'Для размещения жилья требуется авторизация.');
        }
        $user = $this->ensureLoggedUser();
        
        $editId = $request->query('edit');
        $listing = null;
        if ($editId) {
            $listing = Listing::where('user_id', $user->id)->where('id', $editId)->first();
        }

        $telegramLinked = $user ? (!empty($user->getTelegramChatId())) : false;
        $telegramLink = 'https://t.me/' . env('TELEGRAM_BOT_USERNAME', 'FLATLYNOT_bot') . ($user ? '?start=bind_' . $user->id : '');

        return view('listings.create', compact('user', 'listing', 'telegramLinked', 'telegramLink'));
    }

    /**
     * Сохранение объявления в базу данных MySQL
     */
    public function store(Request $request)
    {
        $user = $this->ensureLoggedUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Авторизация не найдена.'], 401);
        }

        try {
            $data = $request->all();

            // Support edit mode if id is provided
            if (isset($data['id']) && !empty($data['id'])) {
                $listing = Listing::where('user_id', $user->id)->where('id', $data['id'])->first();
                if (!$listing) {
                    return response()->json(['success' => false, 'message' => 'Объявление не найдено или доступ ограничен.'], 404);
                }
            } else {
                $listing = new Listing();
                $listing->user_id = $user->id;
            }

            // Map frontend variables to database columns
            $listing->title = $data['title'] ?? 'Новое объявление';
            
            $type = $data['type'] ?? 'apartment';
            if (in_array($type, ['apartment', 'apartments', 'cottage', 'agrotourism'])) {
                $listing->type = $type;
            } else {
                $listing->type = 'apartment';
            }

            $listing->price = intval($data['price'] ?? 65);
            $listing->weekend_price = intval($data['weekend_price'] ?? 0);
            
            // Format address
            $city = $data['city'] ?? 'Минск';
            $street = $data['streetName'] ?? '';
            $streetType = $data['streetType'] ?? '';
            $house = $data['houseNumber'] ?? '';
            $corpus = $data['houseCorpus'] ?? '';
            
            $formattedAddress = $street;
            if ($streetType) $formattedAddress = "{$streetType} {$street}";
            if ($house) $formattedAddress = "{$city}, {$formattedAddress}, {$house}";
            if ($corpus) $formattedAddress = "{$formattedAddress}к{$corpus}";
            $listing->address = $formattedAddress ?: ($data['address'] ?? '');
            
            $listing->city = $city;
            $listing->district = $data['district'] ?? null;
            $listing->nearest_metro = $data['metro'] ?? 'Нет';
            $listing->area = intval($data['area'] ?? 35);
            
            // Additional parameters saving
            $listing->rooms = intval($data['rooms'] ?? 1);
            $listing->apartment_type = intval($listing->rooms) === 0 ? 'studio' : 'apartments';
            $listing->has_balcony = intval($data['has_balcony'] ?? 0);
            $listing->balcony_type = $data['balcony_type'] ?? null;
            if (($data['bathroom_type'] ?? '') === 'three_plus' || ($data['bathroom_type'] ?? '') === '3+') {
                $listing->bathrooms_count = 3;
                $listing->layout = '3+ санузла';
            } elseif (($data['bathroom_type'] ?? '') === 'two') {
                $listing->bathrooms_count = 2;
                $listing->layout = '2 санузла';
            } elseif (($data['bathroom_type'] ?? '') === 'separate') {
                $listing->bathrooms_count = 1;
                $listing->layout = 'Раздельный';
            } elseif (($data['bathroom_type'] ?? '') === 'outside') {
                $listing->bathrooms_count = 1;
                $listing->layout = 'На улице';
            } else {
                $listing->bathrooms_count = 1;
                $listing->layout = 'Совмещённый';
            }
            
            $listing->sleeping_places = intval($data['beds'] ?? 2);
            $listing->description = $data['desc'] ?? '';
            if (!empty($data['location_features']) && is_array($data['location_features'])) {
                $locNotes = [];
                if (in_array('В центре', $data['location_features']) && !str_contains(mb_strtolower($listing->description), 'центр')) {
                    $locNotes[] = 'в центре города';
                }
                if (in_array('Рядом с вокзалом', $data['location_features']) && !str_contains(mb_strtolower($listing->description), 'вокзал')) {
                    $locNotes[] = 'рядом с ж/д вокзалом';
                }
                if (!empty($locNotes)) {
                    $listing->description .= "\nРасположение: " . implode(', ', $locNotes) . '.';
                }
            }
            
            $isCottage = in_array($listing->type, ['cottage', 'agrotourism']);
            if ($isCottage) {
                $listing->plot_area = isset($data['plot_area']) && $data['plot_area'] !== '' ? floatval($data['plot_area']) : null;
                $listing->floors_in_house = isset($data['floors_in_house']) && $data['floors_in_house'] !== '' ? intval($data['floors_in_house']) : null;
                $listing->banquet_hall_capacity = isset($data['banquet_hall_capacity']) && $data['banquet_hall_capacity'] !== '' ? intval($data['banquet_hall_capacity']) : null;
                $listing->parking_type = $data['parking_type'] ?? null;
                $listing->cottage_material = $data['cottage_material'] ?? null;
                $listing->video_surveillance = $data['video_surveillance'] ?? [];
                $listing->neighborhood = $data['neighborhood'] ?? [];
                $listing->object_purpose = $data['object_purpose'] ?? [];
                
                // Amenities structure for cottages
                $listing->amenities = [
                    'Услуги на территории' => $data['cottage_amenity_territory'] ?? [],
                    'Климат и коммуникации' => $data['cottage_amenity_climate'] ?? [],
                    'Досуг и развлечения внутри' => $data['cottage_amenity_leisure'] ?? [],
                    'Сервис и услуги' => $data['cottage_amenity_service'] ?? [],
                    'Развлечения' => $data['entertainment'] ?? []
                ];
                
                $listing->floor = null;
                $listing->total_floors = null;
                $listing->building_material = null;
            } else {
                $listing->floor = isset($data['floor']) && $data['floor'] !== '' ? intval($data['floor']) : null;
                $listing->total_floors = isset($data['total_floors']) && $data['total_floors'] !== '' ? intval($data['total_floors']) : null;
                $listing->building_material = $data['building_material'] ?? null;
                
                $listing->plot_area = null;
                $listing->floors_in_house = null;
                $listing->banquet_hall_capacity = null;
                $listing->parking_type = null;
                $listing->cottage_material = null;
                $listing->video_surveillance = [];
                $listing->neighborhood = [];
                $listing->object_purpose = [];
                
                // Save structured amenities for apartments
                $listing->amenities = [
                    'Основные удобства' => $data['amenity_basic'] ?? [],
                    'Кухонная зона' => $data['amenity_kitchen'] ?? []
                ];
            }
            $listing->territory_features = $data['territory'] ?? [];
            
            // Rules & times
            $listing->check_in_time = $data['check_in_time'] ?? '14:00';
            $listing->check_out_time = $data['check_out_time'] ?? '12:00';
            $listing->children_allowed = intval($data['children_allowed'] ?? 0);
            $listing->animals_allowed = intval($data['animals_allowed'] ?? 0);
            $listing->events_allowed = intval($data['events_allowed'] ?? 0);
            $listing->communications = $data['checking_rules'] ?? []; // we store checking rules list and smoking status here
            $listing->renovation = json_encode($data['payments'] ?? ['Наличными']); // we store payment methods JSON array here
            
            // Host info update
            if ($user && isset($data['hostName'])) {
                $user->name = $data['hostName'];
                $user->save();
            }
            
            $listing->phone = $data['phone'] ?? $user->phone ?? '';
            $listing->telegram = $data['telegram'] ?? $user->telegram ?? '';
            $listing->viber = $data['viber'] ?? $user->viber ?? '';
            $listing->whatsapp = $data['whatsapp'] ?? $user->whatsapp ?? '';
            $listing->unp = $data['unp'] ?? $user->unp ?? '';
            
            // Handle photos (filesystem-backed storage with database path)
            $savedPhotos = [];
            if (isset($data['photos']) && is_array($data['photos']) && count($data['photos']) > 0) {
                foreach ($data['photos'] as $idx => $photoData) {
                    if (str_contains($photoData, ';base64,')) {
                        try {
                            $parts = explode(';base64,', $photoData);
                            $imageBase64 = base64_decode($parts[1]);
                            
                            $dir = storage_path('app/public/listings');
                            if (!file_exists($dir)) {
                                mkdir($dir, 0755, true);
                            }
                            
                            $srcImage = @imagecreatefromstring($imageBase64);
                            if ($srcImage) {
                                $width = imagesx($srcImage);
                                $height = imagesy($srcImage);
                                if ($width > 640) {
                                    $newWidth = 640;
                                    $newHeight = intval(($height / $width) * 640);
                                    $dstImage = imagecreatetruecolor($newWidth, $newHeight);
                                    
                                    // Preserve transparency
                                    imagealphablending($dstImage, false);
                                    imagesavealpha($dstImage, true);
                                    
                                    imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                                    $srcImage = $dstImage;
                                }
                                
                                $filename = 'listing_' . time() . '_' . uniqid() . '_' . $idx . '.webp';
                                imagewebp($srcImage, $dir . '/' . $filename, 60);
                                $savedPhotos[] = '/storage/listings/' . $filename;
                            } else {
                                // Fallback: save original if GD image creation fails
                                $imageTypeAux = explode("image/", $parts[0]);
                                $imageType = $imageTypeAux[1] ?? 'jpeg';
                                if ($imageType === 'jpeg') $imageType = 'jpg';
                                $filename = 'listing_' . time() . '_' . uniqid() . '_' . $idx . '.' . $imageType;
                                file_put_contents($dir . '/' . $filename, $imageBase64);
                                $savedPhotos[] = '/storage/listings/' . $filename;
                            }
                        } catch (\Exception $e) {
                            \Log::error('Error saving base64 image: ' . $e->getMessage());
                        }
                    } else if (strlen($photoData) > 4) {
                        // Preserve existing image path or pre-seeded URL
                        $savedPhotos[] = $photoData;
                    }
                }
            }

            if (count($savedPhotos) > 0) {
                $listing->image = $savedPhotos[0];
                $listing->photos = $savedPhotos;
            } else {
                $listing->image = '';
                $listing->photos = [];
            }

            $isNewListing = !$listing->exists;

            if (!$listing->exists) {
                $listing->featured = false;
            }

            // Если объявление уже оплачено, активируем его, иначе отправляем на оплату
            $listing->status = $listing->is_paid ? 'active' : 'pending_payment';
            
            // Use coords from frontend if provided, else fallback to Minsk center with small variance
            $listing->latitude = isset($data['latitude']) && !empty($data['latitude']) ? floatval($data['latitude']) : (53.902 + (rand(-100, 100) / 2000.0));
            $listing->longitude = isset($data['longitude']) && !empty($data['longitude']) ? floatval($data['longitude']) : (27.555 + (rand(-100, 100) / 2000.0));

            $listing->save();

            // Send Telegram admin notification about new listing creation
            $this->sendTelegramAdminListingNotification($listing, $user, $isNewListing);

            return response()->json([
                'success' => true, 
                'message' => 'Объявление успешно сохранено на сервере!',
                'listing' => $listing
            ]);
        } catch (\Exception $e) {
            \Log::error('Error storing listing: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Ошибка сохранения в БД: ' . $e->getMessage()], 500);
        }
    }

    private function sendTelegramAdminListingNotification(Listing $listing, $user, bool $isNew = true)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $adminChatIdString = env('TELEGRAM_ADMIN_CHAT_ID');

        if (!$botToken || !$adminChatIdString) {
            return;
        }

        $header = $isNew ? "<b>🆕 Новое объявление на сайте!</b>" : "<b>✏️ Обновление объявления!</b>";
        $typeTitle = in_array($listing->type, ['cottage', 'agrotourism']) ? '🏡 Коттедж / Усадьба' : '🏢 Квартира';
        $listingUrl = route('listings.show', ['id' => $listing->id, 'slug' => $listing->slug ?: 'listing']);
        $adminUrl = route('admin.dashboard');

        $text = "{$header}\n\n"
              . "<b>Категория:</b> {$typeTitle}\n"
              . "<b>Название:</b> " . htmlspecialchars($listing->title) . "\n"
              . "<b>Адрес:</b> " . htmlspecialchars($listing->address ?? 'Не указан') . "\n"
              . "<b>Цена:</b> " . round($listing->price) . " BYN / ночь\n\n"
              . "<b>Хозяин:</b> " . htmlspecialchars($user ? $user->name : 'Неизвестный') . " (" . htmlspecialchars($listing->phone ?? ($user ? $user->phone : 'Нет телефона')) . ")\n"
              . "<b>УНП:</b> " . htmlspecialchars($listing->unp ?: ($user->unp ?? 'Отсутствует'));

        if ($listing->isAgencyListing()) {
            $text = "<b>АГЕНТСТВО</b>\n\n" . $text;
        }

        $adminButtons = [
            'inline_keyboard' => [
                [
                    ['text' => '🔗 На сайте', 'url' => $listingUrl],
                    ['text' => '⚙️ В админ-панель', 'url' => $adminUrl]
                ]
            ]
        ];

        $chatIds = array_filter(array_map('trim', explode(',', $adminChatIdString)));
        foreach ($chatIds as $chatId) {
            try {
                \Illuminate\Support\Facades\Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode($adminButtons),
                ]);
            } catch (\Exception $e) {
                \Log::warning('Failed to send Telegram admin listing notification: ' . $e->getMessage());
            }
        }
    }

    public function saveProfile(Request $request)
    {
        $user = $this->ensureLoggedUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Авторизация не найдена.'], 401);
        }

        try {
            $data = $request->all();
            
            $user->name = $data['name'] ?? $user->name;
            $user->phone = $data['phone'] ?? $user->phone;
            $user->telegram = $data['telegram'] ?? $user->telegram;
            $user->viber = $data['viber'] ?? $user->viber;
            $user->whatsapp = $data['whatsapp'] ?? $user->whatsapp;
            $user->unp = $data['unp'] ?? $user->unp;
            
            if (isset($data['avatar'])) {
                $user->avatar = $data['avatar'];
            }

            $user->save();

            // Auto-unlock rejected listings for this host if UNP is provided!
            if (!empty(trim($user->unp))) {
                Listing::where('user_id', $user->id)
                    ->where('status', 'rejected')
                    ->update([
                        'status' => 'active',
                        'unp' => $user->unp
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Профиль арендодателя успешно обновлен в БД!',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Ошибка сохранения профиля: ' . $e->getMessage()], 500);
        }
    }

    public function toggleStatus($id)
    {
        $user = $this->ensureLoggedUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Доступ ограничен.'], 401);
        }

        try {
            $listing = Listing::where('user_id', $user->id)->where('id', $id)->first();
            if (!$listing) {
                return response()->json(['success' => false, 'message' => 'Объявление не найдено.'], 404);
            }

            if ($listing->status === 'active') {
                $listing->status = 'hidden';
                $listing->save();
            } else {
                // Verify subscription is active before publishing
                if ($listing->is_paid) {
                    $listing->status = 'active';
                    $listing->save();
                } else {
                    return response()->json([
                        'success' => false,
                        'requires_payment' => true,
                        'message' => 'Для активации этого объявления требуется оплатить размещение.'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'status' => $listing->status,
                'message' => 'Статус объявления успешно изменен!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function expireFeaturedListings()
    {
        try {
            Listing::where('featured', true)
                ->whereDoesntHave('userTariffs', function($query) {
                    $query->where('tariff_id', 3)
                          ->where('status', 'active')
                          ->where('expires_at', '>', now());
                })
                ->update(['featured' => false]);
        } catch (\Exception $e) {
            \Log::error('Failed to expire featured listings: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        $user = $this->ensureLoggedUser();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Доступ ограничен.'], 401);
        }

        try {
            $listing = Listing::where('user_id', $user->id)->where('id', $id)->first();
            if (!$listing) {
                return response()->json(['success' => false, 'message' => 'Объявление не найдено.'], 404);
            }

            $listing->delete(); // SoftDelete

            return response()->json([
                'success' => true,
                'message' => 'Объявление успешно удалено из БД!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function ensureLoggedUser()
    {
        if (session('logged_in')) {
            if (session('user_id')) {
                $user = \App\Models\User::find(session('user_id'));
                if ($user) {
                    return $user;
                }
            }

            $user = \App\Models\User::where('email', 'natalia@flatly.by')->first();
            if (!$user) {
                $user = new \App\Models\User();
                $user->name = 'Наталья';
                $user->email = 'natalia@flatly.by';
                $user->password = bcrypt('password');
                $user->phone = '+375 29 688-38-98';
                $user->telegram = '@natalia_rent';
                $user->viber = '+375 29 688-38-98';
                $user->unp = '';
                $user->avatar = '';
                $user->save();
            }
            
            session(['user_id' => $user->id]);
            return $user;
        }
        return null;
    }

    private function seedDatabaseIfNeeded()
    {
        try {
            if (\App\Models\Listing::count() === 0) {
                // Fetch or create Natalia User
                $user = \App\Models\User::where('email', 'natalia@flatly.by')->first();
                if (!$user) {
                    $user = new \App\Models\User();
                    $user->name = 'Наталья';
                    $user->email = 'natalia@flatly.by';
                    $user->password = bcrypt('password');
                    $user->phone = '+375 29 688-38-98';
                    $user->telegram = '@natalia_rent';
                    $user->viber = '+375 29 688-38-98';
                    $user->unp = '';
                    $user->avatar = '';
                    $user->save();
                }

                $mockListings = [
                    [
                        'id' => 1,
                        'user_id' => $user->id,
                        'type' => 'apartment',
                        'title' => 'Видовая атмосферная квартира на 20 этаже',
                        'address' => 'Минск, Белградская, 8',
                        'nearest_metro' => 'Аэродромная',
                        'price' => 150,
                        'weekend_price' => 100,
                        'area' => 35,
                        'floor' => 20,
                        'total_floors' => 25,
                        'sleeping_places' => 2,
                        'unp' => '',
                        'phone' => '+375 29 688-38-98',
                        'telegram' => '@natalia_rent',
                        'viber' => '+375 29 688-38-98',
                        'whatsapp' => '+375296883898',
                        'status' => 'active',
                        'latitude' => 53.919,
                        'longitude' => 27.579,
                        'description' => "Атмосферная квартира на 20-ом этаже. В квартире есть все необходимое для комфортного проживания, как на короткий срок, так и на длительный.",
                        'amenities' => [
                            'Основные удобства' => ['Гладильная доска', 'Стиральная машина', 'Утюг', 'Шторы блэкаут'],
                            'Кухонная зона' => ['Холодильник', 'Электрочайник', 'Посуда']
                        ],
                        'views_count' => 1248,
                        'contacts_count' => 142,
                    ],
                    [
                        'id' => 5,
                        'user_id' => $user->id,
                        'type' => 'apartment',
                        'title' => 'Панорамная 2-комнатная квартира у Библиотеки',
                        'address' => 'Минск, Независимости, 119',
                        'nearest_metro' => 'Восток',
                        'price' => 58,
                        'weekend_price' => 35,
                        'area' => 55,
                        'floor' => 14,
                        'total_floors' => 19,
                        'sleeping_places' => 2,
                        'unp' => '',
                        'phone' => '+375 29 688-38-98',
                        'telegram' => '@natalia_rent',
                        'viber' => '+375 29 688-38-98',
                        'whatsapp' => '+375296883898',
                        'status' => 'pending',
                        'latitude' => 53.931,
                        'longitude' => 27.646,
                        'description' => "Просторная современная квартира с панорамным видом на Национальную библиотеку. Отличный ремонт, кондиционер, две полноценные раздельные комнаты.",
                        'amenities' => [
                            'Основные удобства' => ['Гладильная доска', 'Стиральная машина', 'Утюг', 'Шторы блэкаут'],
                            'Кухонная зона' => ['Холодильник', 'Электрочайник', 'Посуда']
                        ],
                        'views_count' => 412,
                        'contacts_count' => 39,
                    ],
                    [
                        'id' => 3,
                        'user_id' => $user->id,
                        'type' => 'apartment',
                        'title' => 'Уютный загородный домик у леса',
                        'address' => 'Лысая Гора, Минский р-н',
                        'nearest_metro' => 'Нет',
                        'price' => 180,
                        'weekend_price' => 120,
                        'area' => 120,
                        'floor' => 1,
                        'total_floors' => 2,
                        'sleeping_places' => 4,
                        'unp' => '',
                        'phone' => '+375 29 688-38-98',
                        'telegram' => '@natalia_rent',
                        'viber' => '+375 29 688-38-98',
                        'whatsapp' => '+375296883898',
                        'status' => 'rejected',
                        'latitude' => 54.020,
                        'longitude' => 27.420,
                        'description' => "Загородная усадьба премиум-класса на опушке соснового бора. Огромная закрытая территория, отсутствие соседей в радиусе 100 метров.",
                        'amenities' => [
                            'Основные удобства' => ['Гладильная доска', 'Стиральная машина', 'Утюг', 'Шторы блэкаут'],
                            'Кухонная зона' => ['Холодильник', 'Электрочайник', 'Посуда']
                        ],
                        'views_count' => 0,
                        'contacts_count' => 0,
                    ]
                ];

                foreach ($mockListings as $ml) {
                    $listing = new \App\Models\Listing();
                    $listing->id = $ml['id'];
                    $listing->user_id = $ml['user_id'];
                    $listing->type = $ml['type'];
                    $listing->title = $ml['title'];
                    $listing->address = $ml['address'];
                    $listing->nearest_metro = $ml['nearest_metro'];
                    $listing->price = $ml['price'];
                    $listing->weekend_price = $ml['weekend_price'];
                    $listing->area = $ml['area'];
                    $listing->floor = $ml['floor'];
                    $listing->total_floors = $ml['total_floors'];
                    $listing->sleeping_places = $ml['sleeping_places'];
                    $listing->unp = $ml['unp'];
                    $listing->telegram = $ml['telegram'];
                    $listing->viber = $ml['viber'];
                    $listing->whatsapp = $ml['whatsapp'];
                    $listing->status = $ml['status'];
                    $listing->latitude = $ml['latitude'];
                    $listing->longitude = $ml['longitude'];
                    $listing->description = $ml['description'];
                    $listing->amenities = $ml['amenities'];
                    $listing->views_count = $ml['views_count'];
                    $listing->contacts_count = $ml['contacts_count'];
                    $listing->save();
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error seeding inside index: ' . $e->getMessage());
        }
    }

    /**
     * Возвращает фейковые/заглушечные объявления для работы сайта без подключенной БД
     */
    private function getMockListing($id)
    {
        $mockDatabase = [
            1 => [
                'id' => 1,
                'title' => 'Видовая атмосферная квартира на 20 этаже',
                'address' => 'Белградская, 8, Минск',
                'nearest_metro' => 'Аэродромная',
                'price' => '150',
                'weekend_price' => '100',
                'area' => '35',
                'floor' => '20',
                'total_floors' => '25',
                'guests' => '3',
                'sleeping_places' => '2',
                'type' => 'студия',
                'host_name' => 'Наталья',
                'phone' => '+375 29 688-38-98',
                'telegram' => 'https://t.me/olga_komarovka',
                'viber' => 'viber://chat?number=%2B375296883898',
                'whatsapp' => 'https://wa.me/375296883898',
                'latitude' => 53.919,
                'longitude' => 27.579,
                'description' => "Атмосферная квартира на 20-ом этаже. В квартире есть все необходимое для комфортного проживания, как на короткий срок, так и на длительный. Сдается на часы-сутки-недели-месяцы. Развитая инфраструктура района. Чистота и порядок, свежее белье и пушистые полотенца. Два Smart TV. Цена в шапке объявления указана ориентировочная. Цена зависит от количества проживающих человек, от срока аренды. Точную стоимость уточняйте в любом удобном мессенджере или по звонку.",
                'chars' => [
                    'bathroom' => 'Совмещённый',
                    'balcony' => 'Есть терраса',
                    'parking' => 'Бесплатная',
                    'view' => 'На улицу/проспект',
                    'territory' => ['Спортивная площадка', 'Зарядка для электромобилей', 'Лифт', 'Консьерж']
                ],
                'beds' => [
                    ['name' => 'Двуспальная широкая (king-size)', 'count' => 1, 'icon' => '🛏️'],
                    ['name' => 'Диван-кровать', 'count' => 1, 'icon' => '🛋️']
                ],
                'amenities' => [
                    'Основные удобства' => ['Гладильная доска', 'Стиральная машина', 'Утюг', 'Шторы блэкаут', 'Ортопедическая кровать', 'Сушилка для белья', 'Шкаф для одежды'],
                    'Кухонная зона' => ['Индукционная плита', 'Микроволновка', 'Обеденный стол', 'Холодильник', 'Электрочайник', 'Посуда и приборы'],
                    'Ванная комната' => ['Ванна', 'Фен', 'Полотенцесушитель']
                ],
                'rules' => [
                    'checkin' => 'с 14:00',
                    'checkout' => 'до 12:00',
                    'hosting' => [
                        ['text' => 'Нельзя с детьми', 'ok' => false],
                        ['text' => 'Разрешено курить', 'ok' => true],
                        ['text' => 'Мероприятия запрещены', 'ok' => false],
                        ['text' => 'С животными нельзя', 'ok' => false]
                    ],
                    'checking' => [
                        ['text' => 'Бесконтактное заселение', 'ok' => true],
                        ['text' => 'Круглосуточное заселение', 'ok' => true],
                        ['text' => 'Отчётные документы', 'ok' => true],
                        ['text' => 'Ранний заезд по согласованию', 'ok' => true],
                        ['text' => 'Поздний выезд по согласованию', 'ok' => true],
                        ['text' => 'Предоплата обязательна', 'ok' => true],
                        ['text' => 'Залог обязателен', 'ok' => true],
                        ['text' => 'Нужен документ, удостоверяющий личность', 'ok' => true]
                    ]
                ],
                'payments' => ['Наличными', 'ЕРИП', 'Иной'],
                'reviews' => [
                    ['name' => 'Гость', 'date' => '01.06.2026', 'elapsed' => '4 часа назад', 'rating' => 5, 'content' => 'Понравилось'],
                    ['name' => 'Евгений', 'date' => '28.05.2026', 'elapsed' => '3 дня назад', 'rating' => 5, 'content' => 'Отличная квартира']
                ],
                'photos' => [
                    ['bg' => 'linear-gradient(140deg,#7B1FA2,#E040FB)', 'emoji' => '💜'],
                    ['bg' => 'linear-gradient(140deg,#2C3E50,#3D5A6B)', 'emoji' => '🏢'],
                    ['bg' => 'linear-gradient(140deg,#34495E,#5D6D7E)', 'emoji' => '🛋️'],
                    ['bg' => 'linear-gradient(140deg,#1F3A52,#2B4C7E)', 'emoji' => '🍳'],
                    ['bg' => 'linear-gradient(140deg,#1A2B3C,#34495E)', 'emoji' => '🚿']
                ]
            ],
            2 => [
                'id' => 2,
                'title' => 'Уютная дизайнерская студия в самом центре',
                'address' => 'Интернациональная, 12, Минск',
                'nearest_metro' => 'Немига',
                'price' => '72',
                'weekend_price' => '50',
                'area' => '35',
                'floor' => '4',
                'total_floors' => '5',
                'guests' => '2',
                'sleeping_places' => '1',
                'type' => 'студия',
                'host_name' => 'Дмитрий',
                'phone' => '+375 44 777-88-99',
                'telegram' => 'https://t.me/dima_minsk_studio',
                'viber' => 'viber://chat?number=%2B375447778899',
                'whatsapp' => 'https://wa.me/375447778899',
                'latitude' => 53.902,
                'longitude' => 27.555,
                'description' => 'Дизайнерская студия на пешеходной улице Интернациональной. Историческая застройка, высокие потолки, свежий ремонт 2026 года. Вся мебель изготовлена под заказ из натурального дерева. Имеется кофемашина Nespresso, СВЧ, индукционная варочная панель, фильтр для воды. Мягкая кровать с ортопедическим матрасом гарантирует отличный сон. Идеальное расположение для туристов и командировок: ратуша, сквер, набережная Свислочи в 3 минутах пешком.',
                'chars' => [
                    'bathroom' => 'Совмещённый',
                    'balcony' => 'Нет',
                    'parking' => 'Бесплатная во дворе',
                    'view' => 'Во двор (тихий)',
                    'territory' => ['Видеонаблюдение', 'Велопарковка', 'Пандус', 'Домофон']
                ],
                'beds' => [
                    ['name' => 'Двуспальная широкая (king-size)', 'count' => 1, 'icon' => '🛏️']
                ],
                'amenities' => [
                    'Основные удобства' => ['Гладильная доска', 'Стиральная машина', 'Утюг', 'Шторы блэкаут', 'Посудомоечная машина'],
                    'Кухонная зона' => ['Индукционная плита', 'Микроволновка', 'Кофемашина', 'Холодильник', 'Электрочайник', 'Посуда и приборы']
                ],
                'rules' => [
                    'checkin' => 'с 15:00',
                    'checkout' => 'до 12:00',
                    'hosting' => [
                        ['text' => 'Можно с детьми', 'ok' => true],
                        ['text' => 'Курить запрещено', 'ok' => false],
                        ['text' => 'Мероприятия запрещены', 'ok' => false],
                        ['text' => 'С животными нельзя', 'ok' => false]
                    ],
                    'checking' => [
                        ['text' => 'Бесконтактное заселение', 'ok' => true],
                        ['text' => 'Отчётные документы', 'ok' => true],
                        ['text' => 'Предоплата обязательна', 'ok' => true]
                    ]
                ],
                'payments' => ['Наличными', 'Банковской картой', 'ЕРИП'],
                'reviews' => [
                    ['name' => 'Мария', 'date' => '30.05.2026', 'elapsed' => '2 дня назад', 'rating' => 5, 'content' => 'Прекрасная студия! Идеальная чистота и супер-локация.']
                ],
                'photos' => [
                    ['bg' => 'linear-gradient(140deg,#3D2B1A,#6B4A2A)', 'emoji' => '🛋️'],
                    ['bg' => 'linear-gradient(140deg,#4A3319,#7C5832)', 'emoji' => '🍳'],
                    ['bg' => 'linear-gradient(140deg,#553F28,#8E6E4D)', 'emoji' => '🚿'],
                    ['bg' => 'linear-gradient(140deg,#2D1E10,#4E361B)', 'emoji' => '🛏️'],
                    ['bg' => 'linear-gradient(140deg,#3A2412,#5B3B1A)', 'emoji' => '🚪']
                ]
            ],
            3 => [
                'id' => 3,
                'title' => 'Уютная студия у вокзала — почасово',
                'address' => 'Кирова, 2, Минск',
                'nearest_metro' => 'Площадь Ленина',
                'price' => '45',
                'weekend_price' => '12',
                'area' => '30',
                'floor' => '2',
                'total_floors' => '5',
                'guests' => '2',
                'sleeping_places' => '1',
                'type' => 'почасово',
                'host_name' => 'Игорь',
                'phone' => '+375 33 666-55-44',
                'telegram' => 'https://t.me/igor_station_rent',
                'viber' => 'viber://chat?number=%2B375336665544',
                'whatsapp' => 'https://wa.me/375336665544',
                'latitude' => 53.890,
                'longitude' => 27.550,
                'description' => 'Комфортабельное жилье прямо напротив железнодорожного вокзала. Идеально для отдыха во время транзита, командировок или романтических встреч. Сдается на часы и сутки. Минимальный заказ на часы — 2 часа. Всегда идеально выглаженное белье, свежие полотенца, чай и кофе входят в стоимость. Имеется кондиционер, бойлер, холодильник, посуда. Быстрое и конфиденциальное заселение 24/7.',
                'chars' => [
                    'bathroom' => 'Совмещённый',
                    'balcony' => 'Нет',
                    'parking' => 'Платная рядом',
                    'view' => 'На улицу/проспект',
                    'territory' => ['Видеонаблюдение', 'Лифт', 'Домофон']
                ],
                'beds' => [
                    ['name' => 'Диван-кровать', 'count' => 1, 'icon' => '🛋️']
                ],
                'amenities' => [
                    'Основные удобства' => ['Гладильная доска', 'Стиральная машина', 'Утюг', 'Бойлер', 'Кондиционер'],
                    'Кухонная зона' => ['Электроплита', 'Микроволновка', 'Холодильник', 'Электрочайник', 'Посуда и приборы']
                ],
                'rules' => [
                    'checkin' => 'Круглосуточно',
                    'checkout' => 'по согласованию',
                    'hosting' => [
                        ['text' => 'Нельзя с детьми', 'ok' => false],
                        ['text' => 'Курить запрещено', 'ok' => false],
                        ['text' => 'Мероприятия запрещены', 'ok' => false]
                    ],
                    'checking' => [
                        ['text' => 'Круглосуточное заселение', 'ok' => true],
                        ['text' => 'Бесконтактное заселение', 'ok' => true],
                        ['text' => 'Отчётные документы', 'ok' => true]
                    ]
                ],
                'payments' => ['Наличными', 'ЕРИП'],
                'reviews' => [
                    ['name' => 'Алексей', 'date' => '25.05.2026', 'elapsed' => '1 неделя назад', 'rating' => 5, 'content' => 'Чисто, аккуратно, заселили за 5 минут после звонка.']
                ],
                'photos' => [
                    ['bg' => 'linear-gradient(140deg,#1A2D3D,#2A4A5C)', 'emoji' => '🏙️'],
                    ['bg' => 'linear-gradient(140deg,#2A3E52,#3D5F76)', 'emoji' => '🛋️'],
                    ['bg' => 'linear-gradient(140deg,#1F3144,#2D4862)', 'emoji' => '🚿'],
                    ['bg' => 'linear-gradient(140deg,#2A3A4D,#3D5470)', 'emoji' => '🚪'],
                    ['bg' => 'linear-gradient(140deg,#12202E,#1D3249)', 'emoji' => '🛏️']
                ]
            ],
            4 => [
                'id' => 4,
                'title' => 'Коттедж с баней, купелью и мангалом у леса',
                'address' => 'Лысая Гора, Минский р-н',
                'nearest_metro' => 'Уручье (20 мин)',
                'price' => '180',
                'weekend_price' => '120',
                'area' => '120',
                'floor' => '1',
                'total_floors' => '2',
                'guests' => '8',
                'sleeping_places' => '4',
                'type' => 'коттедж',
                'host_name' => 'Сергей',
                'phone' => '+375 44 999-00-11',
                'telegram' => 'https://t.me/sergey_cottage',
                'viber' => 'viber://chat?number=%2B375449990011',
                'whatsapp' => 'https://wa.me/375449990011',
                'latitude' => 54.020,
                'longitude' => 27.420,
                'description' => 'Загородная усадьба премиум-класса на опушке соснового бора. Огромная закрытая территория, отсутствие соседей в радиусе 100 метров. Просторная гостиная с настоящим камином на дровах, караоке-система, Смарт ТВ. На участке построена отдельно стоящая баня на дровах с ледяной купелью и просторной комнатой отдыха. Крытая барбекю-зона со всеми принадлежностями. Комфортное размещение спальных мест до 8 человек (3 отдельные спальни + раскладной диван в гостиной). Прекрасно подходит для семейного отдыха или корпоративов.',
                'chars' => [
                    'bathroom' => 'Раздельные санузлы (2 шт.)',
                    'balcony' => 'Терраса и балкон',
                    'parking' => 'Охраняемая на территории',
                    'view' => 'На лес и сад',
                    'territory' => ['Мангал', 'Детская площадка', 'Баня', 'Купель', 'Беседка', 'Домофон', 'Видеонаблюдение по периметру']
                ],
                'beds' => [
                    ['name' => 'Двуспальная широкая (king-size)', 'count' => 3, 'icon' => '🛏️'],
                    ['name' => 'Диван-кровать', 'count' => 1, 'icon' => '🛋️']
                ],
                'amenities' => [
                    'Основные удобства' => ['Гладильная доска', 'Стиральная машина', 'Утюг', 'Камин на дровах', 'Караоке и ТВ', 'Сауна'],
                    'Кухонная зона' => ['Электроплита', 'Духовой шкаф', 'Микроволновка', 'Посудомоечная машина', 'Холодильник', 'Посуда и приборы'],
                    'Ванная комната' => ['Душевая кабина', 'Фен', 'Полотенцесушитель']
                ],
                'rules' => [
                    'checkin' => 'с 14:00',
                    'checkout' => 'до 12:00',
                    'hosting' => [
                        ['text' => 'Можно с детьми', 'ok' => true],
                        ['text' => 'Разрешено курить на улице', 'ok' => true],
                        ['text' => 'Мероприятия разрешены', 'ok' => true],
                        ['text' => 'С животными по согласованию', 'ok' => true]
                    ],
                    'checking' => [
                        ['text' => 'Отчётные документы', 'ok' => true],
                        ['text' => 'Предоплата обязательна', 'ok' => true],
                        ['text' => 'Залог обязателен', 'ok' => true]
                    ]
                ],
                'payments' => ['Наличными', 'Банковской картой', 'Безналичный расчет'],
                'reviews' => [
                    ['name' => 'Святослав', 'date' => '20.05.2026', 'elapsed' => '1 неделя назад', 'rating' => 5, 'content' => 'Потрясающее место! Баня просто космос, камин создает невероятный уют. Обязательно вернемся.']
                ],
                'photos' => [
                    ['bg' => 'linear-gradient(140deg,#1B3A2D,#2E6B4F)', 'emoji' => '🏡'],
                    ['bg' => 'linear-gradient(140deg,#25503C,#3C8D67)', 'emoji' => '🪵'],
                    ['bg' => 'linear-gradient(140deg,#2D664E,#46A67E)', 'emoji' => '🛋️'],
                    ['bg' => 'linear-gradient(140deg,#183327,#28553F)', 'emoji' => '🛏️'],
                    ['bg' => 'linear-gradient(140deg,#1B4734,#2A6D4F)', 'emoji' => '🪓']
                ]
            ],
            5 => [
                'id' => 5,
                'title' => 'Панорамная 2-комнатная квартира у Библиотеки',
                'address' => 'Независимости, 119, Минск',
                'nearest_metro' => 'Восток',
                'price' => '58',
                'weekend_price' => '35',
                'area' => '55',
                'floor' => '14',
                'total_floors' => '19',
                'guests' => '4',
                'sleeping_places' => '2',
                'type' => 'квартира',
                'host_name' => 'Наталья',
                'phone' => '+375 25 444-33-22',
                'telegram' => 'https://t.me/natalia_rent_library',
                'viber' => 'viber://chat?number=%2B375254443322',
                'whatsapp' => 'https://wa.me/375254443322',
                'latitude' => 53.931,
                'longitude' => 27.646,
                'description' => 'Просторная современная квартира с панорамным видом на Национальную библиотеку. Отличный ремонт, кондиционер, две полноценные раздельные комнаты. Метро "Восток" находится прямо во дворе дома. В пешей доступности один из крупнейших ТРЦ страны "Dana Mall". Полностью укомплектованная кухня, стиральная машина, высокоскоростной Wi-Fi, пастельное белье премиум-класса. Квартира сдается на сутки, часы и недели.',
                'chars' => [
                    'bathroom' => 'Раздельный',
                    'balcony' => 'Есть лоджия',
                    'parking' => 'Бесплатная во дворе',
                    'view' => 'Панорамный на Национальную библиотеку',
                    'territory' => ['Детская площадка', 'Велопарковка', 'Два лифта', 'Домофон', 'Пандус']
                ],
                'beds' => [
                    ['name' => 'Двуспальная широкая (king-size)', 'count' => 1, 'icon' => '🛏️'],
                    ['name' => 'Диван-кровать', 'count' => 1, 'icon' => '🛋️']
                ],
                'amenities' => [
                    'Основные удобства' => ['Гладильная доска', 'Стиральная машина', 'Утюг', 'Шторы блэкаут', 'Кондиционер'],
                    'Кухонная зона' => ['Электроплита', 'Микроволновка', 'Обеденный стол', 'Холодильник', 'Электрочайник', 'Посуда и приборы'],
                    'Ванная комната' => ['Ванна', 'Фен', 'Полотенцесушитель']
                ],
                'rules' => [
                    'checkin' => 'с 14:00',
                    'checkout' => 'до 12:00',
                    'hosting' => [
                        ['text' => 'Можно с детьми', 'ok' => true],
                        ['text' => 'Курить запрещено', 'ok' => false],
                        ['text' => 'Мероприятия запрещены', 'ok' => false],
                        ['text' => 'С животными нельзя', 'ok' => false]
                    ],
                    'checking' => [
                        ['text' => 'Бесконтактное заселение', 'ok' => true],
                        ['text' => 'Отчётные документы', 'ok' => true]
                    ]
                ],
                'payments' => ['Наличными', 'Банковской картой', 'ЕРИП'],
                'reviews' => [
                    ['name' => 'Ирина', 'date' => '21.05.2026', 'elapsed' => '1 неделю назад', 'rating' => 5, 'content' => 'Вид из окна просто шикарный! Квартира светлая, чистая, очень уютная. Метро рядом.']
                ],
                'photos' => [
                    ['bg' => 'linear-gradient(140deg,#2D1A3D,#4A2A6B)', 'emoji' => '🏠'],
                    ['bg' => 'linear-gradient(140deg,#3A224E,#5F378A)', 'emoji' => '🛋️'],
                    ['bg' => 'linear-gradient(140deg,#462A5E,#7443A8)', 'emoji' => '🍳'],
                    ['bg' => 'linear-gradient(140deg,#221330,#3A2054)', 'emoji' => '🚿'],
                    ['bg' => 'linear-gradient(140deg,#2C1542,#4A286E)', 'emoji' => '🛏️']
                ]
            ],
            6 => [
                'id' => 6,
                'title' => 'Коттедж «Лясны кут» в Налибокской пуще',
                'address' => 'хутор Налибоки, Воложинский р-н',
                'nearest_metro' => null,
                'price' => '110',
                'weekend_price' => '80',
                'area' => '90',
                'floor' => '1',
                'total_floors' => '2',
                'guests' => '6',
                'sleeping_places' => '4',
                'type' => 'коттедж',
                'host_name' => 'Александр',
                'phone' => '+375 29 111-22-33',
                'telegram' => 'https://t.me/alex_forest_man',
                'viber' => 'viber://chat?number=%2B375291112233',
                'whatsapp' => 'https://wa.me/375291112233',
                'latitude' => 53.900,
                'longitude' => 26.700,
                'description' => 'Уединенная деревянная усадьба на самом краю заповедной Налибокской пущи. Уникальное место для побега от городской суеты, воссоединения с природой и полного расслабления. Дом полностью построен из натурального сруба вручную, внутри потрясающий сосновый воздух. На просторной территории в 50 соток: русская баня на дровах, чистый зарыбленный пруд, в котором можно купаться, уютная беседка-альтанка с мангалом, сеновал для ночевки под открытым небом. Рядом чистейшая лесная река. Размещение до 6 человек.',
                'chars' => [
                    'bathroom' => 'Совмещённый санузел в доме',
                    'balcony' => 'Открытая терраса',
                    'parking' => 'Бесплатная на территории',
                    'view' => 'На лес и пруд',
                    'territory' => ['Русская баня', 'Зарыбленный пруд', 'Беседка', 'Мангал с дровами', 'Видеонаблюдение на участке']
                ],
                'beds' => [
                    ['name' => 'Двуспальная кровать', 'count' => 2, 'icon' => '🛏️'],
                    ['name' => 'Односпальная кровать', 'count' => 2, 'icon' => '🛌']
                ],
                'amenities' => [
                    'Основные удобства' => ['Камин на дровах', 'Стиральная машина', 'Русская баня', 'Беседка-барбекю', 'Прокат велосипедов'],
                    'Кухонная зона' => ['Электроплита', 'Микроволновка', 'Холодильник', 'Электрочайник', 'Посуда и приборы'],
                    'Ванная комната' => ['Душевая кабина', 'Фен']
                ],
                'rules' => [
                    'checkin' => 'с 14:00',
                    'checkout' => 'до 12:00',
                    'hosting' => [
                        ['text' => 'Можно с детьми', 'ok' => true],
                        ['text' => 'Разрешено курить на улице', 'ok' => true],
                        ['text' => 'Мероприятия разрешены', 'ok' => true],
                        ['text' => 'С животными разрешено', 'ok' => true]
                    ],
                    'checking' => [
                        ['text' => 'Круглосуточное заселение', 'ok' => true],
                        ['text' => 'Предоплата обязательна', 'ok' => true]
                    ]
                ],
                'payments' => ['Наличными', 'Банковской картой', 'ЕРИП'],
                'reviews' => [
                    ['name' => 'Валерия', 'date' => '18.05.2026', 'elapsed' => '2 недели назад', 'rating' => 5, 'content' => 'Абсолютная тишина, только пение птиц. Баня шикарная, пруд чистый. Отдохнули телом и душой!']
                ],
                'photos' => [
                    ['bg' => 'linear-gradient(140deg,#3D2A1A,#6B4A30)', 'emoji' => '🪵'],
                    ['bg' => 'linear-gradient(140deg,#4D3521,#7C5539)', 'emoji' => '🏡'],
                    ['bg' => 'linear-gradient(140deg,#5E4129,#8D6142)', 'emoji' => '🪓'],
                    ['bg' => 'linear-gradient(140deg,#2D1E12,#4E3422)', 'emoji' => '🛏️'],
                    ['bg' => 'linear-gradient(140deg,#3A2617,#5B3D25)', 'emoji' => '🪵']
                ]
            ]
        ];

        if (!isset($mockDatabase[$id])) {
            return null;
        }

        return (object) $mockDatabase[$id];
    }

    public function favoritesPage(Request $request)
    {
        $this->ensureLoggedUser();
        $this->expireFeaturedListings();

        $idsString = $request->query('ids');

        if ($idsString === null) {
            return view('listings.favorites', ['listings' => collect(), 'redirect' => true]);
        }

        if (trim($idsString) === '') {
            return view('listings.favorites', ['listings' => collect(), 'redirect' => false]);
        }

        $ids = array_filter(array_map('intval', explode(',', $idsString)));

        $listings = collect();
        if (!empty($ids)) {
            try {
                $listings = Listing::where('status', 'active')
                    ->whereIn('id', $ids)
                    ->get();
                // Preserve the original order of IDs
                $listings = $listings->sortBy(function ($listing) use ($ids) {
                    return array_search($listing->id, $ids);
                })->values();
            } catch (\Exception $e) {
                $listings = collect();
            }
        }

        return view('listings.favorites', ['listings' => $listings, 'redirect' => false]);
    }

    /**
     * Helper to serialize a listing into Yandex Map compatible JSON structure
     */
    protected function getApartmentsData($item)
    {
        $cat = 'apartments';
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
            $itemSlug = $item->slug;
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
            $itemSlug = \Illuminate\Support\Str::slug($transliterated);
        }

        $firstPhoto = null;
        $photos = [];
        if ($item->photos) {
            if (is_string($item->photos)) {
                $photos = json_decode($item->photos, true) ?: [];
            } elseif (is_array($item->photos)) {
                $photos = $item->photos;
            }
        }
        if (empty($photos) && $item->image) {
            $photos = [$item->image];
        }
        
        $isFakePhoto = false;
        $fakeBg = 'linear-gradient(140deg, #2C3E50, #3D5A6B)';
        $fakeEmoji = '🏢';
        
        if (!empty($photos) && is_array($photos)) {
            $firstPhoto = $photos[0];
            if (is_array($firstPhoto)) {
                $isFakePhoto = true;
                $fakeBg = $firstPhoto['bg'] ?? $fakeBg;
                $fakeEmoji = $firstPhoto['emoji'] ?? $fakeEmoji;
                $firstPhoto = null;
            } elseif (is_string($firstPhoto) && (strpos($firstPhoto, 'linear-gradient') !== false || strlen($firstPhoto) < 10)) {
                $isFakePhoto = true;
                $firstPhoto = null;
            }
        } else {
            $isFakePhoto = true;
        }
        
        $specsList = [];
        if ($item->area) {
            $specsList[] = $item->area . ' м²';
        }
        if ($item->type === 'cottage' || $item->type === 'agrotourism') {
            if ($item->rooms) {
                $specsList[] = $item->rooms . ' спален';
            }
        } else {
            if ($item->rooms) {
                $specsList[] = $item->rooms . '-комн.';
            }
        }
        if ($item->sleeping_places) {
            $specsList[] = $item->sleeping_places . ' сп. места';
        }
        $specsText = implode(' · ', $specsList);
        
        $itemLat = number_format($item->latitude ?: 53.900, 6, '.', '');
        $itemLng = number_format($item->longitude ?: 27.559, 6, '.', '');

        $displayAddress = $item->address;
        if (!empty($city) && str_starts_with($displayAddress, $city . ', ')) {
            $displayAddress = substr($displayAddress, strlen($city . ', '));
        }

        $phone = $item->phone ?? '';
        $whatsapp = $item->whatsapp ?? '';
        $viber = $item->viber ?? '';
        $telegram = $item->telegram ?? '';
        $nearestMetro = (!empty($item->nearest_metro) && $item->nearest_metro !== 'Нет' && $item->nearest_metro !== 'Метро не указано') ? $item->nearest_metro : '';
        $weekendPrice = $item->weekend_price ?? 0;
        $area = $item->area ?? 0;

        $bedroomsText = $bedrooms === 0 ? 'Студия' : $bedrooms . ' ' . ($bedrooms == 1 ? 'комната' : ($bedrooms <= 4 ? 'комнаты' : 'комнат'));
        $guestsText = $beds . ' ' . ($beds == 1 ? 'гость' : ($beds <= 4 ? 'гостя' : 'гостей'));

        // WhatsApp Link format
        $waLink = '';
        if (!empty($whatsapp)) {
            $waVal = trim($whatsapp);
            if (str_starts_with($waVal, 'http://') || str_starts_with($waVal, 'https://') || str_starts_with($waVal, 'wa.me/')) {
                $waLink = $waVal;
            } else {
                $cleanPhone = preg_replace('/[^0-9]/', '', $waVal);
                if (str_starts_with($cleanPhone, '80') && strlen($cleanPhone) == 11) {
                    $cleanPhone = '375' . substr($cleanPhone, 2);
                } elseif (strlen($cleanPhone) == 9) {
                    $cleanPhone = '375' . $cleanPhone;
                }
                $waLink = "https://wa.me/" . $cleanPhone;
            }
        }

        // Viber Link format
        $vbLink = '';
        if (!empty($viber)) {
            $vbVal = trim($viber);
            if (str_starts_with($vbVal, 'viber://') || str_starts_with($vbVal, 'http://') || str_starts_with($vbVal, 'https://')) {
                $vbLink = $vbVal;
            } else {
                $cleanPhone = preg_replace('/[^0-9]/', '', $vbVal);
                if (str_starts_with($cleanPhone, '80') && strlen($cleanPhone) == 11) {
                    $cleanPhone = '375' . substr($cleanPhone, 2);
                } elseif (strlen($cleanPhone) == 9) {
                    $cleanPhone = '375' . $cleanPhone;
                }
                $vbLink = "viber://chat?number=%2B" . $cleanPhone;
            }
        }

        // Telegram Link format
        $tgLink = \App\Helpers\ContactHelper::formatTelegramUrl($telegram);

        return [
            'id' => $item->id,
            'title' => $item->title,
            'address' => $displayAddress,
            'basePrice' => $item->price,
            'category' => $cat,
            'coords' => [(float)$itemLat, (float)$itemLng],
            'isAmber' => $cat === 'hourly',
            'slug' => $itemSlug,
            'firstPhoto' => $firstPhoto,
            'isFakePhoto' => $isFakePhoto,
            'fakeBg' => $fakeBg,
            'fakeEmoji' => $fakeEmoji,
            'specsText' => $specsText,
            'phone' => $phone,
            'whatsapp' => $waLink,
            'viber' => $vbLink,
            'telegram' => $tgLink,
            'nearestMetro' => $nearestMetro,
            'weekendPrice' => (float)$weekendPrice,
            'area' => (float)$area,
            'bedroomsText' => $bedroomsText,
            'guestsText' => $guestsText
        ];
    }

    /**
     * Unified response handler to support AJAX request rendering of catalog.
     */
    protected function renderCatalogView(Request $request, array $data)
    {
        if ($request->has('page') && !$request->ajax() && !$request->has('ajax')) {
            $page = (int)$request->query('page');
            if ($page > 0) {
                $path = $request->getPathInfo();
                $cleanPath = preg_replace('/\/page-\d+/i', '', $path);
                
                $targetPath = $cleanPath;
                if ($page > 1) {
                    $targetPath = rtrim($cleanPath, '/') . '/page-' . $page;
                }
                
                $query = $request->query();
                unset($query['page']);
                $queryString = http_build_query($query);
                
                return redirect($targetPath . ($queryString ? '?' . $queryString : ''), 301);
            }
        }

        $listings = $data['listings'] ?? collect();
        $currentPage = $data['currentPage'] ?? 1;

        // SEO check: if listings list is empty, set robots to noindex
        if ($listings->isEmpty()) {
            $data['metaRobots'] = 'noindex, nofollow';
        } else {
            $data['metaRobots'] = $data['metaRobots'] ?? 'index, follow';
        }

        if ($request->ajax() || $request->has('ajax')) {
            $apartmentsData = [];
            foreach ($listings as $item) {
                $apartmentsData[$item->id] = $this->getApartmentsData($item);
            }

            return response()->json([
                'html' => view('listings.partials.grid', [
                    'listings' => $listings,
                    'currentPage' => $currentPage
                ])->render(),
                'apartmentsData' => $apartmentsData,
                'seo' => [
                    'title' => $data['seoTitle'] ?? (isset($data['selectedCity']) && isset($data['inCity']) ? 'Flatly.by — Аренда квартир и коттеджей ' . $data['inCity'] . ' без посредников' : 'Flatly.by — Аренда квартир и коттеджей в Беларуси без посредников'),
                    'description' => $data['seoDescription'] ?? (isset($data['selectedCity']) && isset($data['inCity']) ? 'Снять квартиру посуточно или коттедж ' . $data['inCity'] . '. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.' : 'Снять квартиру посуточно или коттедж в Минске, Бресте, Гродно и других городах Беларуси. Прямые контакты хозяев, актуальные цены, аренда без посредников и комиссий.'),
                    'h1' => $data['seoH1'] ?? (isset($data['selectedCity']) && isset($data['inCity']) ? 'Аренда квартир и коттеджей ' . $data['inCity'] . ' посуточно' : 'Аренда квартир и коттеджей посуточно в Беларуси'),
                    'breadcrumbs' => isset($data['breadcrumbs']) ? view('listings.partials.breadcrumbs', ['breadcrumbs' => $data['breadcrumbs']])->render() : '',
                ],
                'selectedCity' => $data['selectedCity'] ?? null,
                'mapCenter' => $data['mapCenter'] ?? [53.9006, 27.5590],
                'canonicalUrl' => $data['canonicalUrl'] ?? $request->getRequestUri(),
            ]);
        }

        return view('listings.index', $data);
    }

    protected function applyRequestFilters($query, Request $request)
    {
        // 1. Filter by city
        if ($request->has('city')) {
            $city = trim($request->query('city'));
            if ($city !== '' && mb_strtolower($city) !== 'вся беларусь') {
                $query->where('city', $city);
            }
        }

        // 2. Filter by type
        if ($request->has('type')) {
            $type = $request->query('type');
            if ($type === 'apartments' || $type === 'apartment') {
                $query->where('type', '!=', 'cottage')->where(function($q) {
                    $q->whereNull('weekend_price')->orWhere('weekend_price', '<=', 0);
                });
            } elseif ($type === 'cottages' || $type === 'cottage') {
                $query->where('type', 'cottage');
            } elseif ($type === 'hourly') {
                $query->where('weekend_price', '>', 0);
            }
        }

        // 3. Filter by rooms
        if ($request->has('rooms')) {
            $rooms = (int)$request->query('rooms');
            if ($rooms > 0) {
                if ($rooms === 4) {
                    $query->where('rooms', '>=', 4);
                } else {
                    $query->where('rooms', $rooms);
                }
            }
        }

        // 4. Filter by guests
        if ($request->has('guests')) {
            $guests = (int)$request->query('guests');
            if ($guests > 0) {
                $query->where('sleeping_places', '>=', $guests);
            }
        }

        // 5. Filter by price
        if ($request->has('price_min')) {
            $priceMin = (float)$request->query('price_min');
            if ($priceMin > 0) {
                $query->where('price', '>=', $priceMin);
            }
        }
        if ($request->has('price_max')) {
            $priceMax = (float)$request->query('price_max');
            if ($priceMax > 0) {
                $query->where('price', '<=', $priceMax);
            }
        }

        return $query;
    }
}

