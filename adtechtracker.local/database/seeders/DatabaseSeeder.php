<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Offer;
use App\Models\OfferTheme;
use App\Models\Commission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // создаем пользователей
        $userSeeder = new UserSeeder();
        $userSeeder->run();

        // создаем темы
        OfferTheme::create(['name' => 'IT']);
        OfferTheme::create(['name' => 'Образование']);
        OfferTheme::create(['name' => 'Туризм']);
        OfferTheme::create(['name' => 'Путешествия']);
        OfferTheme::create(['name' => 'Спорт']);
        OfferTheme::create(['name' => 'Игры']);
        OfferTheme::create(['name' => 'Кино']);
        OfferTheme::create(['name' => 'Музыка']);

        // создаем офферы
        $offerSeeder = new OfferSeeder();
        $offerSeeder->run();

        // создаем комиссию
        Commission::create(['commission' => 20]);
    }
}
