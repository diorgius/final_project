<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class CheckAuthTest extends TestCase
{
    public function test_guest_is_redirected_to_login(): void
    {
        // переход по маршруту неавторизованного пользователя
        $response = $this->get(route('admin.main'));

        // редирект на страницу логина
        $response->assertRedirect(route('login'));
    }
}