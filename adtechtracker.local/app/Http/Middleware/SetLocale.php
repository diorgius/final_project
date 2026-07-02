<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

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
        if (auth()->check() && $request->user()?->locale) {
            App::setLocale(auth()->user()->locale);
        } elseif (session()->has('locale')) {
            App::setLocale(session('locale'));
        } else {
            App::setLocale($request->getPreferredLanguage(array_keys(config('app.available_locales'))) ?? config('app.locale'));
        }
        return $next($request);
    }
}
