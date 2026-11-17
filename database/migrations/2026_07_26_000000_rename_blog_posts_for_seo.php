<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $mappings = [
            'top-5-rayonov-minska-dlya-posutochnoy-arendy' => [
                'title' => 'В каком районе Минска лучше снять квартиру?',
                'slug' => 'v-kakom-rajone-minska-luchshe-snyat-kvartiru'
            ],
            'kak-vybrat-kvartiru-na-sutki-s-zhivotnymi-v-minske' => [
                'title' => 'Где снять квартиру с животными в Минске?',
                'slug' => 'gde-snyat-kvartiru-s-zhivotnymi-v-minske'
            ],
            'kak-proverit-kvartiru-pered-zaseleniem-chek-list' => [
                'title' => 'На что обратить внимание при осмотре квартиры?',
                'slug' => 'na-chto-obratit-vnimanie-pri-osmotre-kvartiry'
            ],
            'chto-delat-esli-v-kvartire-slomalas-tehnika' => [
                'title' => 'Что делать, если в квартире сломалась техника?',
                'slug' => 'chto-delat-esli-v-kvartire-slomalas-tehnika'
            ],
            'kak-avtomatizirovat-zaselenie-i-vyselenie-v-posutochnoy-arende' => [
                'title' => 'Как автоматизировать заселение в посуточной аренде?',
                'slug' => 'kak-avtomatizirovat-zaselenie-v-posutochnoy-arende'
            ],
            'posutochnaya-arenda-protiv-oteley-sravnenie' => [
                'title' => 'Что дешевле: квартира или отель в Минске?',
                'slug' => 'chto-deshevle-kvartira-ili-otel-v-minske'
            ],
            'kak-podgotovit-kvartiru-k-posutochnoy-sdache' => [
                'title' => 'Как подготовить квартиру к посуточной аренде?',
                'slug' => 'kak-podgotovit-kvartiru-k-posutochnoy-arende'
            ],
            'top-5-oshibok-arendodateley-s-gostyami' => [
                'title' => 'Какие ошибки допускают владельцы посуточной аренды?',
                'slug' => 'kakie-oshibki-dopuskayut-vladelcy-posutochnoy-arendy'
            ],
            'pravila-zaseleniya-i-vyseleniya-posutochno' => [
                'title' => 'Какие правила заселения при посуточной аренде?',
                'slug' => 'kakie-pravila-zaseleniya-pri-posutochnoy-arende'
            ]
        ];

        foreach ($mappings as $oldSlug => $data) {
            DB::table('blog_posts')
                ->where('slug', $oldSlug)
                ->update([
                    'title' => $data['title'],
                    'slug' => $data['slug']
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $mappings = [
            'v-kakom-rajone-minska-luchshe-snyat-kvartiru' => [
                'title' => 'Топ-5 районов Минска для посуточной аренды: где остановиться туристу и где заработать владельцу',
                'slug' => 'top-5-rayonov-minska-dlya-posutochnoy-arendy'
            ],
            'gde-snyat-kvartiru-s-zhivotnymi-v-minske' => [
                'title' => 'Как выбрать квартиру на сутки с животными в Минске: советы для путешественников с питомцами',
                'slug' => 'kak-vybrat-kvartiru-na-sutki-s-zhivotnymi-v-minske'
            ],
            'na-chto-obratit-vnimanie-pri-osmotre-kvartiry' => [
                'title' => 'Как проверить квартиру перед заселением: чек-лист для туриста',
                'slug' => 'kak-proverit-kvartiru-pered-zaseleniem-chek-list'
            ],
            'chto-delat-esli-v-kvartire-slomalas-tehnika' => [
                'title' => 'Что делать, если в квартире сломалась техника: инструкция для гостей и хозяев',
                'slug' => 'chto-delat-esli-v-kvartire-slomalas-tehnika'
            ],
            'kak-avtomatizirovat-zaselenie-v-posutochnoy-arende' => [
                'title' => 'Как автоматизировать заселение и выселение в посуточной аренде',
                'slug' => 'kak-avtomatizirovat-zaselenie-i-vyselenie-v-posutochnoy-arende'
            ],
            'chto-deshevle-kvartira-ili-otel-v-minske' => [
                'title' => 'Посуточная аренда против отелей: что выгоднее для путешественников?',
                'slug' => 'posutochnaya-arenda-protiv-oteley-sravnenie'
            ],
            'kak-podgotovit-kvartiru-k-posutochnoy-arende' => [
                'title' => 'Как подготовить квартиру к посуточной сдаче: чеклист для хозяев',
                'slug' => 'kak-podgotovit-kvartiru-k-posutochnoy-sdache'
            ],
            'kakie-oshibki-dopuskayut-vladelcy-posutochnoy-arendy' => [
                'title' => 'Топ-5 ошибок арендодателей при работе с гостями',
                'slug' => 'top-5-oshibok-arendodateley-s-gostyami'
            ],
            'kakie-pravila-zaseleniya-pri-posutochnoy-arende' => [
                'title' => 'Правила заселения и выселения при посуточной аренде жилья',
                'slug' => 'pravila-zaseleniya-i-vyseleniya-posutochno'
            ]
        ];

        foreach ($mappings as $newSlug => $data) {
            DB::table('blog_posts')
                ->where('slug', $newSlug)
                ->update([
                    'title' => $data['title'],
                    'slug' => $data['slug']
                ]);
        }
    }
};
