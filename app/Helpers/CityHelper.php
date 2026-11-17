<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class CityHelper
{
    protected static $cityData = [
        'Барановичи' => ['slug' => 'baranovichi', 'in' => 'в Барановичах', 'coords' => [53.1325, 26.0142]],
        'Белоозёрск' => ['slug' => 'beloozersk', 'in' => 'в Белоозёрске', 'coords' => [52.4685, 25.1747]],
        'Белыничи' => ['slug' => 'belynychi', 'in' => 'в Белыничах', 'coords' => [53.9984, 29.7042]],
        'Береза' => ['slug' => 'bereza', 'in' => 'в Березе', 'coords' => [52.5323, 24.9785]],
        'Берёза' => ['slug' => 'bereza', 'in' => 'в Берёзе', 'coords' => [52.5323, 24.9785]],
        'Бобруйск' => ['slug' => 'bobruysk', 'in' => 'в Бобруйске', 'coords' => [53.1384, 29.2214]],
        'Боровляны' => ['slug' => 'borovlyany', 'in' => 'в Боровлянах', 'coords' => [54.0019, 27.6828]],
        'Борисов' => ['slug' => 'borisov', 'in' => 'в Борисове', 'coords' => [54.2279, 28.5034]],
        'Брагин' => ['slug' => 'bragin', 'in' => 'в Брагине', 'coords' => [51.7875, 30.2728]],
        'Браслав' => ['slug' => 'braslav', 'in' => 'в Браславе', 'coords' => [55.6389, 27.0317]],
        'Брест' => ['slug' => 'brest', 'in' => 'в Бресте', 'coords' => [52.0976, 23.7341]],
        'Брестская область' => ['slug' => 'brestskaya-oblast', 'in' => 'в Брестской области', 'coords' => [52.0976, 23.7341]],
        'Верхнедвинск' => ['slug' => 'verkhnedvinsk', 'in' => 'в Верхнедвинске', 'coords' => [55.7767, 27.9358]],
        'Вилейка' => ['slug' => 'vileyka', 'in' => 'в Вилейке', 'coords' => [54.4883, 26.9126]],
        'Витебск' => ['slug' => 'vitebsk', 'in' => 'в Витебске', 'coords' => [55.1872, 30.2051]],
        'Витебская область' => ['slug' => 'vitebskaya-oblast', 'in' => 'в Витебской области', 'coords' => [55.1872, 30.2051]],
        'Волковыск' => ['slug' => 'volkovysk', 'in' => 'в Волковыске', 'coords' => [53.1539, 24.4533]],
        'Воложин' => ['slug' => 'volozhin', 'in' => 'в Воложине', 'coords' => [54.0841, 26.5273]],
        'Ганцевичи' => ['slug' => 'gantsevichi', 'in' => 'в Ганцевичах', 'coords' => [52.7578, 26.4317]],
        'Глубокое' => ['slug' => 'glubokoe', 'in' => 'в Глубоком', 'coords' => [55.1384, 27.6836]],
        'Гомель' => ['slug' => 'gomel', 'in' => 'в Гомеле', 'coords' => [52.4345, 30.9754]],
        'Гомельская область' => ['slug' => 'gomelskaya-oblast', 'in' => 'в Гомельской области', 'coords' => [52.4345, 30.9754]],
        'Горки' => ['slug' => 'gorki', 'in' => 'в Горках', 'coords' => [54.2858, 30.9859]],
        'Городок' => ['slug' => 'gorodok', 'in' => 'в Городке', 'coords' => [55.4623, 29.9829]],
        'Гродно' => ['slug' => 'grodno', 'in' => 'в Гродно', 'coords' => [53.6693, 23.8131]],
        'Гродненская область' => ['slug' => 'grodnenskaya-oblast', 'in' => 'в Гродненской области', 'coords' => [53.6693, 23.8131]],
        'Дзержинск' => ['slug' => 'dzerzhinsk', 'in' => 'в Дзержинске', 'coords' => [53.6833, 27.1333]],
        'Добруш' => ['slug' => 'dobrush', 'in' => 'в Добруше', 'coords' => [52.4131, 31.3023]],
        'Докшицы' => ['slug' => 'dokshitsy', 'in' => 'в Докшицах', 'coords' => [54.8929, 27.8105]],
        'Дрогичин' => ['slug' => 'drogichin', 'in' => 'в Дрогичине', 'coords' => [52.1906, 25.1485]],
        'Дружный' => ['slug' => 'druzhny', 'in' => 'в Дружном', 'coords' => [53.6267, 27.8428]],
        'Дятлово' => ['slug' => 'dyatlovo', 'in' => 'в Дятлово', 'coords' => [53.4646, 25.4054]],
        'Ельск' => ['slug' => 'yelsk', 'in' => 'в Ельске', 'coords' => [51.8103, 29.1554]],
        'Жабинка' => ['slug' => 'zhabinka', 'in' => 'в Жабинке', 'coords' => [52.1965, 24.0195]],
        'Житковичи' => ['slug' => 'zhitkovichi', 'in' => 'в Житковичах', 'coords' => [52.2173, 27.8553]],
        'Жлобин' => ['slug' => 'zhlobin', 'in' => 'в Жлобине', 'coords' => [52.8926, 30.0434]],
        'Жодино' => ['slug' => 'zhodino', 'in' => 'в Жодино', 'coords' => [54.0931, 28.3328]],
        'Заславль' => ['slug' => 'zaslavl', 'in' => 'в Заславле', 'coords' => [54.0084, 27.2882]],
        'Зельва' => ['slug' => 'zelva', 'in' => 'в Зельве', 'coords' => [53.1492, 24.8115]],
        'Иваново' => ['slug' => 'ivanovo', 'in' => 'в Иваново', 'coords' => [52.1436, 25.5364]],
        'Ивацевичи' => ['slug' => 'ivatsevichi', 'in' => 'в Ивацевичах', 'coords' => [52.7144, 25.3392]],
        'Ивье' => ['slug' => 'ivye', 'in' => 'в Ивье', 'coords' => [53.9317, 25.7712]],
        'Калинковичи' => ['slug' => 'kalinkovichi', 'in' => 'в Калинковичах', 'coords' => [52.0963, 29.3409]],
        'Каменец' => ['slug' => 'kamenets', 'in' => 'в Каменце', 'coords' => [52.4042, 23.8214]],
        'Клецк' => ['slug' => 'kletsk', 'in' => 'в Клецке', 'coords' => [53.0645, 26.6348]],
        'Климовичи' => ['slug' => 'klimovichi', 'in' => 'в Климовичах', 'coords' => [53.6136, 32.0838]],
        'Кобрин' => ['slug' => 'kobrin', 'in' => 'в Кобрине', 'coords' => [52.2138, 24.3572]],
        'Корма' => ['slug' => 'korma', 'in' => 'в Корме', 'coords' => [53.1256, 30.8033]],
        'Костюковичи' => ['slug' => 'kostyukovichi', 'in' => 'в Костюковичах', 'coords' => [53.3364, 32.0622]],
        'Кричев' => ['slug' => 'krichev', 'in' => 'в Кричеве', 'coords' => [53.7194, 31.7139]],
        'Лельчицы' => ['slug' => 'lelchitsy', 'in' => 'в Лельчицах', 'coords' => [51.7828, 28.3242]],
        'Лепель' => ['slug' => 'lepel', 'in' => 'в Лепеле', 'coords' => [54.8858, 28.6948]],
        'Лида' => ['slug' => 'lida', 'in' => 'в Лиде', 'coords' => [53.8913, 25.3022]],
        'Логойск' => ['slug' => 'logoysk', 'in' => 'в Логойске', 'coords' => [54.2058, 27.8458]],
        'Лунинец' => ['slug' => 'luninets', 'in' => 'в Лунинце', 'coords' => [52.2476, 26.8041]],
        'Любань' => ['slug' => 'lyuban', 'in' => 'в Любани', 'coords' => [52.7985, 27.9995]],
        'Ляховичи' => ['slug' => 'lyakhovichi', 'in' => 'в Ляховичах', 'coords' => [53.0378, 26.2654]],
        'Марьина Горка' => ['slug' => 'maryina-gorka', 'in' => 'в Марьиной Горке', 'coords' => [53.5064, 28.1528]],
        'Мачулищи' => ['slug' => 'machulishchi', 'in' => 'в Мачулищах', 'coords' => [53.7823, 27.5804]],
        'Минск' => ['slug' => 'minsk', 'in' => 'в Минске', 'coords' => [53.9006, 27.5590]],
        'Минская область' => ['slug' => 'minskaya-oblast', 'in' => 'в Минской области', 'coords' => [53.9006, 27.5590]],
        'Могилёв' => ['slug' => 'mogilev', 'in' => 'в Могилёве', 'coords' => [53.8981, 30.3325]],
        'Могилев' => ['slug' => 'mogilev', 'in' => 'в Могилёве', 'coords' => [53.8981, 30.3325]],
        'Могилевская область' => ['slug' => 'mogilevskaya-oblast', 'in' => 'в Могилевской области', 'coords' => [53.8981, 30.3325]],
        'Могилёвская область' => ['slug' => 'mogilevskaya-oblast', 'in' => 'в Могилёвской области', 'coords' => [53.8981, 30.3325]],
        'Мозырь' => ['slug' => 'mozyr', 'in' => 'в Мозыре', 'coords' => [52.0495, 29.2456]],
        'Миоры' => ['slug' => 'miory', 'in' => 'в Миорах', 'coords' => [55.6222, 27.6258]],
        'Молодечно' => ['slug' => 'molodechno', 'in' => 'в Молодечно', 'coords' => [54.3121, 26.8524]],
        'Мосты' => ['slug' => 'mosty', 'in' => 'в Мостах', 'coords' => [53.4116, 24.5381]],
        'Мстиславль' => ['slug' => 'mstislavl', 'in' => 'в Мстиславле', 'coords' => [54.0213, 31.7303]],
        'Мядель' => ['slug' => 'myadel', 'in' => 'в Мяделе', 'coords' => [54.8728, 26.9383]],
        'Нарочь' => ['slug' => 'naroch', 'in' => 'в Нарочи', 'coords' => [54.9126, 26.7029]],
        'Несвиж' => ['slug' => 'nesvizh', 'in' => 'в Несвиже', 'coords' => [53.2185, 26.6781]],
        'Новогрудок' => ['slug' => 'novogrudok', 'in' => 'в Новогрудке', 'coords' => [53.5936, 25.8242]],
        'Новолукомль' => ['slug' => 'novolukoml', 'in' => 'в Новолукомле', 'coords' => [54.6542, 29.1384]],
        'Новополоцк' => ['slug' => 'novopolotsk', 'in' => 'в Новополоцке', 'coords' => [55.5292, 28.6608]],
        'Орша' => ['slug' => 'orsha', 'in' => 'в Орше', 'coords' => [54.5153, 30.4074]],
        'Осиповичи' => ['slug' => 'osipovichi', 'in' => 'в Осиповичах', 'coords' => [53.2929, 28.6436]],
        'Островец' => ['slug' => 'ostrovets', 'in' => 'в Островце', 'coords' => [54.6139, 25.9554]],
        'Ошмяны' => ['slug' => 'oshmyany', 'in' => 'в Ошмянах', 'coords' => [54.4239, 25.9382]],
        'Петриков' => ['slug' => 'petrikov', 'in' => 'в Петрикове', 'coords' => [52.1283, 28.5026]],
        'Пинск' => ['slug' => 'pinsk', 'in' => 'в Пинске', 'coords' => [52.1157, 26.1029]],
        'Полоцк' => ['slug' => 'polotsk', 'in' => 'в Полоцке', 'coords' => [55.4854, 28.7681]],
        'Поставы' => ['slug' => 'postavy', 'in' => 'в Поставах', 'coords' => [55.1139, 26.8398]],
        'Пружаны' => ['slug' => 'pruzhany', 'in' => 'в Пружанах', 'coords' => [52.5564, 24.4582]],
        'Речица' => ['slug' => 'rechitsa', 'in' => 'в Речице', 'coords' => [52.3639, 30.3951]],
        'Рогачев' => ['slug' => 'rogachev', 'in' => 'в Рогачеве', 'coords' => [53.0928, 30.0489]],
        'Рогачёв' => ['slug' => 'rogachev', 'in' => 'в Рогачёве', 'coords' => [53.0928, 30.0489]],
        'Светлогорск' => ['slug' => 'svetlogorsk', 'in' => 'в Светлогорске', 'coords' => [52.6333, 29.7333]],
        'Свислочь' => ['slug' => 'svisloch', 'in' => 'в Свислочи', 'coords' => [53.0333, 24.0954]],
        'Сенно' => ['slug' => 'senno', 'in' => 'в Сенно', 'coords' => [54.8094, 29.7042]],
        'Слоним' => ['slug' => 'slonim', 'in' => 'в Слониме', 'coords' => [53.0939, 25.3194]],
        'Слуцк' => ['slug' => 'slutsk', 'in' => 'в Слуцке', 'coords' => [53.0306, 27.5492]],
        'Смолевичи' => ['slug' => 'smolevichi', 'in' => 'в Смолевичах', 'coords' => [54.0298, 28.0882]],
        'Сморгонь' => ['slug' => 'smorgon', 'in' => 'в Сморгони', 'coords' => [54.4801, 26.4001]],
        'Солигорск' => ['slug' => 'soligorsk', 'in' => 'в Солигорске', 'coords' => [52.7876, 27.5415]],
        'Старые Дороги' => ['slug' => 'starye-dorogi', 'in' => 'в Старых Дорогах', 'coords' => [53.0116, 28.2636]],
        'Столбцы' => ['slug' => 'stolbtsy', 'in' => 'в Столбцах', 'coords' => [53.4833, 26.7333]],
        'Столин' => ['slug' => 'stolin', 'in' => 'в Столине', 'coords' => [51.8885, 26.8485]],
        'Узда' => ['slug' => 'uzda', 'in' => 'в Узде', 'coords' => [53.4639, 27.2144]],
        'Фаниполь' => ['slug' => 'fanipol', 'in' => 'в Фаниполе', 'coords' => [53.7548, 27.3322]],
        'Хатежино' => ['slug' => 'khatezhino', 'in' => 'в Хатежино', 'coords' => [53.9056, 27.2917]],
        'Хойники' => ['slug' => 'khoyniki', 'in' => 'в Хойниках', 'coords' => [51.8906, 29.9554]],
        'Хотимск' => ['slug' => 'khotimsk', 'in' => 'в Хотимске', 'coords' => [53.4072, 32.5853]],
        'Чаусы' => ['slug' => 'chausy', 'in' => 'в Чаусах', 'coords' => [53.8078, 31.0425]],
        'Чечерск' => ['slug' => 'chechersk', 'in' => 'в Чечерске', 'coords' => [52.9238, 30.9167]],
        'Шклов' => ['slug' => 'shklov', 'in' => 'в Шклове', 'coords' => [54.2158, 30.2982]],
        'Щучин' => ['slug' => 'shchuchin', 'in' => 'в Щучине', 'coords' => [53.6019, 24.7431]],
    ];

    public static function slugify(string $city): string
    {
        $city = trim($city);
        if (isset(self::$cityData[$city])) {
            return self::$cityData[$city]['slug'];
        }

        $rus = [
            'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
            'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я'
        ];
        $lat = [
            'A', 'B', 'V', 'G', 'D', 'E', 'E', 'Zh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'Ts', 'Ch', 'Sh', 'Sch', '', 'Y', '', 'E', 'Yu', 'Ya',
            'a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ya'
        ];
        $transliterated = str_replace($rus, $lat, $city);
        return Str::slug($transliterated);
    }

    public static function getCityNameBySlug(string $slug): ?string
    {
        $slug = strtolower(trim($slug));
        foreach (self::$cityData as $name => $data) {
            if ($data['slug'] === $slug) {
                return $name;
            }
        }

        // Fallback: check unique cities in the database
        try {
            $dbCities = \App\Models\Listing::where('status', 'active')
                ->whereNotNull('city')
                ->where('city', '!=', '')
                ->select('city')
                ->distinct()
                ->pluck('city');
                
            foreach ($dbCities as $dbCity) {
                if (self::slugify($dbCity) === $slug) {
                    return $dbCity;
                }
            }
        } catch (\Exception $e) {
            // Database fallback error
        }

        return null;
    }


    public static function getInCityName(string $city): string
    {
        $city = trim($city);
        if (isset(self::$cityData[$city])) {
            return self::$cityData[$city]['in'];
        }

        // Simple fallback inflection rules for Russian cities
        $lastChar = mb_substr($city, -1);
        if (in_array($lastChar, ['к', 'в', 'м', 'р', 'л', 'н', 'т', 'д', 'г', 'з', 'с', 'п', 'б', 'ф'])) {
            return 'в ' . $city . 'е';
        }
        if (in_array($lastChar, ['а', 'я'])) {
            return 'в ' . mb_substr($city, 0, -1) . 'е';
        }
        return 'в ' . $city;
    }

    public static function getCityCoordinates(string $city, $listings = null): array
    {
        $city = trim($city);
        if (isset(self::$cityData[$city]) && isset(self::$cityData[$city]['coords'])) {
            return self::$cityData[$city]['coords'];
        }

        // Try to average coordinates of active listings in this city
        if ($listings && count($listings) > 0) {
            $latSum = 0;
            $lngSum = 0;
            $count = 0;
            foreach ($listings as $l) {
                if ($l->latitude && $l->longitude) {
                    $latSum += $l->latitude;
                    $lngSum += $l->longitude;
                    $count++;
                }
            }
            if ($count > 0) {
                return [$latSum / $count, $lngSum / $count];
            }
        }

        // Default to Minsk center fallback
        return [53.9006, 27.5590];
    }

    public static function getAllSlugsMap(): array
    {
        $map = [];
        foreach (self::$cityData as $name => $data) {
            $map[$name] = $data['slug'];
        }
        return $map;
    }
}
