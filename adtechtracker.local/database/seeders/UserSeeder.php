<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    // пароль по умолчанию для пользователей
    protected const PASSWORD = '12345678';

    /**
     * создаем пользователей
     */
    public function run(): void
    {

        User::create([
            'name' => 'admin',
            'email' => 'admin@example.ru',
            'email_verified_at' => now(),
            'password' => Hash::make(self::PASSWORD),
            'role' => 'admin',
            'status' => true,
        ]);
        for ($i = 1; $i < 4; ++$i) {
            User::create([
                'name' => "advertiser_$i",
                'email' => "advertiser_$i@example.ru",
                'email_verified_at' => now(),
                'password' => Hash::make(self::PASSWORD),
                'role' => 'advertiser',
                'status' => true,
            ]);
            User::create([
                'name' => "webmaster_$i",
                'email' => "webmaster_$i@example.ru",
                'email_verified_at' => now(),
                'password' => Hash::make(self::PASSWORD),
                'role' => 'webmaster',
                'status' => true,
            ]);
        }
    }
}
