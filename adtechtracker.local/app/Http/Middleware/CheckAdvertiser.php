<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Services\SecurityLogger;

/**
 * Summary of CheckAdvertiser
 */
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

            // пишем в лог попытку входа на запрещенную страницу
            SecurityLogger::attemptToLoginForbiddenPage(Auth::user(), $request);
                    
            abort(403, __('http-statuses.403'));
        }

        return $next($request);
    }
}
