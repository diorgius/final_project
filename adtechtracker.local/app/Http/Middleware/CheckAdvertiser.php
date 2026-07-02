<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class CheckAdvertiser
{
    /**
     * Проверка уровня доступа рекламодателя
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()?->role !== 'advertiser') {
            abort(403, __('http-statuses.403'));
        }

        return $next($request);
    }
}
