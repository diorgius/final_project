<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

/**
 * Summary of SetLocale
 */
class SetLocale
{
    /**
     * Устанавливаем язык в зависимости от настроек пользователя
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // если пользователь авторизовался, то берем язык из таблицы users
        if (auth()->check() && $request->user()?->locale) {
            App::setLocale(auth()->user()->locale);

        // если нет, то из сессии
        } elseif (session()->has('locale')) {
            App::setLocale(session('locale'));

        // если нет, то из локали браузера, если и там нет, то из кофига
        } else {
            App::setLocale($request->getPreferredLanguage(array_keys(config('app.available_locales'))) ?? config('app.locale'));
        }

        return $next($request);
    }
}
