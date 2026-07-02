<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Offer;

class OfferSeeder extends Seeder
{
    /**
     * Создаем офферы
     */
    public function run(): void
    {
            Offer::create([
            'name' => 'Skillfactory',
            'url' => 'https://skillfactory.ru/',
            'theme_id' => 2,
            'advertiser_id' => 2,
            'price' => 100,
            'status' => false,
        ]);
        Offer::create([
            'name' => 'Habr',
            'url' => 'https://habr.com/ru/feed/',
            'theme_id' => 1,
            'advertiser_id' => 2,
            'price' => 100,
            'status' => false,
        ]);
        Offer::create([
            'name' => 'Клуб приключений',
            'url' => 'https://www.vpoxod.ru/',
            'theme_id' => 3,
            'advertiser_id' => 4,
            'price' => 100,
            'status' => false,
        ]);
        Offer::create([
            'name' => 'Яндекс путешествия',
            'url' => 'https://travel.yandex.ru/',
            'theme_id' => 4,
            'advertiser_id' => 4,
            'price' => 100,
            'status' => false,
        ]);
        Offer::create([
            'name' => 'Спортс',
            'url' => 'https://www.sports.ru/',
            'theme_id' => 5,
            'advertiser_id' => 6,
            'price' => 100,
            'status' => false,
        ]);
        Offer::create([
            'name' => 'Кинопоиск',
            'url' => 'https://www.kinopoisk.ru/',
            'theme_id' => 7,
            'advertiser_id' => 6,
            'price' => 100,
            'status' => false,
        ]);
    }
}
